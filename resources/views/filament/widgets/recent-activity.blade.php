<x-filament-widgets::widget>
    <div class="rounded-2xl border border-gray-100 bg-white p-4 sm:p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="mb-4 flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
            <div class="flex items-center gap-2">
                <x-heroicon-o-clock class="h-5 w-5 text-amber-500" />
                <span class="text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Aktivitas Terbaru</span>
            </div>
            <span class="text-xs text-gray-400">Real-time</span>
        </div>

        <div class="space-y-3 sm:space-y-4">
            @forelse($this->getActivities() as $activity)
                <div class="flex items-start gap-3 group">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-{{ $activity['color'] }}-50 text-{{ $activity['color'] }}-600 dark:bg-{{ $activity['color'] }}-900/30 dark:text-{{ $activity['color'] }}-400">
                        <x-dynamic-component :component="$activity['icon']" class="h-4 w-4" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-gray-900 dark:text-white">
                            {{ $activity['title'] }}
                        </p>
                        <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                            {{ $activity['description'] }}
                        </p>
                    </div>
                    <span class="text-xs text-gray-400 whitespace-nowrap hidden sm:block">
                        {{ $activity['time']->diffForHumans() }}
                    </span>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-6 sm:py-8 text-center">
                    <x-heroicon-o-inbox class="h-8 w-8 sm:h-10 sm:w-10 text-gray-300 dark:text-gray-600" />
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Belum ada aktivitas</p>
                </div>
            @endforelse
        </div>
    </div>
</x-filament-widgets::widget>
