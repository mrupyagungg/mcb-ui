<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use InvalidArgumentException;
use Exception;

class OpmService
{
    /**
     * Memproses file OPM SPL (Feeder) dan OPM Distribution, 
     * lalu meng-inject Header & Data ke template OTDR.
     *
     * @param string $templatePath  Path ke file template OTDR (.xlsx)
     * @param string|null $opmSplPath  Path ke file OPM SPL / Feeder
     * @param string|null $opmDistPath Path ke file OPM Distribution
     * @param string $outputPath Path tujuan hasil ekspor
     * @param array $headerData Array berisi informasi header
     * @return void
     * @throws Exception|InvalidArgumentException
     */
    public function processAllSheets(
        string $templatePath,
        ?string $opmSplPath,
        ?string $opmDistPath,
        string $outputPath,
        array $headerData = []
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

            // 4. Simpan Hasil ke Path Output
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
}