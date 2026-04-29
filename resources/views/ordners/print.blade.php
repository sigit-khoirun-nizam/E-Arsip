<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Label Ordner {{ $ordner->code }}</title>
    <style>
        /* DOMPDF Compatible CSS */
        @page {
            size: 60mm 200mm portrait;
            margin: 0;
        }

        body {
            font-family: 'Segoe UI', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 60mm;
            height: 200mm;
            background-color: #ffffff;
            color: #1f2937;
        }

        .color-strip {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 12mm;
            height: 200mm;
            background-color:
                {{ $unitColor }}
            ;
            border-right: 1px solid #e5e7eb;
            overflow: hidden;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
        }

        /* Teks vertikal di strip kiri */
        .strip-text-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-90deg);
            width: 190mm;
            /* Ruang untuk teks memanjang */
            text-align: center;
        }

        .strip-text {
            color: #ffffff;
            font-weight: 800;
            font-size: 14pt;
            letter-spacing: 4px;
            text-transform: uppercase;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        .content {
            position: absolute;
            left: 12mm;
            top: 0;
            width: 48mm;
            height: 200mm;
            padding: 0;
            box-sizing: border-box;
            text-align: center;
        }

        /* Inner Wrapper untuk memberi padding dan mendistribusikan space */
        .inner-content {
            padding: 5mm;
            height: 155mm;
        }

        /* Logo Box */
        .logo-container {
            margin-top: 5mm;
            margin-bottom: 8mm;
        }

        .logo {
            max-width: 38mm;
            max-height: 28mm;
        }

        .logo-placeholder {
            border: 1px dashed #d1d5db;
            padding: 8mm 0;
            color: #9ca3af;
            font-size: 10pt;
            border-radius: 6px;
            background-color: #f9fafb;
        }

        /* Tahun (Kotak Warna Unit) */
        .year-box {
            background-color: #f3f4f6;
            color:
                {{ $unitColor }}
            ;
            border: 2px solid
                {{ $unitColor }}
            ;
            padding: 4mm 0;
            border-radius: 8px;
            margin-bottom: 10mm;
            width: 100%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .year-text {
            font-size: 26pt;
            font-weight: 900;
            letter-spacing: 4px;
        }

        /* Seksi Label & Nilai */
        .section-box {
            margin-bottom: 8mm;
            padding-bottom: 6mm;
            border-bottom: 2px dashed #e5e7eb;
        }

        .section-box:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .label-text {
            font-size: 9pt;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 2mm;
            font-weight: 600;
        }

        .value-text {
            font-size: 15pt;
            font-weight: 800;
            color: #111827;
            text-transform: uppercase;
            line-height: 1.3;
        }

        .value-text-highlight {
            font-size: 15pt;
            font-weight: 900;
            color:
                {{ $unitColor }}
            ;
            text-transform: uppercase;
            line-height: 1.3;
        }

        /* Area Bawah (Kode Unit / No Ordner) */
        .bottom-section {
            position: absolute;
            bottom: 8mm;
            left: 4mm;
            right: 4mm;
            text-align: center;
        }

        .unit-code-box {
            background-color:
                {{ $unitColor }}
            ;
            color: #ffffff;
            padding: 6mm 1mm;
            border-radius: 10px;
            width: 100%;
            margin-bottom: 3mm;
            box-sizing: border-box;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .unit-code-text {
            font-size: 22pt;
            font-weight: 900;
            letter-spacing: 1px;
            word-wrap: break-word;
            line-height: 1.2;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .unit-name {
            font-size: 12pt;
            color: #374151;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 2mm;
        }
    </style>
</head>

<body>

    @php
        $unitColor = $ordner->unit?->color ?? '#3b82f6';

        // Parse Tahun & Periode
        $year = date('Y');
        $periodDisplay = '-';

        if ($ordner->period) {
            $parts = explode(' - ', $ordner->period);
            $startDate = \Carbon\Carbon::parse(($parts[0] ?? '') . '-01');
            $year = $startDate->format('Y');

            if (count($parts) === 2) {
                $start = \Carbon\Carbon::parse($parts[0] . '-01')->locale('id')->translatedFormat('M');
                $end = \Carbon\Carbon::parse($parts[1] . '-01')->locale('id')->translatedFormat('M');
                $periodDisplay = strtoupper($start) . ' - ' . strtoupper($end);
            } else {
                $periodDisplay = strtoupper($startDate->locale('id')->translatedFormat('F'));
            }
        }

        $logoPath = public_path('image/logo.png');
        $logoExists = file_exists($logoPath);

        // Logic to remove "SU" as requested by user
        // We remove it from code and unit short name
        $displayCode = str_replace('SU', '', $ordner->code);
        // Remove leading/trailing slashes and handle double slashes if SU was in the middle
        $displayCode = str_replace('//', '/', $displayCode);
        $displayCode = trim($displayCode, '/');

        // Rapihkan: Add spaces around slashes for better readability
        $displayCode = str_replace('/', ' / ', $displayCode);

        $unitShortName = $ordner->unit?->short_name ?? '-';
        if (strtoupper($unitShortName) === 'SU') {
            $unitShortName = '';
        }
    @endphp

    <!-- Strip warna bagian kiri menggunakan Absolute Position -->
    <div class="color-strip">
        <div class="strip-text-container">
            <span class="strip-text">{{ $ordner->unit?->name ?? 'UNIT' }}</span>
        </div>
    </div>

    <!-- Area konten utama menggunakan Absolute Position -->
    <div class="content">

        <div class="inner-content">
            <!-- Logo -->
            <div class="logo-container">
                @if($logoExists)
                    <img class="logo" src="{{ $logoPath }}" alt="Logo">
                @else
                    <div class="logo-placeholder">LOGO KKUSB</div>
                @endif
            </div>

            <!-- Tahun -->
            <div class="year-box">
                <span class="year-text">{{ $year }}</span>
            </div>

            <!-- Jenis Ordner -->
            <div class="section-box">
                <div class="label-text">Kategori</div>
                <div class="value-text">
                    {{ $ordner->category?->name ?? 'KATEGORI' }}
                </div>
            </div>

            <!-- Periode -->
            <div class="section-box">
                <div class="label-text">Periode</div>
                <div class="value-text-highlight">
                    {{ $periodDisplay }}
                </div>
            </div>

        </div>

        <!-- Nomor Ordner ditempel absolut di paling bawah konten -->
        <div class="bottom-section">
            <div class="unit-code-box">
                <span class="unit-code-text">{{ $displayCode }}</span>
            </div>
        </div>

    </div>

</body>

</html>