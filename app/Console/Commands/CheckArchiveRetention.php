<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckArchiveRetention extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archives:check-retention';

    protected $description = 'Check archives for approaching retention dates and send notifications';

    public function handle()
    {
        $archives = \App\Models\Archive::with(['category', 'pic', 'uploader'])
            ->join('categories', 'archives.category_id', '=', 'categories.id')
            ->whereRaw('DATE_ADD(archives.upload_date, INTERVAL categories.retention_years YEAR) <= ?', [now()->addDays(30)])
            ->where('archives.status', 'active')
            ->select('archives.*')
            ->get();

        $superAdmins = \App\Models\User::whereHas('roles', function ($query) {
            $query->where('name', 'super_admin');
        })->get();

        $notifiedCount = 0;

        foreach ($archives as $archive) {
            $notification = \Filament\Notifications\Notification::make()
                ->title('Arsip Memasuki Masa Retensi')
                ->body("Arsip '{$archive->title}' (Kategori: {$archive->category->name}) akan/telah mencapai batas waktu penyusutan.")
                ->warning()
                ->actions([
                    \Filament\Notifications\Actions\Action::make('view')
                        ->label('Lihat Arsip')
                        ->url("/admin/archives/{$archive->id}/edit"),
                ]);

            if ($archive->pic) {
                $notification->sendToDatabase($archive->pic);
            }

            if ($archive->uploader && $archive->uploader->id !== $archive->pic_id) {
                $notification->sendToDatabase($archive->uploader);
            }

            foreach ($superAdmins as $admin) {
                if ($admin->id !== $archive->pic_id && $admin->id !== $archive->uploaded_by) {
                    $notification->sendToDatabase($admin);
                }
            }

            $notifiedCount++;
        }

        $this->info("Checked $notifiedCount archives approaching retention.");
    }
}
