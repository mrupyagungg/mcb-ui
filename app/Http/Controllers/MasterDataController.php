<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use ZipArchive;

class MasterDataController extends Controller
{

    /**
     * Tampilkan halaman form upload template.
     */
    public function uploadTemplateForm()
    {
        return view('admin.template.upload');
    }

    /**
     * Upload / Update Template Excel (Distribusi & Subfeeder)
     */
    public function uploadTemplate(Request $request)
    {
        $request->validate([
            'template_type' => 'required|string|in:distribusi,subfeeder',
            'template_file' => 'required|file|mimes:xlsx|max:10240', // Maks. 10MB
        ]);

        $type = $request->input('template_type');
        $file = $request->file('template_file');

        // Tentukan nama file template yang akan digantikan
        $fileName = ($type === 'subfeeder')
            ? 'Template ATP - Full Foto-SF.xlsx'
            : 'Template ATP - Full Foto.xlsx';

        $destinationPath = storage_path('app/templates');

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        // Simpan / Overwrite file template
        $file->move($destinationPath, $fileName);

        return back()->with('success', 'Template ' . strtoupper($type) . ' berhasil diperbarui!');
    }

    /**
     * Proses Export Bulk ATP (Distribusi & Subfeeder)
     */
    public function exportBulkAtp(Request $request)
    {
        HistoryLog::create([
            'user_id' => auth()->id(),
            'module' => 'Manajemen Template',
            'action_type' => 'Upload',
            'region' => '-',
            'target_name' => $fileName,
            'description' => 'Mengunggah file template baru',
            'status' => 'Selesai',
        ]);

        ini_set('memory_limit', '2048M');
        set_time_limit(600);

        // Validasi Request Input Form
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

        // Ekstraksi File ZIP
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

            // Populate Parameter Header ke Seluruh Sheet Bawaan Template
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
                    'pondasi' => ['cell' => 'B7', 'name' => 'DIGGING POLE'],
                    'install' => ['cell' => 'H7', 'name' => 'INSTALL POLE'],
                    'acc' => ['cell' => 'B27', 'name' => 'Install Accessories'],
                    'pulling' => ['cell' => 'H27', 'name' => 'Pulling Cable'],
                    'cable' => ['cell' => 'H27', 'name' => 'Pulling Cable'],
                ];

                $mapFDT_DS = [
                    'cls' => ['cell' => 'B7', 'name' => 'FAT Close'],
                    'opn' => ['cell' => 'H7', 'name' => 'FAT Open'],
                    'fdt' => ['cell' => 'B7', 'name' => 'FDT Photo'],
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
                        // Sanitasi Nama Sheet
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

                        // Set Format Label Header ke F2 dan F5
                        $formattedLabel = strtoupper(str_replace('_', ' ', $fatFolderName));
                        if (!str_contains($formattedLabel, 'PHOTO')) {
                            $formattedLabel = 'PHOTO ' . $formattedLabel;
                        }

                        $currentFatSheet->setCellValue('F2', $formattedLabel);
                        $currentFatSheet->setCellValue('F5', $formattedLabel);

                        // Attach Foto
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

            // Simpan dan Unduh File Excel
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
     * Helper Private: Membersihkan foto konten bawaan template tanpa menghapus logo header (baris <= 5)
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
     * Helper Private: Menempelkan foto ke sheet berdasarkan keyword
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

    /**
     * Tampilkan daftar template yang tersimpan di storage/app/templates.
     */
    public function listTemplates()
    {
        $destinationPath = storage_path('app/templates');

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        // Ambil semua file di dalam folder templates
        $files = File::files($destinationPath);
        $templates = [];

        foreach ($files as $file) {
            $templates[] = [
                'name' => $file->getFilename(),
                'size' => round($file->getSize() / 1024, 2) . ' KB',
                'updated_at' => date('Y-m-d H:i:s', $file->getMTime()),
                'path' => $file->getRealPath(),
            ];
        }

        return view('admin.template.index', compact('templates'));
    }

    /**
     * Download template tertentu.
     */
    public function downloadTemplate($filename)
    {
        $filePath = storage_path('app/templates/' . $filename);

        if (!File::exists($filePath)) {
            return back()->with('error', 'File template tidak ditemukan!');
        }

        return response()->download($filePath);
    }
}