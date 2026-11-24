<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;

class HRDController extends Controller
{
    /**
     * Dashboard HRD
     */
    public function dashboard()
    {
        $totalKaryawan = DB::table('users')
            ->where('role', 'employee')
            ->count();

        $totalHadir = DB::table('absensis')
            ->whereDate('tanggal', Carbon::today())
            ->distinct('user_id')
            ->count('user_id');

        $totalTerlambat = DB::table('absensis')
            ->whereDate('tanggal', Carbon::today())
            ->where('status_masuk', 'terlambat')
            ->distinct('user_id')
            ->count('user_id');

        $belumAbsen = max(0, $totalKaryawan - $totalHadir);

        $absensiTerbaru = DB::table('absensis')
            ->join('users', 'absensis.user_id', '=', 'users.id')
            ->select(
                'absensis.*',
                'users.name',
                'users.email'
            )
            ->whereDate('absensis.tanggal', Carbon::today())
            ->orderByDesc('absensis.created_at')
            ->limit(10)
            ->get();

        return view('hrd.dashboard', compact(
            'totalKaryawan',
            'totalHadir',
            'totalTerlambat',
            'belumAbsen',
            'absensiTerbaru'
        ));
    }

    public function importKaryawan(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $spreadsheet = IOFactory::load(
            $request->file('file_excel')->getRealPath()
        );

        $sheet = $spreadsheet->getSheetByName('List Karyawan MCB');

        if (!$sheet) {
            return back()->withErrors([
                'file_excel' => 'Sheet List Karyawan MCB tidak ditemukan.'
            ]);
        }

        $berhasil = 0;
        $dilewati = 0;
        $kredensial = [];
        $employeeIdsTerbaca = [];

        $highestRow = $sheet->getHighestDataRow();

        DB::transaction(function () use ($sheet, $highestRow, &$berhasil, &$dilewati, &$kredensial, &$employeeIdsTerbaca) {
            for ($row = 4; $row <= $highestRow; $row++) {

                // B sampai M
                $name = trim((string) $sheet->getCell("B{$row}")->getFormattedValue());

                if ($name === '') {
                    continue;
                }

                $position = trim((string) $sheet->getCell("C{$row}")->getFormattedValue());
                $vendor = trim((string) $sheet->getCell("D{$row}")->getFormattedValue());
                $region = trim((string) $sheet->getCell("E{$row}")->getFormattedValue());
                $noRekening = trim((string) $sheet->getCell("F{$row}")->getFormattedValue());
                $bank = trim((string) $sheet->getCell("G{$row}")->getFormattedValue());
                $phone = trim((string) $sheet->getCell("H{$row}")->getFormattedValue());
                $idNumber = trim((string) $sheet->getCell("I{$row}")->getFormattedValue());
                $employeeId = trim((string) $sheet->getCell("J{$row}")->getFormattedValue());

                // Tanda "-" dianggap data kosong
                $position = $position === '-' ? null : $position;
                $vendor = $vendor === '-' ? null : $vendor;
                $region = $region === '-' ? null : $region;
                $noRekening = $noRekening === '-' ? null : $noRekening;
                $bank = $bank === '-' ? null : $bank;
                $phone = $phone === '-' ? null : $phone;
                $idNumber = $idNumber === '-' ? null : $idNumber;
                $employeeId = $employeeId === '-' ? null : $employeeId;

                // NIK wajib diisi
                if (empty($idNumber)) {
                    $dilewati++;
                    continue;
                }

                // Generate Employee ID jika Excel kosong atau "-"
                if (empty($employeeId)) {
                    $employeeId = $this->generateEmployeeId();
                }

                // Cegah ID karyawan duplikat
                if (
                    in_array($employeeId, $employeeIdsTerbaca, true) ||
                    User::where('employee_id', $employeeId)->exists()
                ) {
                    $dilewati++;
                    continue;
                }

                $employeeIdsTerbaca[] = $employeeId;

                // Email menggunakan NIK
                $email = $idNumber . '@employee.mcb-id.com';

                // Cegah NIK/email duplikat
                if (
                    User::where('email', $email)->exists() ||
                    User::where('id_number', $idNumber)->exists()
                ) {
                    $dilewati++;
                    continue;
                }

                // Baca tanggal Excel (Kolom K)
                $dateValue = $sheet->getCell("K{$row}")->getValue();
                $startDate = null;

                if ($dateValue !== null && $dateValue !== '' && $dateValue !== '-') {
                    try {
                        if ($dateValue instanceof \DateTimeInterface) {
                            $startDate = Carbon::instance($dateValue)->format('Y-m-d');
                        } elseif (is_numeric($dateValue)) {
                            $startDate = ExcelDate::excelToDateTimeObject(
                                (float) $dateValue
                            )->format('Y-m-d');
                        } else {
                            $startDate = Carbon::parse($dateValue)->format('Y-m-d');
                        }
                    } catch (\Throwable $e) {
                        $startDate = null;
                    }
                }

                // Baca jenis kelamin (Kolom L)
                $jenisKelamin = trim(
                    (string) $sheet->getCell("L{$row}")->getFormattedValue()
                );

                $jenisKelamin = $jenisKelamin === '-' || $jenisKelamin === ''
                    ? null
                    : $jenisKelamin;

                // Normalisasi jenis kelamin
                if ($jenisKelamin !== null) {
                    $jenisKelamin = strtolower($jenisKelamin);

                    if (in_array($jenisKelamin, ['laki-laki', 'laki laki', 'l', 'pria'])) {
                        $jenisKelamin = 'Laki-laki';
                    } elseif (in_array($jenisKelamin, ['perempuan', 'p', 'wanita'])) {
                        $jenisKelamin = 'Perempuan';
                    } else {
                        $jenisKelamin = null;
                    }
                }

                // Baca alamat (Kolom M)
                $alamat = trim(
                    (string) $sheet->getCell("M{$row}")->getFormattedValue()
                );

                $alamat = $alamat === '-' || $alamat === ''
                    ? null
                    : $alamat;

                // Password awal menggunakan NIK
                $password = $idNumber;

                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'role' => 'employee',
                    'position' => $position,
                    'vendor' => $vendor,
                    'region' => $region,
                    'no_rekening' => $noRekening,
                    'bank' => $bank,
                    'phone' => $phone,
                    'id_number' => $idNumber,
                    'employee_id' => $employeeId,
                    'start_date' => $startDate,
                    'jenis_kelamin' => $jenisKelamin,
                    'alamat' => $alamat,
                ]);

                // Simpan kredensial untuk ditampilkan setelah import
                $kredensial[] = [
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                ];

                $berhasil++;
            }
        });

        return redirect()
            ->route('hrd.karyawan')
            ->with('success', "Import selesai. {$berhasil} karyawan berhasil ditambahkan dan {$dilewati} data dilewati.")
            ->with('kredensial_import', $kredensial);
    }

    public function karyawan(Request $request)
    {
        $query = User::where('role', 'employee');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        $karyawan = $query->latest()->paginate(15)->withQueryString();

        $nextEmployeeId = $this->generateEmployeeId();

        return view('hrd.karyawan', compact(
            'karyawan',
            'nextEmployeeId'
        ));
    }

    public function storeKaryawan(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'vendor' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'phone' => 'nullable|string|max:30',
            'id_number' => 'nullable|string|max:100|unique:users,id_number',
            'bank' => 'nullable|string|max:100',
            'no_rekening' => 'nullable|string|max:100',
            'alamat' => 'nullable|string',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        DB::transaction(function () use ($validated) {
            $employeeId = $this->generateEmployeeId();

            User::create([
                'name' => $validated['name'],
                'employee_id' => $employeeId,
                'position' => $validated['position'] ?? null,
                'vendor' => $validated['vendor'] ?? null,
                'region' => $validated['region'] ?? null,
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'id_number' => $validated['id_number'] ?? null,
                'bank' => $validated['bank'] ?? null,
                'no_rekening' => $validated['no_rekening'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'employee',
            ]);
        });

        return redirect()
            ->route('hrd.karyawan')
            ->with('success', 'Data karyawan berhasil ditambahkan.');
    }
    private function generateEmployeeId(): string
    {
        $lastEmployee = User::where('role', 'employee')
            ->whereNotNull('employee_id')
            ->whereRaw("employee_id REGEXP '^[0-9]+$'")
            ->orderByRaw('CAST(employee_id AS UNSIGNED) DESC')
            ->lockForUpdate()
            ->first();

        if ($lastEmployee) {
            $lastId = (string) $lastEmployee->employee_id;
            $nextNumber = (int) $lastId + 1;

            return str_pad(
                (string) $nextNumber,
                strlen($lastId),
                '0',
                STR_PAD_LEFT
            );
        }

        // Jika belum ada Employee ID sama sekali
        return date('Ymd') . '1';
    }
    public function updateKaryawan(Request $request, $id)
    {
        $karyawan = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'employee_id' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('users', 'employee_id')->ignore($karyawan->id),
            ],
            'jenis_kelamin' => 'nullable|string|max:30',
            'position' => 'nullable|string|max:255',
            'vendor' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($karyawan->id),
            ],
            'phone' => 'nullable|string|max:30',
            'id_number' => 'nullable|string|max:100',
            'bank' => 'nullable|string|max:100',
            'no_rekening' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'role' => 'required|in:employee,admin,superadmin,hrd',
        ]);

        $karyawan->update($validated);

        return redirect()->back()->with('success', 'Data karyawan berhasil diperbarui.');
    }
    public function destroyKaryawan($id)
    {
        $karyawan = User::findOrFail($id);

        $karyawan->delete();

        return redirect()->back()->with('success', 'Data karyawan berhasil dihapus.');
    }
    /**
     * Menampilkan seluruh riwayat absensi karyawan
     */
    public function riwayatAbsensi(Request $request)
    {
        $query = DB::table('absensis')
            ->join('users', 'absensis.user_id', '=', 'users.id')
            ->select(
                'absensis.*',
                'users.name',
                'users.email',
                'users.position',
                'users.vendor',
                'users.region'
            );

        // Filter Position
        if ($request->filled('position')) {
            $query->where('users.position', $request->position);
        }

        // Filter Vendor
        if ($request->filled('vendor')) {
            $query->where('users.vendor', $request->vendor);
        }

        // Filter Region
        if ($request->filled('region')) {
            $query->where('users.region', $request->region);
        }

        // Filter Tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('absensis.tanggal', $request->tanggal);
        }

        // Filter Nama atau Email
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        $riwayat = $query
            ->orderByDesc('absensis.tanggal')
            ->orderByDesc('absensis.jam_masuk')
            ->paginate(20)
            ->withQueryString();

        // Data pilihan filter
        $positions = DB::table('users')
            ->where('role', 'employee')
            ->whereNotNull('position')
            ->where('position', '!=', '')
            ->distinct()
            ->orderBy('position')
            ->pluck('position');

        $vendors = DB::table('users')
            ->where('role', 'employee')
            ->whereNotNull('vendor')
            ->where('vendor', '!=', '')
            ->distinct()
            ->orderBy('vendor')
            ->pluck('vendor');

        $regions = DB::table('users')
            ->where('role', 'employee')
            ->whereNotNull('region')
            ->where('region', '!=', '')
            ->distinct()
            ->orderBy('region')
            ->pluck('region');

        return view('hrd.riwayat-absensi', compact(
            'riwayat',
            'positions',
            'vendors',
            'regions'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | ASSETS MANAGEMENT
    |--------------------------------------------------------------------------
    */

    public function assets(Request $request)
    {
        $query = Asset::with('pic');

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('asset_code', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('area', 'like', "%{$search}%")
                    ->orWhereHas('pic', function ($pic) use ($search) {
                        $pic->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter kategori
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter kondisi
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        $assets = $query->latest()->paginate(10)->withQueryString();

        // Statistik
        $totalAssets = Asset::count();

        $availableAssets = Asset::where('status', 'Available')->count();

        $assignedAssets = Asset::whereNotNull('assigned_to')->count();

        $maintenanceAssets = Asset::where('status', 'Maintenance')->count();

        // Data kategori dan karyawan untuk filter/form
        $categories = Asset::whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $employees = User::where('role', 'employee')
            ->orderBy('name')
            ->get();

        return view('hrd.assets', compact(
            'assets',
            'totalAssets',
            'availableAssets',
            'assignedAssets',
            'maintenanceAssets',
            'categories',
            'employees'
        ));
    }

    // Store Asset
    public function storeAsset(Request $request)
    {
        $validated = $request->validate([
            'asset_code' => 'required|string|max:100|unique:assets,asset_code',
            'category' => 'required|string|max:100',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:150',
            'assigned_to' => 'nullable|exists:users,id',
            'area' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'vendor' => 'nullable|string|max:150',
            'status' => 'required|in:Available,Active,Maintenance,Lost,Retired',
            'condition' => 'required|in:Good,Fair,Damaged,Unrepairable',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'acquisition_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'warranty_expiry' => 'nullable|date',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('assets', 'public');
        }

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        Asset::create($validated);

        return redirect()->route('hrd.assets')
            ->with('success', 'Asset berhasil ditambahkan.');
    }

    // Edit Asset
    public function editAsset(Asset $asset)
    {
        $asset->load('pic');

        $employees = User::orderBy('name')->get();

        return view('hrd.assets-edit', compact('asset', 'employees'));
    }

    // Update Asset
    public function updateAsset(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'asset_code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('assets', 'asset_code')->ignore($asset->id),
            ],
            'category' => 'required|string|max:100',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:150',
            'assigned_to' => 'nullable|exists:users,id',
            'area' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'vendor' => 'nullable|string|max:150',
            'status' => 'required|in:Available,Active,Maintenance,Lost,Retired',
            'condition' => 'required|in:Good,Fair,Damaged,Unrepairable',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'acquisition_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'warranty_expiry' => 'nullable|date',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($asset->photo) {
                Storage::disk('public')->delete($asset->photo);
            }

            $validated['photo'] = $request->file('photo')->store('assets', 'public');
        }

        $validated['updated_by'] = Auth::id();

        $asset->update($validated);

        return redirect()->route('hrd.assets')
            ->with('success', 'Asset berhasil diperbarui.');
    }

    // Delete Asset
    public function destroyAsset(Asset $asset)
    {
        if ($asset->photo) {
            Storage::disk('public')->delete($asset->photo);
        }

        $asset->delete();

        return redirect()->route('hrd.assets')
            ->with('success', 'Asset berhasil dihapus.');
    }

    public function importAssets(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            $spreadsheet = IOFactory::load(
                $request->file('file')->getRealPath()
            );

            $sheet = $spreadsheet->getSheetByName('List Asset Laptop MCB');

            if (!$sheet) {
                return back()->with('error', 'Sheet List Asset Laptop MCB tidak ditemukan.');
            }

            $highestRow = $sheet->getHighestRow();

            $berhasil = 0;
            $dilewati = 0;
            $picTidakDitemukan = 0;

            DB::transaction(function () use ($sheet, $highestRow, &$berhasil, &$dilewati, &$picTidakDitemukan) {
                for ($row = 2; $row <= $highestRow; $row++) {

                    $category = trim((string) $sheet->getCell("B{$row}")->getFormattedValue());
                    $brand = trim((string) $sheet->getCell("C{$row}")->getFormattedValue());
                    $model = trim((string) $sheet->getCell("D{$row}")->getFormattedValue());
                    $picName = trim((string) $sheet->getCell("E{$row}")->getFormattedValue());
                    $area = trim((string) $sheet->getCell("F{$row}")->getFormattedValue());
                    $vendor = trim((string) $sheet->getCell("G{$row}")->getFormattedValue());
                    $statusExcel = trim((string) $sheet->getCell("H{$row}")->getFormattedValue());
                    $description = trim((string) $sheet->getCell("I{$row}")->getFormattedValue());
                    $serialNumber = trim((string) $sheet->getCell("K{$row}")->getFormattedValue());
                    $dateValue = $sheet->getCell("L{$row}")->getValue();

                    if ($category === '') {
                        $dilewati++;
                        continue;
                    }

                    // Cari PIC
                    $pic = null;

                    if ($picName !== '') {
                        $pic = User::whereRaw(
                            'LOWER(TRIM(name)) = ?',
                            [mb_strtolower($picName)]
                        )->first();

                        if (!$pic) {
                            $picTidakDitemukan++;
                        }
                    }

                    // Konversi status
                    $statusMap = [
                        'aktif' => 'Active',
                        'available' => 'Available',
                        'tersedia' => 'Available',
                        'maintenance' => 'Maintenance',
                        'perbaikan' => 'Maintenance',
                        'rusak' => 'Maintenance',
                        'nonaktif / rusak' => 'Maintenance',
                        'nonaktif' => 'Maintenance',
                        'hilang' => 'Lost',
                        'nonaktif / hilang' => 'Lost',
                        'pesangon resign' => 'Retired',
                        'retired' => 'Retired',
                    ];

                    $statusKey = mb_strtolower($statusExcel);
                    $status = $statusMap[$statusKey] ?? 'Available';

                    // Konversi tanggal
                    $acquisitionDate = null;

                    if ($dateValue !== null && $dateValue !== '') {
                        try {
                            $acquisitionDate = is_numeric($dateValue)
                                ? Carbon::instance(
                                    ExcelDate::excelToDateTimeObject($dateValue)
                                )->format('Y-m-d')
                                : Carbon::parse($dateValue)->format('Y-m-d');
                        } catch (\Throwable $e) {
                            $acquisitionDate = null;
                        }
                    }

                    // Catatan PIC
                    $notes = null;

                    if ($picName !== '' && !$pic) {
                        $notes = 'PIC Excel: ' . $picName;
                    }

                    if ($statusExcel !== '' && !isset($statusMap[$statusKey])) {
                        $notes = trim(($notes ? $notes . ' | ' : '') .
                            'Status Excel: ' . $statusExcel);
                    }

                    // Generate Asset Code otomatis
                    $assetCode = $this->generateAssetCode();

                    Asset::create([
                        'asset_code' => $assetCode,
                        'category' => $category,
                        'brand' => $brand ?: null,
                        'model' => $model ?: null,
                        'serial_number' => $serialNumber ?: null,
                        'assigned_to' => $pic?->id,
                        'area' => $area ?: null,
                        'vendor' => $vendor ?: null,
                        'status' => $status,
                        'condition' => 'Good',
                        'description' => $description ?: null,
                        'notes' => $notes,
                        'acquisition_date' => $acquisitionDate,
                        'created_by' => Auth::id(),
                    ]);

                    $berhasil++;
                }
            });

            return redirect()
                ->route('hrd.assets')
                ->with('success', "Import berhasil! {$berhasil} asset ditambahkan, {$dilewati} baris dilewati, {$picTidakDitemukan} PIC tidak ditemukan.");

        } catch (\Throwable $e) {
            return back()->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    /**
     * Generate Asset Code otomatis
     * Format: AST-000001
     */
    private function generateAssetCode(): string
    {
        $lastAsset = Asset::where('asset_code', 'like', 'AST-%')
            ->orderByRaw('CAST(SUBSTRING(asset_code, 5) AS UNSIGNED) DESC')
            ->lockForUpdate()
            ->first();

        $nextNumber = $lastAsset
            ? ((int) substr($lastAsset->asset_code, 4)) + 1
            : 1;

        return 'AST-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }


}