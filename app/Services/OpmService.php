<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Smalot\PdfParser\Parser; // Pastikan library smalot/pdfparser sudah terinstall (composer require smalot/pdfparser)
use InvalidArgumentException;
use Exception;

class OpmService
{
    /**
     * Memproses file OPM SPL (Feeder), OPM Distribution, dan ZIP PDF Combine, 
     * lalu meng-inject Header & Data ke template OTDR.
     *
     * @param string      $templatePath      Path ke file template OTDR (.xlsx)
     * @param string|null $opmSplPath        Path ke file OPM SPL / Feeder
     * @param string|null $opmDistPath       Path ke file OPM Distribution
     * @param string      $outputPath        Path tujuan hasil ekspor
     * @param array       $headerData        Array berisi informasi header
     * @param array       $extractedPdfData  Array pemetaan [ 'LINE_NAME' => '/path/to/COMBINE.pdf', ... ]
     * @return void
     * @throws Exception|InvalidArgumentException
     */
    public function processAllSheets(
        string $templatePath,
        ?string $opmSplPath,
        ?string $opmDistPath,
        string $outputPath,
        array $headerData = [],
        array $extractedPdfData = []
    ): void {
        if (!file_exists($templatePath)) {
            throw new InvalidArgumentException("File template tidak ditemukan pada path: {$templatePath}");
        }

        try {
            $spreadsheetTemplate = IOFactory::load($templatePath);

            // 1. Inject Data Header
            if (!empty($headerData)) {
                $this->injectHeaderData($spreadsheetTemplate, $headerData);
            }

            // 2. Proses Data Sheet 1: E2E OPM Feeder
            if (!empty($opmSplPath) && file_exists($opmSplPath)) {
                $this->processFeederSheet($spreadsheetTemplate, $opmSplPath);
            }

            // 3. Proses Data Sheet 2: E2E OPM Distribution
            if (!empty($opmDistPath) && file_exists($opmDistPath)) {
                $this->processDistributionSheet($spreadsheetTemplate, $opmDistPath);
            }

            // 4. Proses Data Sheet 3: OTDR Summary / Pemetaan PDF Combine
            if (!empty($extractedPdfData)) {
                $this->processOtdrSummarySheet($spreadsheetTemplate, $extractedPdfData);
            }

            // 5. Simpan Hasil ke Path Output
            $writer = IOFactory::createWriter($spreadsheetTemplate, 'Xlsx');
            $writer->save($outputPath);

            // Cleanup Memori tanpa disconnectCells()
            unset($spreadsheetTemplate);
            gc_collect_cycles();

        } catch (Exception $e) {
            throw new Exception("Gagal memproses file OPM: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Helper privat untuk memformat nilai ukur OPM agar selalu memiliki 2 digit desimal (misal: -19,8 -> -19,80)
     */
    private function formatOpmValue($val)
    {
        if ($val === null || $val === '') {
            return $val;
        }

        // Jika berbentuk string dengan koma (misal "-19,8"), ubah koma ke titik untuk pengecekan numerik
        $cleanVal = str_replace(',', '.', (string) $val);

        if (is_numeric($cleanVal)) {
            $formatted = number_format((float) $cleanVal, 2, '.', '');
            // Jika input asli menggunakan koma, kembalikan dengan format koma
            if (strpos((string) $val, ',') !== false) {
                return str_replace('.', ',', $formatted);
            }
            return (float) $formatted;
        }

        return $val;
    }

    /**
     * Helper privat untuk mengisi nilai ke sel sekaligus mengatur Rata Kiri & Cetak Tebal (Bold)
     */
    private function setHeaderValue($sheet, string $cellCoordinate, $value): void
    {
        $sheet->setCellValue($cellCoordinate, $value);
        $sheet->getStyle($cellCoordinate)->getFont()->setBold(true);
        $sheet->getStyle($cellCoordinate)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    }

    /**
     * Memasukkan data Header ke Sheet 1 & Sheet 2.
     */
    private function injectHeaderData(Spreadsheet $spreadsheetTemplate, array $data): void
    {
        // SHEET 1: E2E OPM Feeder
        $wsFeeder = $spreadsheetTemplate->getSheetByName('E2E OPM Feeder');
        if ($wsFeeder) {
            if (!empty($data['region']))
                $this->setHeaderValue($wsFeeder, 'B3', $data['region']);
            if (!empty($data['olt_name']))
                $this->setHeaderValue($wsFeeder, 'B4', $data['olt_name']);
            if (!empty($data['address']))
                $this->setHeaderValue($wsFeeder, 'B5', $data['address']);
            if (!empty($data['cluster']))
                $this->setHeaderValue($wsFeeder, 'H3', $data['cluster']);
            if (!empty($data['odf_number']))
                $this->setHeaderValue($wsFeeder, 'H4', $data['odf_number']);
            if (isset($data['opm_calibration_deviation']))
                $this->setHeaderValue($wsFeeder, 'H5', $data['opm_calibration_deviation']);
            if (!empty($data['fdt_number']))
                $this->setHeaderValue($wsFeeder, 'M4', $data['fdt_number']);
            if (!empty($data['power_meter_sn']))
                $this->setHeaderValue($wsFeeder, 'M5', $data['power_meter_sn']);
        }

        // SHEET 2: E2E OPM Distribution
        $wsDist = $spreadsheetTemplate->getSheetByName('E2E OPM Distribution');
        if ($wsDist) {
            if (!empty($data['region']))
                $this->setHeaderValue($wsDist, 'B3', $data['region']);
            if (!empty($data['olt_name']))
                $this->setHeaderValue($wsDist, 'B4', $data['olt_name']);
            if (!empty($data['address']))
                $this->setHeaderValue($wsDist, 'B5', $data['address']);
            if (!empty($data['cluster']))
                $this->setHeaderValue($wsDist, 'J3', $data['cluster']);
            if (!empty($data['odf_number']))
                $this->setHeaderValue($wsDist, 'J4', $data['odf_number']);
            if (isset($data['opm_calibration_deviation']))
                $this->setHeaderValue($wsDist, 'J5', $data['opm_calibration_deviation']);
            if (!empty($data['fdt_number']))
                $this->setHeaderValue($wsDist, 'P4', $data['fdt_number']);
            if (!empty($data['power_meter_sn']))
                $this->setHeaderValue($wsDist, 'P5', $data['power_meter_sn']);
        }
    }

    /**
     * Pengisian data Sheet 1: E2E OPM Feeder
     */
    private function processFeederSheet(Spreadsheet $spreadsheetTemplate, string $opmSplPath): void
    {
        $wsFeeder = $spreadsheetTemplate->getSheetByName('E2E OPM Feeder');
        if (!$wsFeeder) {
            throw new Exception("Sheet 'E2E OPM Feeder' tidak ditemukan pada template.");
        }

        $spreadsheetSpl = IOFactory::load($opmSplPath);
        $afterSplCells = ['G23', 'L23', 'B39', 'G39', 'L39', 'B55', 'G55', 'L55'];

        // Splitter 1 (Sheet '1')
        if ($wsSpl1 = $spreadsheetSpl->getSheetByName('1')) {
            $valSpl1 = $this->formatOpmValue($wsSpl1->getCell('B23')->getValue());
            $wsFeeder->setCellValue('E9', $valSpl1);

            foreach ($afterSplCells as $idx => $cellRef) {
                $val = $this->formatOpmValue($wsSpl1->getCell($cellRef)->getValue());
                $cellCoord = "G" . (9 + $idx);
                $wsFeeder->setCellValue($cellCoord, $val);
                $wsFeeder->getStyle($cellCoord)->getNumberFormat()->setFormatCode('0.00');
            }
        }

        // Splitter 2 (Sheet '2')
        if ($wsSpl2 = $spreadsheetSpl->getSheetByName('2')) {
            $valSpl2 = $this->formatOpmValue($wsSpl2->getCell('B23')->getValue());
            $wsFeeder->setCellValue('E17', $valSpl2);

            foreach ($afterSplCells as $idx => $cellRef) {
                $val = $this->formatOpmValue($wsSpl2->getCell($cellRef)->getValue());
                $cellCoord = "G" . (17 + $idx);
                $wsFeeder->setCellValue($cellCoord, $val);
                $wsFeeder->getStyle($cellCoord)->getNumberFormat()->setFormatCode('0.00');
            }
        }

        unset($spreadsheetSpl);
    }

    /**
     * Pengisian data Sheet 2: E2E OPM Distribution
     */
    private function processDistributionSheet(Spreadsheet $spreadsheetTemplate, string $opmDistPath): void
    {
        $wsDist = $spreadsheetTemplate->getSheetByName('E2E OPM Distribution');
        if (!$wsDist) {
            throw new Exception("Sheet 'E2E OPM Distribution' tidak ditemukan pada template.");
        }

        $spreadsheetDist = IOFactory::load($opmDistPath);
        $distributionCells = ['G23', 'L23', 'B39', 'G39', 'L39', 'B55', 'G55', 'L55'];

        $fatMapping = [
            'A01' => ['col' => 'D', 'startRow' => 9],
            'A02' => ['col' => 'D', 'startRow' => 25],
            'A03' => ['col' => 'H', 'startRow' => 9],
            'A04' => ['col' => 'H', 'startRow' => 25],
            'A05' => ['col' => 'L', 'startRow' => 9],
            'B01' => ['col' => 'L', 'startRow' => 25],
            'B02' => ['col' => 'P', 'startRow' => 9],
            'B03' => ['col' => 'P', 'startRow' => 25],
        ];

        foreach ($spreadsheetDist->getSheetNames() as $sheetName) {
            $cleanSheetName = strtoupper(trim($sheetName));

            if (array_key_exists($cleanSheetName, $fatMapping)) {
                $wsSource = $spreadsheetDist->getSheetByName($sheetName);
                $col = $fatMapping[$cleanSheetName]['col'];
                $startRow = $fatMapping[$cleanSheetName]['startRow'];

                foreach ($distributionCells as $idx => $cellRef) {
                    $targetRow = $startRow + $idx;
                    $rawVal = $wsSource->getCell($cellRef)->getValue();

                    // Format angka agar 2 digit desimal (-19.7 -> -19.70)
                    $formattedVal = $this->formatOpmValue($rawVal);

                    $cellCoord = "{$col}{$targetRow}";
                    $wsDist->setCellValue($cellCoord, $formattedVal);

                    // Set number format di Excel agar tetap menampilkan 2 angka dibelakang koma/titik
                    $wsDist->getStyle($cellCoord)->getNumberFormat()->setFormatCode('0.00');
                }
            }
        }

        unset($spreadsheetDist);
    }

    /**
     * Pengisian data Sheet 3: OTDR Summary / Pemetaan PDF Combine
     */

    private function processOtdrSummarySheet(Spreadsheet $spreadsheetTemplate, array $extractedPdfData): void
    {
        $wsSummary = $spreadsheetTemplate->getSheetByName('OTDR Summary (FDT-FAT)')
            ?? $spreadsheetTemplate->getSheetByName('OTDR Sumary (FDT-FAT)');

        if (!$wsSummary) {
            throw new Exception("Sheet 'OTDR Summary (FDT-FAT)' tidak ditemukan pada template Excel.");
        }

        foreach ($extractedPdfData as $pdfPath) {
            if (!file_exists($pdfPath)) {
                continue;
            }

            // Gunakan helper parseCombinePdfPages untuk membaca semua halaman dan memetakan berdasarkan nama FAT (A01, A02, dll)
            $parsedPages = $this->parseCombinePdfPages($pdfPath);

            foreach ($parsedPages as $lineName => $data) {
                // Tentukan baris Excel secara dinamis berdasarkan nama FAT/Line (misal A01, A02, dll)
                $targetRow = $this->resolveExcelRowByLineName($wsSummary, $lineName);

                if (!$targetRow) {
                    continue; // Lewati jika baris untuk FAT tersebut tidak ditemukan di template
                }

                $distance = $data['distance'] ?? null;
                $cumLoss = $data['cum_loss'] ?? null;
                $is1550 = $data['is_1550'] ?? false;

                // Masukkan data ke Excel jika nilai valid
                if ($distance !== null && $cumLoss !== null) {
                    // Tentukan kolom berdasarkan panjang gelombang (1310 nm atau 1550 nm)
                    // Sesuaikan nama kolom jika layout template Anda memisahkan 1310 dan 1550
                    // Contoh standar: Kolom F & G untuk 1310, atau sesuaikan kebutuhan layout Anda

                    // Kolom F: Distance (Km)
                    $wsSummary->setCellValue('F' . $targetRow, str_replace(',', '.', $distance));

                    // Kolom G: Cum Loss (1310 nm)
                    $wsSummary->setCellValue('G' . $targetRow, str_replace(',', '.', $cumLoss));
                }
            }
        }
    }
    private function parsePdfEndValues($pdfPath)
    {
        try {
            if (!file_exists($pdfPath)) {
                return null;
            }

            $parser = new Parser();
            $pdf = $parser->parseFile($pdfPath);
            $text = $pdf->getText();

            // Ubah teks menjadi satu baris panjang untuk membersihkan spasi berlebih / newline
            $cleanText = preg_replace('/\s+/', ' ', $text);

            // Cari pola yang mendahului kata END dan ambil angka-angka berformat desimal di dekatnya
            // Contoh teks: "END 0.079 ---.--- ---.--- 13.141"
            if (preg_match('/END\s+([0-9\.]+)\s+.*?([0-9\.]+)/i', $cleanText, $matches)) {
                $distance = $matches[1];
                $cumLoss = $matches[2];

                // Validasi bahwa keduanya adalah angka valid
                if (is_numeric($distance) && is_numeric($cumLoss)) {
                    return [
                        'distance' => str_replace(',', '.', $distance),
                        'cum_loss' => str_replace(',', '.', $cumLoss),
                    ];
                }
            }

            // Fallback: Cari baris demi baris yang mengandung kata END
            $lines = explode("\n", $text);
            foreach ($lines as $line) {
                if (stripos($line, 'END') !== false) {
                    // Ambil semua angka desimal dalam baris tersebut
                    preg_match_all('/\d+\.\d+/', $line, $matches);
                    $numbers = $matches[0] ?? [];

                    if (count($numbers) >= 2) {
                        return [
                            'distance' => str_replace(',', '.', $numbers[0]),
                            'cum_loss' => str_replace(',', '.', $numbers[1]),
                        ];
                    }
                }
            }

        } catch (Exception $e) {
            // Abaikan error per file
        }

        return null;
    }
    private function resolveExcelRowByLineName($wsSummary, $lineName)
    {
        $lineNameUpper = strtoupper(trim($lineName));

        // 1. Ekstraksi nomor core/line dari nama folder (misal: "Core 1", "Line-05", "C3", atau angka "3")
        if (preg_match('/(?:CORE|LINE|C)[\s\-_]*(\d+)/i', $lineNameUpper, $match)) {
            $number = (int) $match[1];

            // Core 1-24 berada di baris 13 s.d. 36 (12 + nomor core)
            if ($number >= 1 && $number <= 24) {
                return 12 + $number;
            }
        }

        // 2. Jika nama folder langsung berupa angka (misal folder "1", "2", "12")
        if (is_numeric(trim($lineName))) {
            $number = (int) trim($lineName);
            if ($number >= 1 && $number <= 24) {
                return 12 + $number;
            }
        }

        // 3. Fallback: Pencarian teks langsung mencocokkan ke kolom C (Core) di dalam Excel
        $highestRow = $wsSummary->getHighestRow();
        for ($row = 13; $row <= $highestRow; $row++) {
            $cellVal = strtoupper(trim($wsSummary->getCell('C' . $row)->getValue()));
            if (!empty($cellVal) && (strpos($lineNameUpper, $cellVal) !== false || strpos($cellVal, $lineNameUpper) !== false)) {
                return $row;
            }
        }

        return null; // Mengembalikan null jika baris tidak ditemukan agar tidak menimpa data yang salah
    }
    private function parseCombinePdfPages($pdfPath)
    {
        $results = [];

        try {
            if (!file_exists($pdfPath)) {
                return $results;
            }

            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($pdfPath);
            $pages = $pdf->getPages();

            foreach ($pages as $page) {
                $text = $page->getText();

                // 1. Ambil nama FAT dari baris "File Name:" (Contoh: "STA23 A01 1310.sor" -> menangkap "A01")
                $lineName = null;
                if (preg_match('/File Name:.*?([A-Z]\d+)/i', $text, $matchLine)) {
                    $lineName = strtoupper($matchLine[1]);
                } elseif (preg_match('/STA\d+\s+([A-Z]\d+)/i', $text, $matchLine)) {
                    $lineName = strtoupper($matchLine[1]);
                }

                if (!$lineName) {
                    continue;
                }

                // 2. Deteksi apakah halaman ini menggunakan 1310 atau 1550 berdasarkan teks "File Name:" atau "1310/1550"
                $is1550 = (strpos($text, '1550') !== false);

                // 3. Cari baris "END" di dalam Event List untuk mengambil Distance dan Cum Loss
                $lines = explode("\n", $text);
                $distance = null;
                $cumLoss = null;

                foreach ($lines as $line) {
                    if (preg_match('/\bEND\b/i', $line)) {
                        preg_match_all('/\d+\.\d+/', $line, $numMatches);
                        $numbers = $numMatches[0] ?? [];

                        if (count($numbers) >= 2) {
                            $distance = $numbers[0];
                            $cumLoss = $numbers[1];
                            break;
                        }
                    }
                }

                if ($distance !== null && $cumLoss !== null) {
                    // Simpan data berdasarkan nama FAT dan jenis gelombangnya
                    $results[$lineName] = [
                        'distance' => str_replace(',', '.', $distance),
                        'cum_loss' => str_replace(',', '.', $cumLoss),
                        'is_1550' => $is1550,
                    ];
                }
            }
        } catch (\Exception $e) {
            // Abaikan error per halaman
        }

        return $results;
    }
}