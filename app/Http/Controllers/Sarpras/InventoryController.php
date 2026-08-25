<?php

namespace App\Http\Controllers\Sarpras;

use App\Http\Controllers\Controller;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryMaintenance;
use App\Models\InventoryMutation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', InventoryItem::class);

        $items = InventoryItem::with('category')
            ->when(request('category_id'), fn ($q, $v) => $q->where('category_id', $v))
            ->when(request('condition'), fn ($q, $v) => $q->where('condition', $v))
            ->when(request('status'), fn ($q, $v) => $q->where('status', $v))
            ->when(request('q'), fn ($q, $s) => $q->where(function ($w) use ($s) {
                $w->where('name', 'like', "%{$s}%")
                    ->orWhere('code', 'like', "%{$s}%")
                    ->orWhere('brand', 'like', "%{$s}%");
            }))
            ->orderBy('code')
            ->paginate(15)
            ->withQueryString();

        return view('pages.sarpras.inventaris.index', [
            'roleLabel' => 'Sarpras',
            'breadcrumb' => [
                ['label' => 'Sarpras', 'href' => route('dashboard')],
                ['label' => 'Inventaris Barang'],
            ],
            'items' => $items,
            'categories' => InventoryCategory::orderBy('name')->get(),
            'conditions' => InventoryItem::CONDITIONS,
            'statuses' => InventoryItem::STATUSES,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', InventoryItem::class);

        return view('pages.sarpras.inventaris.form', [
            'roleLabel' => 'Sarpras',
            'breadcrumb' => [
                ['label' => 'Sarpras', 'href' => route('dashboard')],
                ['label' => 'Inventaris Barang', 'href' => route('inventaris.index')],
                ['label' => 'Tambah Barang'],
            ],
            'editing' => false,
            'categories' => $this->categoryOptions(),
            'conditions' => InventoryItem::CONDITIONS,
            'statuses' => InventoryItem::STATUSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', InventoryItem::class);

        $validated = $request->validate($this->itemRules());

        $item = InventoryItem::create([
            ...$validated,
            'code' => $this->nextCode(),
            'created_by' => auth()->id(),
        ]);

        activity('sarpras')->performedOn($item)->log('barang_ditambah');

        return redirect()->route('inventaris.show', $item)->with('status', 'Barang inventaris ditambahkan.');
    }

    public function show(InventoryItem $item): View
    {
        $this->authorize('view', $item);

        $item->load('category', 'creator', 'mutations.approver', 'mutations.creator', 'maintenances.creator');

        return view('pages.sarpras.inventaris.show', [
            'roleLabel' => 'Sarpras',
            'breadcrumb' => [
                ['label' => 'Sarpras', 'href' => route('dashboard')],
                ['label' => 'Inventaris Barang', 'href' => route('inventaris.index')],
                ['label' => $item->name],
            ],
            'item' => $item,
            'canManage' => auth()->user()->can('update', $item),
            'canApprove' => auth()->user()->can('approve', InventoryItem::class),
        ]);
    }

    public function edit(InventoryItem $item): View
    {
        $this->authorize('update', $item);

        return view('pages.sarpras.inventaris.form', [
            'roleLabel' => 'Sarpras',
            'breadcrumb' => [
                ['label' => 'Sarpras', 'href' => route('dashboard')],
                ['label' => 'Inventaris Barang', 'href' => route('inventaris.index')],
                ['label' => 'Ubah '.$item->name],
            ],
            'editing' => true,
            'item' => $item,
            'categories' => $this->categoryOptions(),
            'conditions' => InventoryItem::CONDITIONS,
            'statuses' => InventoryItem::STATUSES,
        ]);
    }

    public function update(Request $request, InventoryItem $item): RedirectResponse
    {
        $this->authorize('update', $item);

        $validated = $request->validate($this->itemRules($item->id));

        $item->update($validated);

        activity('sarpras')->performedOn($item)->log('barang_diubah');

        return redirect()->route('inventaris.show', $item)->with('status', 'Barang inventaris diperbarui.');
    }

    public function destroy(InventoryItem $item): RedirectResponse
    {
        $this->authorize('delete', $item);

        $item->delete();

        activity('sarpras')->performedOn($item)->log('barang_dihapus');

        return redirect()->route('inventaris.index')->with('status', 'Barang inventaris dihapus.');
    }

    public function mutationStore(Request $request, InventoryItem $item): RedirectResponse
    {
        $this->authorize('update', $item);

        $validated = $request->validate([
            'to_location' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:1', 'max:'.$item->quantity],
            'mutation_date' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:200'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $mutation = InventoryMutation::create([
            ...$validated,
            'item_id' => $item->id,
            'from_location' => $item->location,
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        activity('sarpras')->performedOn($item)->log('barang_mutasi_diajukan');

        return back()->with('status', 'Mutasi diajukan — menunggu persetujuan.');
    }

    public function mutationDestroy(InventoryItem $item, InventoryMutation $mutation): RedirectResponse
    {
        $this->authorize('update', $item);

        if ($mutation->status !== 'pending') {
            return back()->withErrors(['mutasi' => 'Mutasi yang sudah diproses tidak dapat dihapus.']);
        }

        $mutation->delete();

        activity('sarpras')->performedOn($item)->log('barang_mutasi_dibatalkan');

        return back()->with('status', 'Pengajuan mutasi dibatalkan.');
    }

    public function mutationApprove(InventoryItem $item, InventoryMutation $mutation): RedirectResponse
    {
        $this->authorize('approve', InventoryItem::class);

        if ($mutation->status !== 'pending') {
            return back()->withErrors(['mutasi' => 'Mutasi sudah diproses.']);
        }

        DB::transaction(function () use ($item, $mutation) {
            $mutation->update([
                'status' => 'disetujui',
                'approved_by' => auth()->id(),
            ]);

            $item->update([
                'location' => $mutation->to_location,
                'status' => $item->status === 'dalam_perawatan' ? $item->status : 'tersedia',
            ]);
        });

        activity('sarpras')->performedOn($item)->log('barang_mutasi_disetujui');

        return back()->with('status', 'Mutasi disetujui — lokasi barang diperbarui.');
    }

    public function mutationReject(InventoryItem $item, InventoryMutation $mutation): RedirectResponse
    {
        $this->authorize('approve', InventoryItem::class);

        if ($mutation->status !== 'pending') {
            return back()->withErrors(['mutasi' => 'Mutasi sudah diproses.']);
        }

        $mutation->update([
            'status' => 'ditolak',
            'approved_by' => auth()->id(),
        ]);

        activity('sarpras')->performedOn($item)->log('barang_mutasi_ditolak');

        return back()->with('status', 'Pengajuan mutasi ditolak.');
    }

    public function maintenanceStore(Request $request, InventoryItem $item): RedirectResponse
    {
        $this->authorize('update', $item);

        $validated = $request->validate([
            'type' => ['required', Rule::in(InventoryMaintenance::TYPES)],
            'description' => ['required', 'string', 'max:2000'],
            'cost' => ['nullable', 'integer', 'min:0'],
            'start_date' => ['required', 'date'],
            'technician' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $maintenance = InventoryMaintenance::create([
            ...$validated,
            'item_id' => $item->id,
            'status' => 'berlangsung',
            'created_by' => auth()->id(),
        ]);

        $item->update(['status' => 'dalam_perawatan']);

        activity('sarpras')->performedOn($item)->log('barang_dipelihara');

        return back()->with('status', 'Pemeliharaan/perbaikan dicatat — barang ditandai dalam perawatan.');
    }

    public function maintenanceDone(InventoryItem $item, InventoryMaintenance $maintenance): RedirectResponse
    {
        $this->authorize('update', $item);

        $maintenance->update([
            'status' => 'selesai',
            'end_date' => $maintenance->end_date ?? now()->toDateString(),
        ]);

        // Barang kembali tersedia bila tidak ada pemeliharaan berlangsung lain
        if (! $item->maintenances()->where('status', 'berlangsung')->exists()) {
            $item->update(['status' => 'tersedia']);
        }

        activity('sarpras')->performedOn($item)->log('barang_perawatan_selesai');

        return back()->with('status', 'Pemeliharaan ditandai selesai.');
    }

    public function maintenanceDestroy(InventoryItem $item, InventoryMaintenance $maintenance): RedirectResponse
    {
        $this->authorize('update', $item);

        $maintenance->delete();

        if ($item->status === 'dalam_perawatan' && ! $item->maintenances()->where('status', 'berlangsung')->exists()) {
            $item->update(['status' => 'tersedia']);
        }

        activity('sarpras')->performedOn($item)->log('barang_perawatan_dihapus');

        return back()->with('status', 'Catatan pemeliharaan dihapus.');
    }

    public function categoryIndex(): View
    {
        $this->authorize('manageCategories', InventoryItem::class);

        $categories = InventoryCategory::withCount('items')->orderBy('name')->paginate(15);

        return view('pages.sarpras.inventaris.kategori', [
            'roleLabel' => 'Sarpras',
            'breadcrumb' => [
                ['label' => 'Sarpras', 'href' => route('dashboard')],
                ['label' => 'Inventaris Barang', 'href' => route('inventaris.index')],
                ['label' => 'Kategori'],
            ],
            'categories' => $categories,
        ]);
    }

    public function categoryStore(Request $request): RedirectResponse
    {
        $this->authorize('manageCategories', InventoryItem::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:60', 'unique:inventory_categories,name'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        InventoryCategory::create($validated);

        activity('sarpras')->log('kategori_barang_dibuat');

        return back()->with('status', 'Kategori barang ditambahkan.');
    }

    public function categoryUpdate(Request $request, InventoryCategory $category): RedirectResponse
    {
        $this->authorize('manageCategories', InventoryItem::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:60', Rule::unique('inventory_categories', 'name')->ignore($category->id)],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $category->update($validated);

        activity('sarpras')->performedOn($category)->log('kategori_barang_diubah');

        return back()->with('status', 'Kategori diperbarui.');
    }

    public function categoryDestroy(InventoryCategory $category): RedirectResponse
    {
        $this->authorize('manageCategories', InventoryItem::class);

        if ($category->items()->exists()) {
            return back()->withErrors(['kategori' => 'Kategori masih berisi barang — tidak dapat dihapus.']);
        }

        $category->delete();

        activity('sarpras')->performedOn($category)->log('kategori_barang_dihapus');

        return back()->with('status', 'Kategori dihapus.');
    }

    protected function itemRules(?int $ignoreId = null): array
    {
        return [
            'category_id' => ['required', 'exists:inventory_categories,id'],
            'name' => ['required', 'string', 'max:120'],
            'brand' => ['nullable', 'string', 'max:60'],
            'model' => ['nullable', 'string', 'max:60'],
            'serial_number' => ['nullable', 'string', 'max:60'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit' => ['nullable', 'string', 'max:20'],
            'condition' => ['required', Rule::in(InventoryItem::CONDITIONS)],
            'location' => ['nullable', 'string', 'max:100'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::in(InventoryItem::STATUSES)],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function categoryOptions(): array
    {
        return InventoryCategory::orderBy('name')->get()->pluck('name', 'id')->all();
    }

    protected function nextCode(): string
    {
        $prefix = 'INV-'.now()->format('Ym').'-';
        $last = InventoryItem::where('code', 'like', $prefix.'%')
            ->orderByDesc('code')
            ->value('code');

        $seq = $last ? ((int) substr($last, -3)) + 1 : 1;

        return $prefix.str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
    }
}
