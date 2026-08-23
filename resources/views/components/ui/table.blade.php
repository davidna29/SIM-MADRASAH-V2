@props([
    'headers' => [],
    'footer' => null,
    'empty' => true,
    'emptySlot' => null,
])

<div {{ $attributes->merge(['class' => 'overflow-x-auto']) }}>
    <table class="w-full min-w-[640px] border-collapse text-sm">
        @if (!empty($headers))
            <thead>
                <tr class="border-b border-rule-strong">
                    @foreach ($headers as $i => $header)
                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft {{ $loop->last ? 'text-right' : '' }}">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
        @endif
        <tbody class="divide-y divide-rule/70">
            @if ($empty)
                <tr>
                    <td colspan="{{ count($headers) }}">
                        {{ $emptySlot ?? 'Tidak ada data.' }}
                    </td>
                </tr>
            @else
                {{ $slot }}
            @endif
        </tbody>
        @if ($footer)
            <tfoot>
                <tr class="border-t border-rule-strong">
                    {{ $footer }}
                </tr>
            </tfoot>
        @endif
    </table>
</div>
