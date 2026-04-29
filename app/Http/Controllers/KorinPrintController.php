<?php

namespace App\Http\Controllers;

use App\Models\Korin;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class KorinPrintController extends Controller
{
    public function printPdf($id)
    {
        $korin = Korin::with(['unitPengirim', 'pembuat', 'disposisis.pengirim'])->findOrFail($id);

        $disposisiKetua = '';
        $disposisiBendahara = '';
        $disposisiSekretaris = '';

        foreach ($korin->disposisis as $disposisi) {
            $catatan = strip_tags($disposisi->catatan ?? '');
            if ($catatan) {
                $statusText = $disposisi->status !== 'Pending' ? " [{$disposisi->status}]" : '';
                $catatan .= $statusText;
            } else {
                $catatan = $disposisi->status !== 'Pending' ? "[{$disposisi->status}]" : '';
            }

            if ($disposisi->pengirim) {
                if ($disposisi->pengirim->hasRole('pengurus ketua')) {
                    $disposisiKetua = $catatan;
                } elseif ($disposisi->pengirim->hasRole('pengurus bendahara')) {
                    $disposisiBendahara = $catatan;
                } elseif ($disposisi->pengirim->hasRole('pengurus sekretaris')) {
                    $disposisiSekretaris = $catatan;
                } elseif ($disposisi->pengirim->hasRole('pengurus')) {
                    // Fallback to pengurus if specific role isn't assigned
                    $disposisiKetua = $disposisiKetua ?: $catatan;
                }
            }
        }

        $pdf = Pdf::loadView('korins.print', [
            'korin' => $korin,
            'disposisiKetua' => $disposisiKetua,
            'disposisiBendahara' => $disposisiBendahara,
            'disposisiSekretaris' => $disposisiSekretaris,
        ]);

        return $pdf->stream("Korin_{$korin->nomor_surat}.pdf");
    }
}
