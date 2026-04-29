<?php

namespace App\Http\Controllers;

use App\Models\Ordner;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OrdnerPrintController extends Controller
{
    public function printPdf($id)
    {
        $ordner = Ordner::with(['unit', 'category', 'letterType', 'archives.uploader'])->findOrFail($id);

        // Warna unit, fallback ke biru
        $unitColor = $ordner->unit?->color ?? '#47b5e6';

        $pdf = Pdf::loadView('ordners.print', [
            'ordner'    => $ordner,
            'unitColor' => $unitColor,
        ]);

        // Ukuran custom: 60mm × 180mm (portrait label punggung ordner)
        $pdf->setPaper([0, 0, 170.08, 510.24], 'portrait'); // 60mm x 180mm in pts (1mm = 2.835pt)

        $pdf->setOptions([
            'dpi'                     => 150,
            'defaultFont'             => 'Arial',
            'isHtml5ParserEnabled'    => true,
            'isRemoteEnabled'         => false,
            'chroot'                  => public_path(),
        ]);

        $safeFilename = str_replace(['/', '\\'], '_', $ordner->code);

        return $pdf->stream("Label_Ordner_{$safeFilename}.pdf");
    }
}
