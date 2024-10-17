<div class="bg-red-300 text-center flex">
    @if(\App\Services\UsersPermissionsService::getExpiringSubscribes(\App\Helpers\Helpers::user())->isNotEmpty() && !session('notification.div'))
        <div class="w-11/12">
            @foreach(\App\Services\UsersPermissionsService::getExpiringSubscribes(\App\Helpers\Helpers::user()) as $permission)
                <x-titles.sub-title
                    :name="'Ваша подписка '  . $permission->name . ' истекает ' . $permission->pivot->expires"/>
            @endforeach
        </div>
        <div class="w-1/12 flex items-center justify-end mr-4 cursor-pointer" wire:click="close">
            &#10006;
        </div>
    @endif
</div>
