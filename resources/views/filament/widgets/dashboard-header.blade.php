<x-filament-widgets::widget>
<div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        {{-- Subtle Background Decoration --}}
        <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full bg-gradient-to-br from-amber-400/10 to-orange-500/5 blur-3xl"></div>
        <div class="absolute -bottom-16 -left-16 h-48 w-48 rounded-full bg-gradient-to-tr from-blue-400/10 to-cyan-500/5 blur-3xl"></div>

        <div class="relative flex flex-col items-center gap-4 md:flex-row md:justify-between">
            <div class="space-y-1.5 text-center md:text-left">
                <h1 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white md:text-2xl">
                    Selamat Datang, <span class="text-amber-600 dark:text-amber-400">{{ $this->getUserName() }}</span> 👋
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Sistem Informasi Kearsipan <span class="font-medium text-gray-700 dark:text-gray-300">{{ $this->getUnitName() }}</span>
                </p>
                <div class="mt-2 flex flex-wrap justify-center gap-2 md:justify-start">
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200/50 bg-emerald-50 px-2.5 py-0.5 text-[10px] uppercase tracking-wider font-bold text-emerald-700 dark:border-emerald-800/30 dark:bg-emerald-900/20 dark:text-emerald-400">
                        <span class="flex h-1.5 w-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></span>
                        Sistem Aktif
                    </span>
                    <span class="inline-flex items-center gap-1 rounded-full border border-gray-200 bg-gray-50 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        <x-heroicon-m-calendar class="h-3 w-3 text-gray-400" />
                        {{ now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>
            </div>

            <div class="hidden lg:block">
                <div class="flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50/50 p-3 dark:border-gray-800 dark:bg-gray-800/50">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 text-white shadow-md shadow-amber-500/20">
                        <x-heroicon-o-clock class="h-5 w-5" />
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Waktu Server</p>
                        <p class="text-lg font-bold tracking-tight text-gray-900 dark:text-white" id="current-time">
                            {{ now()->format('H:i') }}
                        </p>
                    </div>
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
        }
        setInterval(updateTime, 10000);
    </script>
</x-filament-widgets::widget>
