<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OpmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Models\HistoryLog;
use ZipArchive;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.cwatp.index');
    }

    public function opmcIndex()
    {
        return view('admin.opmc.index');
    }

    // Tambahkan method ini
    public function opmcReaderIndex()
    {
        return view('admin.opmc.reader');
    }

    public function process(Request $request, OpmService $opmService)
    {
        // 1. Validasi Input
        $request->validate([
            'region' => 'required|string',
            'cluster' => 'required|string',
            'opm_spl_file' => 'nullable|file|mimes:xlsx,xls',
            'opm_dist_file' => 'nullable|file|mimes:xlsx,xls',
        ]);

        // 2. Kumpulkan Data Header
        $headerData = [
            'region' => $request->input('region'),
            'cluster' => $request->input('cluster'),
            'olt_name' => $request->input('olt_name'),
            'odf_number' => $request->input('odf_number'),
            'fdt_number' => $request->input('fdt_number'),
            'opm_calibration_deviation' => $request->input('opm_calibration_deviation'),
            'power_meter_sn' => $request->input('power_meter_sn'),
            'address' => $request->input('address'),
        ];

        // Path File
        $templatePath = storage_path('app/templates/OTDR_for_OPM.xlsx');
        $outputPath = storage_path('app/public/OPM_Result_' . time() . '.xlsx');

        $opmSplPath = $request->hasFile('opm_spl_file') ? $request->file('opm_spl_file')->getRealPath() : null;
        $opmDistPath = $request->hasFile('opm_dist_file') ? $request->file('opm_dist_file')->getRealPath() : null;

        // 3. Panggil Service
        try {
            $opmService->processAllSheets($templatePath, $opmSplPath, $opmDistPath, $outputPath, $headerData);
            // Return file untuk diunduh langsung oleh Fetch/Blob JavaScript
            return response()->download($outputPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }

    public function opmcReaderProcess(Request $request, \App\Services\OpmService $opmService)
    {
        // 1. Validasi Input (Header & File)
        $request->validate([
            'region' => 'required|string',
            'cluster' => 'required|string',
            'olt_name' => 'nullable|string',
            'odf_number' => 'nullable|string',
            'fdt_number' => 'nullable|string',
            'opm_calibration_deviation' => 'nullable|string',
            'power_meter_sn' => 'nullable|string',
            'address' => 'nullable|string',
            'opm_spl_file' => 'nullable|file|mimes:xlsx,xls|max:10240',
            'opm_dist_file' => 'nullable|file|mimes:xlsx,xls|max:10240',
        ]);

        if (!$request->hasFile('opm_spl_file') && !$request->hasFile('opm_dist_file')) {
            return redirect()->back()->with('error', 'Pilih minimal satu file untuk diproses!');
        }

        // 2. Kumpulkan Data Header dari Form
        $headerData = [
            'region' => $request->input('region'),
            'cluster' => $request->input('cluster'),
            'olt_name' => $request->input('olt_name'),
            'odf_number' => $request->input('odf_number'),
            'fdt_number' => $request->input('fdt_number'),
            'opm_calibration_deviation' => $request->input('opm_calibration_deviation'),
            'power_meter_sn' => $request->input('power_meter_sn'),
            'address' => $request->input('address'),
        ];

        try {
            $templatePath = storage_path('app/templates/OTDR_for_OPM.xlsx');
            $outputPath = storage_path('app/public/OTDR_for_OPM_Filled_' . time() . '.xlsx');

            $opmSplPath = $request->hasFile('opm_spl_file') ? $request->file('opm_spl_file')->getRealPath() : null;
            $opmDistPath = $request->hasFile('opm_dist_file') ? $request->file('opm_dist_file')->getRealPath() : null;

            // 3. Kirim $headerData sebagai argumen ke service
            $opmService->processAllSheets($templatePath, $opmSplPath, $opmDistPath, $outputPath, $headerData);

            // Return file untuk diunduh langsung oleh Fetch/Blob JavaScript
            return response()->download($outputPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }
    public function processOcr(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,jpg,png|max:10240',
        ]);

        try {
            $image = $request->file('photo');

            // 1. Buat direktori temp & scripts jika belum ada
            $tempDir = storage_path('app/temp');
            $scriptDir = storage_path('app/scripts');

            if (!File::exists($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }
            if (!File::exists($scriptDir)) {
                File::makeDirectory($scriptDir, 0755, true);
            }

            // 2. Simpan gambar sementara
            $tempFileName = 'ocr_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $tempPath = storage_path('app/temp/' . $tempFileName);
            $image->move($tempDir, $tempFileName);

            // 3. Tentukan path skrip Python & lokasi executable Python
            $scriptPath = storage_path('app/scripts/ocr_processor.py');

            // Validasi keberadaan file script Python
            if (!File::exists($scriptPath)) {
                if (File::exists($tempPath)) {
                    File::delete($tempPath);
                }
                return response()->json([
                    'success' => false,
                    'message' => 'File script Python tidak ditemukan di: ' . $scriptPath
                ], 500);
            }

            // Deteksi OS: Gunakan 'python' untuk Windows, atau 'python3' untuk Linux/Mac
            $defaultPython = PHP_OS_FAMILY === 'Windows' ? 'python' : 'python3';
            $pythonBinary = env('PYTHON_BINARY', $defaultPython);

            // 4. Jalankan perintah menggunakan format yang aman untuk Windows & Linux
            $command = sprintf(
                '"%s" "%s" "%s"',
                $pythonBinary,
                $scriptPath,
                $tempPath
            );

            $result = Process::run($command);

            // 5. Hapus gambar sementara
            if (File::exists($tempPath)) {
                File::delete($tempPath);
            }

            // Jika eksekusi gagal (misal: Python tidak terdaftar di Environment Path)
            if ($result->failed()) {
                $errorMsg = $result->errorOutput() ?: $result->output();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menjalankan OCR lokal: ' . $errorMsg
                ], 500);
            }

            $parsedData = json_decode(trim($result->output()), true);

            if (isset($parsedData['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error dari OCR: ' . $parsedData['error']
                ], 422);
            }

            return response()->json([
                'success' => true,
                'data' => $parsedData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }
    public function generateExcel(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'nullable|string',
            'lokasi' => 'nullable|string',
            'cluster' => 'nullable|string',
            'fdt_id' => 'nullable|string',
            'spl_no' => 'nullable|string',
            'spliter_ratio' => 'nullable|string',
            'input_dbm' => 'nullable|string',
            'ports' => 'nullable|array',
        ]);

        $templatePath = storage_path('app/templatesTemplate File Foto OPM.xlsx');

        if (!file_exists($templatePath)) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'FORM HASIL PENGUKURAN TES OPM (FDT)');
            $sheet->setCellValue('A3', 'Tanggal Pengukuran:');
            $sheet->setCellValue('B3', $data['tanggal'] ?? '');
            $sheet->setCellValue('A4', 'Lokasi:');
            $sheet->setCellValue('B4', $data['lokasi'] ?? '');
            $sheet->setCellValue('A5', 'Cluster:');
            $sheet->setCellValue('B5', $data['cluster'] ?? '');
            $sheet->setCellValue('A6', 'FDT ID:');
            $sheet->setCellValue('B6', $data['fdt_id'] ?? '');
            $sheet->setCellValue('A7', 'SPL NO:');
            $sheet->setCellValue('B7', $data['spl_no'] ?? '');
            $sheet->setCellValue('A8', 'Spliter Ratio:');
            $sheet->setCellValue('B8', $data['spliter_ratio'] ?? '');
            $sheet->setCellValue('A9', 'Input (dBm):');
            $sheet->setCellValue('B9', $data['input_dbm'] ?? '');

            $sheet->setCellValue('A11', 'Port Output');
            $sheet->setCellValue('B11', 'Nilai Ukur (dBm)');
            $sheet->setCellValue('C11', 'Status');

            $row = 12;
            if (isset($data['ports']) && is_array($data['ports'])) {
                foreach ($data['ports'] as $portNumber => $val) {
                    $sheet->setCellValue('A' . $row, "Port " . $portNumber);
                    $sheet->setCellValue('B' . $row, ($val !== null && $val !== '') ? (float) $val : '');
                    $sheet->setCellValue('C' . $row, ($val !== null && $val !== '') ? 'OK' : '-');
                    $row++;
                }
            }
        } else {
            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('L3', ': ' . ($data['lokasi'] ?? ''));
            $sheet->setCellValue('L5', ': ' . ($data['cluster'] ?? ''));
            $sheet->setCellValue('F5', $data['fdt_id'] ?? 'FDT 1 FAT A01');

            if (isset($data['ports']) && is_array($data['ports'])) {
                $slots = [
                    1 => 'B23',
                    2 => 'G23',
                    3 => 'L23',
                    4 => 'B39',
                    5 => 'G39',
                    6 => 'L39',
                    7 => 'B55',
                    8 => 'G55',
                    9 => 'L55',
                ];

                $i = 1;
                foreach ($data['ports'] as $portNumber => $val) {
                    if (isset($slots[$i])) {
                        $sheet->setCellValue($slots[$i], ($val !== null && $val !== '') ? $val : '');
                    }
                    $i++;
                }
            }
        }

        $fileName = 'Hasil_Pengukuran_OPM_' . str_replace(' ', '_', $data['fdt_id'] ?? 'Report') . '.xlsx';
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Memproses File ZIP Berisi Foto OPM, Jalankan OCR Lokal (Python), dan Export ke Template Excel
     */
    public function exportBulkOpm(Request $request)
    {

        // Sekarang pemanggilan ini tidak akan error lagi
        HistoryLog::create([
            'user_id' => auth()->id(),
            'module' => 'ATP Distribusi',
            'action_type' => 'Generate',
            'region' => $request->region,
            'target_name' => $request->olt_name,
            'description' => $request->cluster_name,
            'status' => 'Completed',
        ]);

        ini_set('memory_limit', '2048M');
        set_time_limit(600);

        $request->validate([
            'region' => 'required|string',
            'olt_name' => 'required|string',
            'cluster_name' => 'required|string',
            'cluster_id' => 'required|string',
            'zip_file' => 'required|file|mimes:zip|max:102400',
        ]);

        $templatePath = storage_path('app/templates/FILE  OPM FAT SETIA ASIH RW 23.xlsx');

        if (!File::exists($templatePath)) {
            return back()->with('error', 'File template Excel OPM tidak ditemukan di storage/app/templates/!');
        }

        $zipFile = $request->file('zip_file');
        $extractPath = storage_path('app/temp_zip_opm_' . time());

        // 1. Ekstrak ZIP
        $zip = new ZipArchive();
        if ($zip->open($zipFile->getRealPath()) === TRUE) {
            $zip->extractTo($extractPath);
            $zip->close();
        } else {
            return back()->with('error', 'Gagal mengekstrak file ZIP!');
        }

        // 2. Kumpulkan file gambar
        $allFiles = File::allFiles($extractPath);
        $imageFiles = [];

        foreach ($allFiles as $file) {
            $ext = strtolower($file->getExtension());
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $imageFiles[] = $file;
            }
        }

        if (empty($imageFiles)) {
            File::deleteDirectory($extractPath);
            return back()->with('error', 'Tidak ada file gambar (.jpg/.png) ditemukan di dalam file ZIP!');
        }

        usort($imageFiles, function ($a, $b) {
            return strnatcmp($a->getFilename(), $b->getFilename());
        });

        try {
            $spreadsheet = IOFactory::load($templatePath);
            $masterSheet = $spreadsheet->getActiveSheet();

            // Grid Sel 3x3 Template OPM
            $gridSlots = [
                1 => ['photo' => 'B8', 'value' => 'B23'],
                2 => ['photo' => 'G8', 'value' => 'G23'],
                3 => ['photo' => 'L8', 'value' => 'L23'],
                4 => ['photo' => 'B24', 'value' => 'B39'],
                5 => ['photo' => 'G24', 'value' => 'G39'],
                6 => ['photo' => 'L24', 'value' => 'L39'],
                7 => ['photo' => 'B40', 'value' => 'B55'],
                8 => ['photo' => 'G40', 'value' => 'G55'],
                9 => ['photo' => 'L40', 'value' => 'L55'],
            ];

            $scriptPath = storage_path('app/scripts/ocr_processor.py');
            $pythonBinary = env('PYTHON_BINARY', 'python3');

            $currentSheetIndex = 0;
            $slotInSheet = 1;
            $currentSheet = $masterSheet;

            $currentSheet->setTitle('A01');
            $this->applyOpmHeaderMetadata($currentSheet, $request);

            foreach ($imageFiles as $index => $imgFile) {
                // Jika melebihi 9 foto, buat Sheet baru
                if ($slotInSheet > 9) {
                    $currentSheetIndex++;
                    $sheetCode = 'A' . sprintf('%02d', $currentSheetIndex + 1);

                    $currentSheet = clone $masterSheet;
                    $currentSheet->setTitle($sheetCode);
                    $spreadsheet->addSheet($currentSheet);

                    $drawings = $currentSheet->getDrawingCollection();
                    foreach ($drawings as $key => $dr) {
                        if (str_contains($dr->getName(), 'OPM_PHOTO_')) {
                            unset($drawings[$key]);
                        }
                    }

                    $this->applyOpmHeaderMetadata($currentSheet, $request);
                    $slotInSheet = 1;
                }

                $realImagePath = $imgFile->getRealPath();
                $extractedValue = null;

                // 3. Jalankan OCR via Python
                if (File::exists($scriptPath)) {
                    $process = Process::run("{$pythonBinary} " . escapeshellarg($scriptPath) . " " . escapeshellarg($realImagePath));
                    if ($process->successful()) {
                        $parsedData = json_decode(trim($process->output()), true);
                        if (isset($parsedData['input_dbm'])) {
                            $extractedValue = $parsedData['input_dbm'];
                        } elseif (isset($parsedData['output_ports'][0]['value_dbm'])) {
                            $extractedValue = $parsedData['output_ports'][0]['value_dbm'];
                        }
                    }
                }

                $slotConfig = $gridSlots[$slotInSheet];

                // 4. Masukkan Nilai dBm jika terdeteksi
                if ($extractedValue !== null) {
                    $currentSheet->setCellValue($slotConfig['value'], $extractedValue);
                }

                // 5. Masukkan Foto ke Sel Template
                $drawing = new Drawing();
                $drawing->setName('OPM_PHOTO_' . ($index + 1));
                $drawing->setDescription('Foto OPM ' . ($index + 1));
                $drawing->setPath($realImagePath);
                $drawing->setCoordinates($slotConfig['photo']);
                $drawing->setHeight(230);
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setWorksheet($currentSheet);

                $slotInSheet++;
            }

            File::deleteDirectory($extractPath);

            $outputFileName = 'OPM_Bulk_Report_' . time() . '.xlsx';
            $outputPath = storage_path('app/public/' . $outputFileName);

            if (!File::exists(storage_path('app/public'))) {
                File::makeDirectory(storage_path('app/public'), 0755, true);
            }

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->setPreCalculateFormulas(false);
            $writer->save($outputPath);

            // Return file untuk diunduh langsung oleh Fetch/Blob JavaScript
            return response()->download($outputPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            File::deleteDirectory($extractPath);
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    private function applyOpmHeaderMetadata($sheet, Request $request)
    {
        $sheet->setCellValue('L3', ': ' . $request->input('region'));
        $sheet->setCellValue('L4', ': ' . $request->input('olt_name'));
        $sheet->setCellValue('L5', ': ' . $request->input('cluster_name'));
        $sheet->setCellValue('L6', ': ' . $request->input('cluster_id'));
    }

    // CW ATP  CODE
    public function exportBulkAtp(Request $request)
    {
        // Sekarang pemanggilan ini tidak akan error lagi
        HistoryLog::create([
            'user_id' => auth()->id(),
            'module' => 'ATP Distribusi',
            'action_type' => 'Generate',
            'region' => $request->region,
            'target_name' => $request->olt_name,
            'description' => $request->cluster_name,
            'status' => 'Completed',
        ]);

        ini_set('memory_limit', '2048M');
        set_time_limit(600);

        // 1. Validasi Input Form
        $request->validate([
            'type' => 'required|string|in:distribusi,subfeeder',
            'region' => 'required|string',
            'olt_name' => 'required|string',
            'cluster_name' => 'required|string',
            'cluster_id' => 'required|string',
            'zip_file' => 'required|file|mimes:zip|max:102400',
        ]);

        $type = $request->input('type');

        if ($type === 'subfeeder') {
            $templatePath = storage_path('app/templates/Template ATP - Full Foto-SF.xlsx');
        } else {
            $templatePath = storage_path('app/templates/Template ATP - Full Foto.xlsx');
        }

        if (!file_exists($templatePath)) {
            return back()->with('error', 'File template Excel (' . strtoupper($type) . ') tidak ditemukan!');
        }

        // 2. Ekstraksi File ZIP
        $zipFile = $request->file('zip_file');
        $extractPath = storage_path('app/temp_zip_' . time() . '_' . uniqid());

        $zip = new ZipArchive();
        if ($zip->open($zipFile->getRealPath()) === TRUE) {
            $zip->extractTo($extractPath);
            $zip->close();
        } else {
            return back()->with('error', 'Gagal mengekstrak file ZIP!');
        }

        try {
            $allFiles = File::allFiles($extractPath);
            if (empty($allFiles)) {
                File::deleteDirectory($extractPath);
                return back()->with('error', 'File ZIP kosong!');
            }

            // Load Spreadsheet Template
            $spreadsheet = IOFactory::load($templatePath);

            // Populate Parameter Header (Region, OLT, Cluster) ke Seluruh Sheet Bawaan Template
            foreach ($spreadsheet->getAllSheets() as $tplSheet) {
                $tplSheet->setCellValue('J2', ': ' . $request->input('region'));
                $tplSheet->setCellValue('J3', ': ' . $request->input('olt_name'));
                $tplSheet->setCellValue('J4', ': ' . $request->input('cluster_name'));
                $tplSheet->setCellValue('J5', ': ' . $request->input('cluster_id'));
            }

            if ($type === 'subfeeder') {
                // ==========================================
                // LOGIKA SUBFEEDER (SF)
                // ==========================================
                $mapImplementasiSF = [
                    'pole' => ['cell' => 'B7', 'name' => 'NEW POLE'],
                    'cable' => ['cell' => 'H7', 'name' => 'POOLING CABLE'],
                    'fjc' => ['cell' => 'B27', 'name' => 'FJC CLOSURE'],
                    'closure' => ['cell' => 'B27', 'name' => 'FJC CLOSURE'],
                    'otdr' => ['cell' => 'H27', 'name' => 'OTDR'],
                    'opm' => ['cell' => 'H27', 'name' => 'OPM'],
                ];

                $mapFDT_SF = [
                    'fdt' => ['cell' => 'B7', 'name' => 'FDT Photo'],
                    'cls' => ['cell' => 'B7', 'name' => 'FAT Close'],
                    'opn' => ['cell' => 'H7', 'name' => 'FAT Open'],
                ];

                $sheetImplementasiSF = $spreadsheet->getSheetByName('IMPLEMENTASI SF') ?? $spreadsheet->getSheet(0);
                $sheetFDT_SF = $spreadsheet->getSheetByName('FOTO_FDT') ?? $spreadsheet->getSheet(1);

                foreach ($allFiles as $file) {
                    if (!in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'])) {
                        continue;
                    }

                    $relativePath = strtolower($file->getRelativePath());

                    if (str_contains($relativePath, 'implementasi') || str_contains($relativePath, 'impelemntasi')) {
                        $this->attachImageToSheet($file, $sheetImplementasiSF, $mapImplementasiSF);
                    } elseif (str_contains($relativePath, 'fdt')) {
                        $this->attachImageToSheet($file, $sheetFDT_SF, $mapFDT_SF);
                    }
                }

            } else {
                // ==========================================
                // LOGIKA DISTRIBUSI (DS)
                // ==========================================
                $mapImplementasiDS = [
                    'digging' => ['cell' => 'B7', 'name' => 'DIGGING POLE'],
                    'install' => ['cell' => 'H7', 'name' => 'INSTALL POLE'],
                    'acc' => ['cell' => 'B27', 'name' => 'Install Accessories'],
                    'cable' => ['cell' => 'H27', 'name' => 'Pulling Cable'],
                    'pulling' => ['cell' => 'H27', 'name' => 'Pulling Cable'],
                    'jc' => ['cell' => 'B47', 'name' => 'Install JC'],
                    'fdt' => ['cell' => 'H47', 'name' => 'Install FDT'],
                ];

                $mapFDT_DS = [
                    'cls' => ['cell' => 'B7', 'name' => 'FDT Close'],
                    'opn' => ['cell' => 'H7', 'name' => 'FDT Open'],
                    'acc' => ['cell' => 'H7', 'name' => 'FDT Accessories Pole'],
                    'idpole' => ['cell' => 'H7', 'name' => 'FDT ID Pole'],
                    'pondasi' => ['cell' => 'H7', 'name' => 'FDT Pondasi Pole'],
                    'pole' => ['cell' => 'H7', 'name' => 'FDT Full Pole'],
                ];

                $mapFotoFAT = [
                    'cls' => ['cell' => 'B7', 'name' => 'FAT Close'],
                    'opn' => ['cell' => 'H7', 'name' => 'FAT Open'],
                    'acc' => ['cell' => 'B27', 'name' => 'Accessories'],
                    'idpole' => ['cell' => 'H27', 'name' => 'ID Pole'],
                    'pondasi' => ['cell' => 'B47', 'name' => 'Pondasi Pole'],
                    'pole' => ['cell' => 'H47', 'name' => 'View Pole'],
                ];

                $sheetImplementasiDS = $spreadsheet->getSheetByName('IMPLEMENTASI DS') ?? $spreadsheet->getSheet(0);
                $sheetFDT_DS = $spreadsheet->getSheetByName('FDT') ?? $spreadsheet->getSheet(1);

                $masterFotoFAT = $spreadsheet->getSheetByName('DISTRIBUSI')
                    ?? $spreadsheet->getSheetByName('FOTO_FAT')
                    ?? $spreadsheet->getSheet(2);

                $fatFolders = [];

                foreach ($allFiles as $file) {
                    if (!in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'])) {
                        continue;
                    }

                    $relPathFormatted = str_replace('\\', '/', $file->getRelativePath());
                    $pathSegments = array_values(array_filter(explode('/', trim($relPathFormatted, '/'))));

                    $targetFolder = '';
                    foreach ($pathSegments as $segment) {
                        $segmentLower = strtolower($segment);
                        if (
                            !str_contains($segmentLower, 'implementasi') &&
                            !str_contains($segmentLower, 'impelemntasi') &&
                            $segmentLower !== 'fdt' &&
                            $segmentLower !== 'distribusi'
                        ) {
                            $targetFolder = $segment;
                            break;
                        }
                    }

                    $fullPathLower = strtolower($file->getRealPath());

                    if (str_contains($fullPathLower, 'implementasi') || str_contains($fullPathLower, 'impelemntasi')) {
                        $this->attachImageToSheet($file, $sheetImplementasiDS, $mapImplementasiDS);
                    } elseif (str_contains($fullPathLower, '/fdt/') || str_contains($fullPathLower, '\\fdt\\')) {
                        $this->attachImageToSheet($file, $sheetFDT_DS, $mapFDT_DS);
                    } else {
                        $folderName = !empty($targetFolder) ? $targetFolder : 'FAT_01';
                        $fatFolders[$folderName][] = $file;
                    }
                }

                if (!empty($fatFolders)) {
                    ksort($fatFolders);
                    $fatIndex = 0;
                    $usedFatTitles = [];

                    foreach ($fatFolders as $fatFolderName => $fatImages) {
                        // Sanitasi nama sheet Excel
                        $cleanTitle = substr(preg_replace('/[\/*?:\[\]]/', '', $fatFolderName), 0, 31);
                        $sheetTitle = $cleanTitle;
                        $counter = 1;
                        while (in_array($sheetTitle, $usedFatTitles)) {
                            $suffix = '_' . $counter;
                            $sheetTitle = substr($cleanTitle, 0, 31 - strlen($suffix)) . $suffix;
                            $counter++;
                        }
                        $usedFatTitles[] = $sheetTitle;

                        if ($fatIndex === 0 && $masterFotoFAT) {
                            $currentFatSheet = $masterFotoFAT;
                            $currentFatSheet->setTitle($sheetTitle);
                            $this->clearContentPhotosOnly($currentFatSheet);
                        } else {
                            $currentFatSheet = clone $masterFotoFAT;
                            $currentFatSheet->setTitle($sheetTitle);
                            $this->clearContentPhotosOnly($currentFatSheet);
                            $spreadsheet->addSheet($currentFatSheet);
                        }

                        // ========================================================
                        // FORMAT NAMA HEADER TITLE DAN MERGE ALIGNMENT (CENTER + MIDDLE)
                        // ========================================================
                        $formattedLabel = strtoupper(str_replace('_', ' ', $fatFolderName));
                        if (!str_contains($formattedLabel, 'PHOTO')) {
                            $formattedLabel = 'PHOTO ' . $formattedLabel;
                        }

                        // Set nilai teks ke sel
                        $currentFatSheet->setCellValue('F2', $formattedLabel);
                        $currentFatSheet->setCellValue('F5', $formattedLabel);

                        // Atur Posisi Teks: Horizontal CENTER, Vertical CENTER (MIDDLE ALIGN)
                        $alignmentStyle = [
                            'font' => [
                                'bold' => true,
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                        ];

                        $currentFatSheet->getStyle('F2:G2')->applyFromArray($alignmentStyle);
                        $currentFatSheet->getStyle('F5:G5')->applyFromArray($alignmentStyle);

                        // Tempelkan foto-foto ke sel
                        foreach ($fatImages as $imgFile) {
                            $this->attachImageToSheet($imgFile, $currentFatSheet, $mapFotoFAT);
                        }

                        $fatIndex++;
                    }
                } else {
                    if ($masterFotoFAT && $spreadsheet->getSheetCount() > 2) {
                        $sheetIndexToRemove = $spreadsheet->getIndex($masterFotoFAT);
                        $spreadsheet->removeSheetByIndex($sheetIndexToRemove);
                    }
                }
            }

            // 3. Simpan dan Download File Excel
            $outputFileName = 'ATP_' . ucfirst($type) . '_Report_' . time() . '.xlsx';
            $outputPath = storage_path('app/public/' . $outputFileName);

            if (!File::exists(storage_path('app/public'))) {
                File::makeDirectory(storage_path('app/public'), 0755, true);
            }

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->setPreCalculateFormulas(false);
            $writer->save($outputPath);

            File::deleteDirectory($extractPath);

            return response()->download($outputPath)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            if (File::exists($extractPath)) {
                File::deleteDirectory($extractPath);
            }
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Helper Private Function: Membersihkan foto konten saja tanpa menghapus logo header di A2:E5
     */
    private function clearContentPhotosOnly($sheet)
    {
        if (!$sheet)
            return;

        $drawings = $sheet->getDrawingCollection();
        foreach ($drawings as $key => $drawing) {
            $coord = $drawing->getCoordinates();
            preg_match('/\d+/', $coord, $matches);
            $rowNumber = isset($matches[0]) ? (int) $matches[0] : 0;

            if ($rowNumber > 5) {
                unset($drawings[$key]);
            }
        }
    }

    /**
     * Helper Private Function untuk Tempel Gambar ke Sheet Berdasarkan Map Keyword
     */
    private function attachImageToSheet($imgFile, $sheet, array $map)
    {
        if (!$sheet)
            return;

        $fileNameOnly = strtolower(pathinfo($imgFile->getFilename(), PATHINFO_FILENAME));

        foreach ($map as $keyword => $pos) {
            if (str_contains($fileNameOnly, $keyword)) {
                $drawing = new Drawing();
                $drawing->setName($pos['name']);
                $drawing->setPath($imgFile->getRealPath());
                $drawing->setHeight(320);
                $drawing->setCoordinates($pos['cell']);
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setWorksheet($sheet);
                break;
            }
        }
    }

}