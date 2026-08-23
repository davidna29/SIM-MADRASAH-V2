<div class="flex items-center gap-1.5">
    <x-ui.button variant="secondary" size="sm" icon="arrow-up-right" href="{{ route('jadwal.model.show', $model) }}">Detail</x-ui.button>
    <x-ui.button size="sm" variant="ghost" icon="pencil-square" href="{{ route('jadwal.model.edit', $model) }}">Ubah</x-ui.button>
    <form method="POST" action="{{ route('jadwal.model.destroy', $model) }}" onsubmit="return confirm('Hapus model jadwal ini?');">
        @csrf
        @method('DELETE')
        <x-ui.button type="submit" size="sm" variant="ghost" icon="trash">Hapus</x-ui.button>
    </form>
</div>
