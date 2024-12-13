<div class="absolute top-1/4 left-2">
    <flux:dropdown>
        <flux:button>Поставщики (отчёты)</flux:button>

        <flux:menu class="h-1/3 overflow-y-scroll">
            @foreach($reports as $report)
                <flux:menu.item
                    class="flex gap-6"
                    :href="$report['report'] ? route('supplier.report.edit', ['supplier' => $report['supplier']->id, 'report' => $report['report']->id]) : ''"
                >
                    <flux:badge color="{{$report['report'] ? ($report['report']->status === 0 ? 'lime' : ($report['report']->status === 2 ? 'yellow' : 'red')) : ''}}">
                        @if($report['report']?->status === 0)
                            <flux:icon.check-badge />
                        @elseif($report['report']?->status === 1)
                            <flux:icon.exclamation-triangle />
                        @else
                            <flux:icon.exclamation-circle />
                        @endif
                    </flux:badge>
                    {{$report['supplier']->name}} [{{$report['report']?->updated_at}} {{$report['report']?->message}}]
                </flux:menu.item>
            @endforeach
            <flux:menu.separator />
        </flux:menu>
    </flux:dropdown>
</div>
