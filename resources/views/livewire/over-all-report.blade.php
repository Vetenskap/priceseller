<div class="absolute top-1/4 left-2">
    <flux:dropdown>
        <flux:button>Поставщики (отчёты)</flux:button>

        <flux:menu class="h-1/3 overflow-y-scroll">
            @foreach($reports as $report)
                <flux:menu.item
                    class="{{$report['report'] ? ($report['report']->status === 0 ? 'bg-green-300' : ($report['report']->status === 2 ? 'bg-yellow-300' : 'bg-red-300')) : ''}}"
                    :href="$report['report'] ? route('supplier.report.edit', ['supplier' => $report['supplier']->id, 'report' => $report['report']->id]) : ''"
                    :icon="match($report['report']?->status) {
                    0 => 'check-badge',
                    1 => 'exclamation-triangle',
                    default => 'exclamation-circle',
                }"
                >
                    {{$report['supplier']->name}} [{{$report['report']?->updated_at}} {{$report['report']?->message}}]
                </flux:menu.item>
            @endforeach
            <flux:menu.separator />
        </flux:menu>
    </flux:dropdown>
</div>
