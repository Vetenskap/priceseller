<div>
    @if (is_null(session('cookies')))
        <div
            class="fixed w-1/4 p-4 mx-auto bg-white border border-gray-200 dark:bg-gray-800 left-12 bottom-16 dark:border-gray-700 rounded-2xl">
            <h2 class="font-semibold text-gray-800 dark:text-white">🍪 Мы используем файлы cookie!</h2>

            <p class="mt-4 text-sm text-gray-600 dark:text-gray-300">Здравствуйте, на этом сайте используются файлы
                cookie,
                необходимые для обеспечения его нормальной работы, и файлы cookie для отслеживания, чтобы понять, как вы
                с
                ним взаимодействуете. Последние будут установлены только после получения согласия. <a href="#"
                                                                                                      class="font-medium text-gray-700 underline transition-colors duration-300 dark:hover:text-blue-400 dark:text-white hover:text-blue-500">Позвольте
                    мне выбрать.</a>. </p>

            <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">При закрытии этого окна настройки по умолчанию
                будут
                сохранены.</p>

            <div class="grid grid-cols-2 gap-4 mt-4 shrink-0">
                <button
                    class="text-xs bg-gray-800 font-medium rounded-lg hover:bg-gray-700 text-white px-4 py-2.5 duration-300 transition-colors focus:outline-none"
                    wire:click="acceptCookies">
                    Принять все
                </button>

                <button
                    class="text-xs border text-gray-800 hover:bg-gray-100 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 font-medium rounded-lg px-4 py-2.5 duration-300 transition-colors focus:outline-none"
                    wire:click="rejectCookies">
                    Отклонить все
                </button>

                <button
                    class="text-xs border text-gray-800 hover:bg-gray-100 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 font-medium rounded-lg px-4 py-2.5 duration-300 transition-colors focus:outline-none">
                    Предпочтения
                </button>

                <button
                    class="text-xs border text-gray-800 hover:bg-gray-100 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 font-medium rounded-lg px-4 py-2.5 duration-300 transition-colors focus:outline-none"
                    wire:click="acceptCookies">
                    Закрыть
                </button>
            </div>
        </div>
    @endif
</div>

