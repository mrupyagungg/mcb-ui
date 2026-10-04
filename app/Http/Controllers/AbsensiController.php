<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    /**
     * Halaman Dashboard Employee
     */
    public function dashboard()
    {
        $today = Carbon::today();
        $startOfMonth = $today->copy()->startOfMonth();

        /*
        |--------------------------------------------------------------------------
        | PENGATURAN POIN
        |--------------------------------------------------------------------------
        */
        $pointTepatWaktu = 1;
        $pointTerlambat = 2;
        $pointTidakAbsen = 5;
        $pointWeekendAbsen = 2;

        /*
        |--------------------------------------------------------------------------
        | SKOR AWAL
        |--------------------------------------------------------------------------
        */
        $attendanceScore = 100;

        /*
        |--------------------------------------------------------------------------
        | AMBIL ABSENSI BULAN INI
        |--------------------------------------------------------------------------
        */
        $absensi = Absensi::where('user_id', Auth::id())
            ->whereBetween('tanggal', [
                $startOfMonth->toDateString(),
                $today->toDateString()
            ])
            ->orderBy('tanggal')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | COUNTER
        |--------------------------------------------------------------------------
        */
        $totalTepatWaktu = 0;
        $totalTerlambat = 0;
        $totalTidakAbsen = 0;
        $totalWeekendMasuk = 0;

        $attendanceWorkdays = 0;
        $attendancePresent = 0;
        $attendanceAbsent = 0;

        /*
        |--------------------------------------------------------------------------
        | TANGGAL TIDAK ABSEN
        |--------------------------------------------------------------------------
        */
        $tanggalTidakAbsen = [];

        /*
        |--------------------------------------------------------------------------
        | CEK SETIAP HARI
        |--------------------------------------------------------------------------
        */
        $date = $startOfMonth->copy();

        while ($date->lte($today)) {

            $tanggal = $date->format('Y-m-d');

            /*
            |--------------------------------------------------------------------------
            | CARI ABSENSI TANGGAL INI
            |--------------------------------------------------------------------------
            */
            $dataAbsensi = $absensi->first(function ($item) use ($tanggal) {
                return Carbon::parse($item->tanggal)->format('Y-m-d') === $tanggal;
            });

            /*
            |--------------------------------------------------------------------------
            | SENIN - JUMAT
            |--------------------------------------------------------------------------
            */
            if (!$date->isWeekend()) {

                // Total hari kerja
                $attendanceWorkdays++;

                /*
                | Tidak absen
                */
                if (!$dataAbsensi) {

                    $attendanceScore -= $pointTidakAbsen;

                    $totalTidakAbsen++;
                    $attendanceAbsent++;

                    $tanggalTidakAbsen[] = [
                        'tanggal' => $date->copy(),
                        'hari' => $date->translatedFormat('l'),
                        'format' => $date->translatedFormat('d F Y'),
                    ];
                }

                /*
                | Ada absensi
                */ else {

                    $attendancePresent++;

                    /*
                    | Tepat waktu
                    */
                    if ($dataAbsensi->status_masuk === 'tepat_waktu') {

                        $attendanceScore += $pointTepatWaktu;

                        $totalTepatWaktu++;
                    }

                    /*
                    | Terlambat
                    */ elseif ($dataAbsensi->status_masuk === 'terlambat') {

                        $attendanceScore -= $pointTerlambat;

                        $totalTerlambat++;
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | SABTU - MINGGU
            |--------------------------------------------------------------------------
            */ else {

                /*
                | Weekend masuk = +2
                */
                if ($dataAbsensi) {

                    $attendanceScore += $pointWeekendAbsen;

                    $totalWeekendMasuk++;
                }

                /*
                | Weekend tidak masuk = 0
                */
            }

            $date->addDay();
        }

        /*
        |--------------------------------------------------------------------------
        | MINIMUM SKOR 0
        |--------------------------------------------------------------------------
        */
        $attendanceScore = max(0, $attendanceScore);

        /*
        |--------------------------------------------------------------------------
        | WARNA SKOR
        |--------------------------------------------------------------------------
        */
        if ($attendanceScore >= 70) {

            $scoreColor = 'success';
            $scoreLabel = 'Baik';

        } elseif ($attendanceScore >= 50) {

            $scoreColor = 'warning';
            $scoreLabel = 'Perlu Perhatian';

        } else {

            $scoreColor = 'danger';
            $scoreLabel = 'Rendah';
        }

        /*
        |--------------------------------------------------------------------------
        | ABSENSI HARI INI
        |--------------------------------------------------------------------------
        */
        $absensiHariIni = Absensi::where('user_id', Auth::id())
            ->whereDate('tanggal', $today->toDateString())
            ->first();

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */
        return view('employee.dashboard', [

            'absensi' => $absensiHariIni,

            'attendanceScore' => $attendanceScore,

            'scoreColor' => $scoreColor,
            'scoreLabel' => $scoreLabel,

            /*
            | Variabel yang dipakai Blade
            */
            'attendancePresent' => $attendancePresent,
            'attendanceWorkdays' => $attendanceWorkdays,
            'attendanceAbsent' => $attendanceAbsent,

            /*
            | Dipakai pada teks "-5 poin per hari"
            */
            'attendanceDeduction' => $pointTidakAbsen,

            /*
            | Detail statistik
            */
            'totalTepatWaktu' => $totalTepatWaktu,
            'totalTerlambat' => $totalTerlambat,
            'totalTidakAbsen' => $totalTidakAbsen,
            'totalWeekendMasuk' => $totalWeekendMasuk,

            /*
            | Daftar tanggal tidak absen
            */
            'tanggalTidakAbsen' => $tanggalTidakAbsen,

            /*
            | Poin
            */
            'pointTepatWaktu' => $pointTepatWaktu,
            'pointTerlambat' => $pointTerlambat,
            'pointTidakAbsen' => $pointTidakAbsen,
            'pointWeekendAbsen' => $pointWeekendAbsen,
        ]);
    }
    /**
     * Halaman Attendance / Absensi
     */
    public function index()
    {
        $absensi = Absensi::where('user_id', Auth::id())
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        return view('employee.attendance', [
            'absensi' => $absensi,
        ]);
    }

    /**
     * Halaman Riwayat Absensi
     */
    public function riwayat(Request $request)
    {
        $bulan = (int) ($request->bulan ?? now()->month);
        $tahunDipilih = (int) ($request->tahun ?? now()->year);

        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA ABSENSI
        |--------------------------------------------------------------------------
        */
        $absensi = Absensi::where('user_id', Auth::id())
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahunDipilih)
            ->orderBy('tanggal', 'desc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | TOTAL ABSENSI
        |--------------------------------------------------------------------------
        */
        $totalAbsensi = $absensi->count();

        /*
        |--------------------------------------------------------------------------
        | STATUS ABSENSI
        |--------------------------------------------------------------------------
        */
        $totalTepatWaktu = $absensi
            ->where('status_masuk', 'tepat_waktu')
            ->count();

        $totalTerlambat = $absensi
            ->where('status_masuk', 'terlambat')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | HITUNG HARI KERJA
        |--------------------------------------------------------------------------
        */
        $startDate = Carbon::create($tahunDipilih, $bulan, 1);

        $endDate = $startDate->copy()->endOfMonth();

        /*
        | Jika bulan yang dipilih adalah bulan berjalan,
        | hanya hitung sampai hari ini.
        */
        if (
            $tahunDipilih === now()->year &&
            $bulan === now()->month
        ) {
            $endDate = Carbon::today();
        }

        $attendanceWorkdays = 0;

        $date = $startDate->copy();

        while ($date->lte($endDate)) {

            if (!$date->isWeekend()) {
                $attendanceWorkdays++;
            }

            $date->addDay();
        }

        /*
        |--------------------------------------------------------------------------
        | HITUNG TIDAK ABSEN
        |--------------------------------------------------------------------------
        |
        | Hanya Senin-Jumat yang dihitung sebagai tidak absen.
        | Sabtu-Minggu tidak masuk perhitungan.
        |
        */
        $attendanceAbsent = max(
            0,
            $attendanceWorkdays - $totalAbsensi
        );

        $totalTidakAbsen = $attendanceAbsent;

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */
        return view('employee.riwayat', compact(
            'absensi',
            'bulan',
            'tahunDipilih',
            'totalAbsensi',
            'totalTepatWaktu',
            'totalTerlambat',
            'totalTidakAbsen',
            'attendanceWorkdays',
            'attendanceAbsent'
        ));
    }

    /**
     * Absen Masuk
     */
    public function masuk(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'jalan' => 'nullable|string',
            'kota' => 'nullable|string',
            'photo' => 'required|string',
        ]);

        $now = now();
        $tanggal = $now->toDateString();

        // Cek apakah sudah absen masuk hari ini
        $existing = Absensi::where('user_id', Auth::id())
            ->where('tanggal', $tanggal)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absen masuk hari ini.'
            ], 422);
        }

        // Batas masuk pukul 10:00 WIB
        $status = $now->format('H:i:s') > '10:00:00'
            ? 'terlambat'
            : 'tepat_waktu';

        // Proses foto
        $photoData = $request->photo;

        if (preg_match('/^data:image\/(\w+);base64,/', $photoData, $type)) {
            $photoData = substr(
                $photoData,
                strpos($photoData, ',') + 1
            );

            $extension = strtolower($type[1]);

            if ($extension === 'jpeg') {
                $extension = 'jpg';
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Format foto tidak valid.'
            ], 422);
        }

        $photoData = base64_decode($photoData);

        if ($photoData === false) {
            return response()->json([
                'success' => false,
                'message' => 'Foto tidak dapat diproses.'
            ], 422);
        }

        // Nama file foto masuk
        $fileName = 'absensi/masuk/' .
            Auth::id() . '_' .
            $now->format('Ymd_His') . '.' .
            $extension;

        // Simpan foto
        Storage::disk('public')->put(
            $fileName,
            $photoData
        );

        // Simpan data absensi
        $absensi = Absensi::create([
            'user_id' => Auth::id(),
            'tanggal' => $tanggal,
            'jam_masuk' => $now->format('H:i:s'),
            'status_masuk' => $status,
            'latitude_masuk' => $request->latitude,
            'longitude_masuk' => $request->longitude,
            'jalan_masuk' => $request->jalan,
            'kota_masuk' => $request->kota,
            'foto_masuk' => $fileName,
        ]);

        return response()->json([
            'success' => true,
            'message' => $status === 'terlambat'
                ? 'Absen masuk berhasil. Anda tercatat TERLAMBAT.'
                : 'Absen masuk berhasil. Selamat bekerja!',
            'status' => $status,
            'jam_masuk' => $absensi->jam_masuk,
        ]);
    }

    /**
     * Absen Keluar
     */
    public function keluar(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'jalan' => 'nullable|string',
            'kota' => 'nullable|string',
            'photo' => 'required|string',
        ]);

        $now = now();
        $tanggal = $now->toDateString();

        // Cari absensi hari ini
        $absensi = Absensi::where('user_id', Auth::id())
            ->where('tanggal', $tanggal)
            ->first();

        // Belum absen masuk
        if (!$absensi) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan absen masuk.'
            ], 422);
        }

        // Sudah absen keluar
        if ($absensi->jam_keluar) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absen keluar hari ini.'
            ], 422);
        }

        // Proses foto
        $photoData = $request->photo;

        if (preg_match('/^data:image\/(\w+);base64,/', $photoData, $type)) {
            $photoData = substr(
                $photoData,
                strpos($photoData, ',') + 1
            );

            $extension = strtolower($type[1]);

            if ($extension === 'jpeg') {
                $extension = 'jpg';
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Format foto tidak valid.'
            ], 422);
        }

        $photoData = base64_decode($photoData);

        if ($photoData === false) {
            return response()->json([
                'success' => false,
                'message' => 'Foto tidak dapat diproses.'
            ], 422);
        }

        // Nama file foto keluar
        $fileName = 'absensi/keluar/' .
            Auth::id() . '_' .
            $now->format('Ymd_His') . '.' .
            $extension;

        // Simpan foto
        Storage::disk('public')->put(
            $fileName,
            $photoData
        );

        // Update absensi
        $absensi->update([
            'jam_keluar' => $now->format('H:i:s'),
            'latitude_keluar' => $request->latitude,
            'longitude_keluar' => $request->longitude,
            'jalan_keluar' => $request->jalan,
            'kota_keluar' => $request->kota,
            'foto_keluar' => $fileName,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absen keluar berhasil. Terima kasih.',
            'jam_keluar' => $absensi->jam_keluar,
        ]);
    }
}