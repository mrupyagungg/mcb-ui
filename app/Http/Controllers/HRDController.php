<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
                // B sampai K
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

                // Cegah ID karyawan duplikat
                if ($employeeId) {
                    if (
                        in_array($employeeId, $employeeIdsTerbaca, true) ||
                        User::where('employee_id', $employeeId)->exists()
                    ) {
                        $dilewati++;
                        continue;
                    }

                    $employeeIdsTerbaca[] = $employeeId;
                }

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

                // Baca tanggal Excel
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

        return view('hrd.karyawan', compact('karyawan'));
    }

    public function storeKaryawan(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'employee',
        ]);

        return redirect()
            ->route('hrd.karyawan')
            ->with('success', 'Akun karyawan berhasil dibuat.');
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
                'users.email'
            );

        // Filter tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('absensis.tanggal', $request->tanggal);
        }

        // Filter nama karyawan
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

        return view('hrd.riwayat-absensi', compact('riwayat'));
    }

    public function assets()
    {
        return view('hrd.assets');
    }
}