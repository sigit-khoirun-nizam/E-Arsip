<x-filament-widgets::widget>
    @php
        $expiredOrdners = $this->getExpiredOrdners();
        $expiringOrdners = $this->getExpiringOrdners();
    @endphp

    <div class="rounded-2xl border border-gray-100 bg-white p-4 sm:p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="mb-4 flex items-center gap-2 border-b border-gray-100 pb-4 dark:border-gray-800">
            <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-amber-500" />
            <span class="text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Perlu Tindakan</span>
            @if($expiredOrdners->count() > 0)
                <span class="ml-auto flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                    {{ $expiredOrdners->count() }}
                </span>
            @endif
        </div>

        @if($expiredOrdners->count() > 0)
            <div class="mb-4">
                <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-red-600 dark:text-red-400">
                    <x-heroicon-o-exclamation-circle class="inline h-3 w-3" />
                    Retensi Habis
                </p>
                <div class="space-y-2">
                    @foreach($expiredOrdners as $ordner)
                        <a href="{{ \App\Filament\Resources\OrdnerResource::getUrl('edit', ['record' => $ordner]) }}"
                           class="flex items-center justify-between rounded-lg border border-red-100 bg-red-50/50 p-2.5 sm:p-3 transition-colors hover:bg-red-50 dark:border-red-900/30 dark:bg-red-900/20 dark:hover:bg-red-900/30">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-red-900 dark:text-red-200">
                                    {{ $ordner->code }}
                                </p>
                                <p class="truncate text-xs text-red-600 dark:text-red-400">
                                    {{ $ordner->category?->name ?? 'Tanpa Kategori' }}
                                </p>
                            </div>
                            <span class="text-xs font-medium text-red-500 whitespace-nowrap ml-2">
                                {{ \Carbon\Carbon::parse($ordner->retention_expires_at)->translatedFormat('d M y') }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if($expiringOrdners->count() > 0)
            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                    <x-heroicon-o-clock class="inline h-3 w-3" />
                    Akan Expired (30 Hari)
                </p>
                <div class="space-y-2">
                    @foreach($expiringOrdners as $ordner)
                        <div class="flex items-center justify-between rounded-lg border border-amber-100 bg-amber-50/50 p-2.5 sm:p-3 dark:border-amber-900/30 dark:bg-amber-900/20">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-amber-900 dark:text-amber-200">
                                    {{ $ordner->code }}
                                </p>
                                <p class="truncate text-xs text-amber-600 dark:text-amber-400">
                                    {{ $ordner->unit?->name }}
                                </p>
                            </div>
                            <span class="text-xs font-medium text-amber-600 whitespace-nowrap ml-2">
                                {{ \Carbon\Carbon::parse($ordner->retention_expires_at)->translatedFormat('d M y') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($expiredOrdners->count() === 0 && $expiringOrdners->count() === 0)
            <div class="flex flex-col items-center justify-center py-6 sm:py-8 text-center">
                <x-heroicon-o-check-circle class="h-8 w-8 sm:h-10 sm:w-10 text-emerald-400" />
                <p class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Semua Tertib!</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ada ordner yang perlu perhatian</p>
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
