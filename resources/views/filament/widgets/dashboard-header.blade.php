<x-filament-widgets::widget>
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-white via-white to-amber-50/50 border border-gray-100 p-3 sm:p-5 shadow-sm dark:border-gray-800 dark:bg-gradient-to-br dark:from-gray-900 dark:via-gray-900 dark:to-amber-950/20">
        <div class="absolute -right-8 sm:-right-16 -top-8 sm:-top-16 h-24 sm:h-48 w-24 sm:w-48 rounded-full bg-gradient-to-br from-amber-400/20 to-orange-500/10 blur-2xl sm:blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-8 sm:-bottom-16 -left-8 sm:-left-16 h-24 sm:h-48 w-24 sm:w-48 rounded-full bg-gradient-to-tr from-blue-400/20 to-cyan-500/10 blur-2xl sm:blur-3xl animate-pulse" style="animation-delay: 2s;"></div>

        <div class="relative flex flex-col items-center gap-2 sm:gap-4 sm:flex-row sm:justify-between">
            <div class="space-y-1 text-center sm:text-left w-full">
                <h1 class="text-base sm:text-xl font-bold tracking-tight text-gray-900 dark:text-white md:text-2xl">
                    Selamat Datang, <span class="text-amber-600 dark:text-amber-400">{{ $this->getUserName() }}</span> &#128075;
                </h1>
                <p class="text-[10px] sm:text-sm text-gray-500 dark:text-gray-400 line-clamp-1">
                    Sistem Informasi Kearsipan <span class="font-medium text-gray-700 dark:text-gray-300">{{ $this->getUnitName() }}</span>
                </p>
                <div class="mt-1 sm:mt-2 flex flex-wrap justify-center gap-1 sm:gap-2 sm:justify-start">
                    <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200/50 bg-emerald-50 px-1.5 py-0.5 text-[8px] sm:text-[10px] uppercase tracking-wider font-bold text-emerald-700 dark:border-emerald-800/30 dark:bg-emerald-900/20 dark:text-emerald-400 animate-pulse">
                        <span class="flex h-1.5 w-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></span>
                        <span class="hidden sm:inline">Sistem Aktif</span>
                        <span class="sm:hidden">Aktif</span>
                    </span>
                    <span class="inline-flex items-center gap-0.5 sm:gap-1 rounded-full border border-gray-200 bg-gray-50 px-1.5 py-0.5 text-[9px] sm:text-xs font-medium text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        <x-heroicon-m-calendar class="h-2.5 w-2.5 sm:h-3 sm:w-3 text-gray-400" />
                        <span class="hidden sm:inline">{{ now()->translatedFormat('l, d F Y') }}</span>
                        <span class="sm:hidden">{{ now()->translatedFormat('d F Y') }}</span>
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-1.5 sm:gap-3 rounded-lg sm:rounded-xl border border-gray-100 bg-gray-50/50 p-1.5 sm:p-3 dark:border-gray-800 dark:bg-gray-800/50 mt-2 sm:mt-0">
                <div class="flex h-7 w-7 sm:h-10 sm:w-10 items-center justify-center rounded-lg bg-gradient-to-br from-amber-400 to-amber-600 text-white shadow-md shadow-amber-500/20">
                    <x-heroicon-o-clock class="h-3.5 w-3.5 sm:h-5 sm:w-5" />
                </div>
                <div class="hidden sm:block">
                    <p class="text-[9px] sm:text-xs font-medium text-gray-500 dark:text-gray-400">Waktu Server</p>
                    <p class="text-sm sm:text-lg font-bold tracking-tight text-gray-900 dark:text-white" id="current-time">
                        {{ now()->format('H:i') }}
                    </p>
                </div>
                <div class="sm:hidden">
                    <p class="text-sm font-bold tracking-tight text-gray-900 dark:text-white" id="current-time-xs">
                        {{ now()->format('H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateTime() {
            const now = new Date();
            const timeString = now.getHours().toString().padStart(2, '0') + ':' + 
                               now.getMinutes().toString().padStart(2, '0');
            const timeElement = document.getElementById('current-time');
            if (timeElement) timeElement.textContent = timeString;
            const timeElementXs = document.getElementById('current-time-xs');
            if (timeElementXs) timeElementXs.textContent = timeString;
        }
        setInterval(updateTime, 10000);
    </script>
</x-filament-widgets::widget>
