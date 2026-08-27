<?php

namespace App\Http\Controllers\Sarpras;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Room::class);

        $type = request('type', 'ruangan');

        $rooms = Room::with('penanggJawab.person')
            ->where('type', $type)
            ->when(request('building'), fn ($q, $v) => $q->where('building', $v))
            ->when(request('condition'), fn ($q, $v) => $q->where('condition', $v))
            ->when(request('q'), fn ($q, $s) => $q->where(function ($w) use ($s) {
                $w->where('name', 'like', "%{$s}%")
                    ->orWhere('code', 'like', "%{$s}%")
                    ->orWhere('building', 'like', "%{$s}%");
            }))
            ->orderBy('code')
            ->paginate(15)
            ->withQueryString();

        $buildings = Room::where('type', $type)->whereNotNull('building')->distinct()->pluck('building');

        return view('pages.sarpras.ruangan.index', [
            'roleLabel' => 'Sarpras',
            'breadcrumb' => [
                ['label' => 'Sarpras', 'href' => route('dashboard')],
                ['label' => Room::TYPES[$type] ?? 'Ruangan'],
            ],
            'rooms' => $rooms,
            'type' => $type,
            'buildings' => $buildings,
            'conditions' => Room::CONDITIONS,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Room::class);

        return view('pages.sarpras.ruangan.form', [
            'roleLabel' => 'Sarpras',
            'breadcrumb' => [
                ['label' => 'Sarpras', 'href' => route('dashboard')],
                ['label' => 'Daftar Ruangan', 'href' => route('ruangan.index')],
                ['label' => 'Tambah Ruangan'],
            ],
            'editing' => false,
            'room' => null,
            'types' => Room::TYPES,
            'conditions' => Room::CONDITIONS,
            'employees' => Employee::with('person')->where('status', 'aktif')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Room::class);

        $validated = $request->validate($this->roomRules());

        $room = Room::create([
            ...$validated,
            'code' => Room::nextCode(),
            'created_by' => auth()->id(),
        ]);

        activity('sarpras')->performedOn($room)->log('ruangan_ditambah');

        return redirect()->route('ruangan.show', $room)->with('status', 'Ruangan berhasil ditambahkan.');
    }

    public function show(Room $room): View
    {
        $this->authorize('view', $room);

        return view('pages.sarpras.ruangan.show', [
            'roleLabel' => 'Sarpras',
            'breadcrumb' => [
                ['label' => 'Sarpras', 'href' => route('dashboard')],
                ['label' => Room::TYPES[$room->type] ?? 'Ruangan', 'href' => route('ruangan.index', ['type' => $room->type])],
                ['label' => $room->name],
            ],
            'room' => $room->load(['penanggJawab.person']),
        ]);
    }

    public function edit(Room $room): View
    {
        $this->authorize('update', $room);

        return view('pages.sarpras.ruangan.form', [
            'roleLabel' => 'Sarpras',
            'breadcrumb' => [
                ['label' => 'Sarpras', 'href' => route('dashboard')],
                ['label' => Room::TYPES[$room->type] ?? 'Ruangan', 'href' => route('ruangan.index', ['type' => $room->type])],
                ['label' => 'Ubah '.$room->name],
            ],
            'editing' => true,
            'room' => $room,
            'types' => Room::TYPES,
            'conditions' => Room::CONDITIONS,
            'employees' => Employee::with('person')->where('status', 'aktif')->orderBy('id')->get(),
        ]);
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $this->authorize('update', $room);

        $validated = $request->validate($this->roomRules($room->id));

        $room->update($validated);

        activity('sarpras')->performedOn($room)->log('ruangan_diubah');

        return redirect()->route('ruangan.show', $room)->with('status', 'Ruangan berhasil diperbarui.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        $this->authorize('delete', $room);

        $room->delete();

        activity('sarpras')->log('ruangan_dihapus');

        return redirect()->route('ruangan.index')->with('status', 'Ruangan berhasil dihapus.');
    }

    protected function roomRules(?int $ignoreId = null): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:'.implode(',', array_keys(Room::TYPES))],
            'building' => ['nullable', 'string', 'max:60'],
            'floor' => ['nullable', 'string', 'max:20'],
            'capacity' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'condition' => ['required', 'in:'.implode(',', array_keys(Room::CONDITIONS))],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
