<x-filament-widgets::widget>
<div class="rounded-2xl border border-gray-100 bg-white p-4 sm:p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="mb-4 flex items-center gap-2 border-b border-gray-100 pb-4 dark:border-gray-800">
            <x-heroicon-o-bolt class="h-5 w-5 text-amber-500" />
            <span class="text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Aksi Cepat</span>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-1">
            @foreach($this->getActions() as $action)
                <a href="{{ $action['url'] }}" 
                   class="group flex items-center justify-between rounded-xl border border-gray-50 bg-gray-50/30 p-3 sm:p-4 transition-all hover:border-amber-200 hover:bg-amber-50 hover:shadow-sm dark:border-gray-800/50 dark:bg-gray-800/20 dark:hover:border-amber-900/50 dark:hover:bg-amber-900/20">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="flex h-9 w-9 sm:h-10 sm:w-10 shrink-0 items-center justify-center rounded-lg bg-white shadow-sm ring-1 ring-gray-900/5 transition-colors group-hover:bg-amber-500 group-hover:text-white group-hover:ring-amber-500 dark:bg-gray-900 dark:ring-gray-100/10 dark:group-hover:bg-amber-500 dark:group-hover:ring-amber-500 text-gray-500 dark:text-gray-400">
                            <x-dynamic-component :component="$action['icon']" class="h-4 w-4 sm:h-5 sm:w-5" />
                        </div>
                        <span class="text-sm sm:text-base font-medium text-gray-700 transition-colors group-hover:text-amber-700 dark:text-gray-300 dark:group-hover:text-amber-400">
                            {{ $action['label'] }}
                        </span>
                    </div>
                    <x-heroicon-m-chevron-right class="h-4 w-4 sm:h-5 sm:w-5 shrink-0 text-gray-400 transition-all group-hover:translate-x-1 group-hover:text-amber-500" />
                </a>
            @endforeach
        </div>
    </div>
</x-filament-widgets::widget>
