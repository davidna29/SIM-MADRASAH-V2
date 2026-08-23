<div class="flex items-center gap-1.5">
    <x-ui.button variant="secondary" size="sm" icon="arrow-up-right" href="{{ route('kelas.show', $classGroup) }}">Kelola</x-ui.button>
    <form method="POST" action="{{ route('kelas.destroy', $classGroup) }}" onsubmit="return confirm('Hapus kelas {{ $classGroup->name }}?');">
        @csrf
        @method('DELETE')
        <x-ui.button type="submit" size="sm" variant="ghost" icon="trash">Hapus</x-ui.button>
    </form>
</div>
