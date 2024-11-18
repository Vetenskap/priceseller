<div>
    <livewire:moysklad::moysklad-bundle-main-attribute-link.moysklad-bundle-main-attribute-link-index
        :moysklad="$moysklad"/>
    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <flux:heading size="xl">Выгрузка комплектов с Моего склада</flux:heading>
            <flux:card>
                <div class="flex gap-2 items-center">
                    <flux:badge color="red">Важно!</flux:badge>
                    <flux:subheading>Необходимо связать поставщиков. Товары комплектов с несвязанными поставщиками не будут добавлены</flux:subheading>
                </div>
            </flux:card>
            <flux:button wire:click="importApi">Выгрузить по АПИ</flux:button>
            <flux:card class="space-y-6">
                <flux:heading size="lg">Отчеты</flux:heading>
                <flux:table>
                    <flux:columns>
                        <flux:column>Статус</flux:column>
                        <flux:column>Обновлено</flux:column>
                        <flux:column>Создано</flux:column>
                        <flux:column>Ошибки</flux:column>
                    </flux:columns>
                    <flux:rows>
                        @foreach($moysklad->apiBundlesReports as $report)
                            <flux:row :key="$report->getKey()">
                                <flux:cell>
                                    <flux:badge :color="$report->status === 2 ? 'yellow' : ($report->status === 1 ? 'red' : 'lime')" inset="top bottom">{{ $report->message }}</flux:badge>
                                </flux:cell>
                                <flux:cell>{{$report->updated}}</flux:cell>
                                <flux:cell>{{$report->created}}</flux:cell>
                                <flux:cell>{{$report->errors}}</flux:cell>
                                <flux:cell>
                                    <flux:button wire:click="deleteReport({{json_encode($report->getKey())}})" variant="danger" size="sm" wire:target="deleteReport({{json_encode($report->getKey())}})" icon="trash"/>
                                </flux:cell>
                                <flux:cell>
                                    <flux:button icon="eye" size="sm" :href="route('moysklad.bundle.reports.show', ['report' => $report->getKey()])"/>
                                </flux:cell>
                            </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>
            </flux:card>
        </flux:card>
    </x-blocks.main-block>
    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <flux:heading size="xl">Вебхуки</flux:heading>
            <flux:card class="space-y-6">
                <flux:heading size="lg">Вебхук на создание комплекта</flux:heading>
                @if($webhook = $moysklad->webhooks()->where(['action' => 'CREATE', 'type' => 'bundle'])->first())
                    <flux:subheading>Дата создания: {{$webhook->created_at}}</flux:subheading>
                    <flux:button variant="danger" wire:click="deleteWebhook({{$webhook}})">Удалить</flux:button>
                @else
                    <flux:button wire:click="addCreateWebhook">Добавить</flux:button>
                @endif
            </flux:card>
            <flux:card class="space-y-6">
                <flux:heading size="lg">Вебхук на изменение комплекта</flux:heading>
                @if($webhook = $moysklad->webhooks()->where(['action' => 'UPDATE', 'type' => 'bundle'])->first())
                    <flux:subheading>Дата создания: {{$webhook->created_at}}</flux:subheading>
                    <flux:button variant="danger" wire:click="deleteWebhook({{$webhook}})">Удалить</flux:button>
                @else
                    <flux:button wire:click="addUpdateWebhook">Добавить</flux:button>
                @endif
            </flux:card>
            <flux:card class="space-y-6">
                <flux:heading size="lg">Вебхук на удаление комплекта</flux:heading>
                @if($webhook = $moysklad->webhooks()->where(['action' => 'DELETE', 'type' => 'bundle'])->first())
                    <flux:subheading>Дата создания: {{$webhook->created_at}}</flux:subheading>
                    <flux:button variant="danger" wire:click="deleteWebhook({{$webhook}})">Удалить</flux:button>
                @else
                    <flux:button wire:click="addDeleteWebhook">Добавить</flux:button>
                @endif
            </flux:card>
        </flux:card>
    </x-blocks.main-block>
</div>
