<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use App\Models\KPIIndicator;
use App\Models\User;

class KPIController extends Controller
{
    /**
     * KATEGORI KPI MCB
     */
    private const KATEGORI = [
        'dokumen',
        'drafter',
        'permit',
        'waspang',
        'logistic',
        'admin',
    ];

    /**
     * DASHBOARD KPI
     */
    public function dashboard()
    {
        return view('hrd.kpi.dashboard');
    }

    /**
     * HALAMAN INDIKATOR KPI
     */
    public function indikator()
    {
        $indikators = KPIIndicator::orderBy('kategori')
            ->orderBy('nama')
            ->get();

        $totalBobot = KPIIndicator::selectRaw('kategori, SUM(bobot) as total')
            ->groupBy('kategori')
            ->pluck('total', 'kategori');

        return view('hrd.kpi.indikator', compact(
            'indikators',
            'totalBobot'
        ));
    }

    /**
     * SIMPAN INDIKATOR KPI
     */
    public function storeIndikator(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',

            'kategori' => [
                'required',
                Rule::in(self::KATEGORI),
            ],

            'jenis' => [
                'required',
                Rule::in(['target', 'rubrik']),
            ],

            'bobot' => 'required|numeric|min:0.01|max:100',
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($request, $data) {

            $total = KPIIndicator::where('kategori', $data['kategori'])
                ->lockForUpdate()
                ->sum('bobot');

            $sisa = 100 - (float) $total;

            if ((float) $data['bobot'] > $sisa + 0.000001) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'bobot' => 'Total bobot kategori '
                            . strtoupper($data['kategori'])
                            . ' tidak boleh melebihi 100%. Sisa bobot: '
                            . number_format(max(0, $sisa), 2)
                            . '%.'
                    ]);
            }

            $data['is_active'] = $request->boolean('is_active');

            KPIIndicator::create($data);

            return redirect()
                ->route('hrd.kpi.indikator')
                ->with('success', 'Indikator KPI berhasil ditambahkan.');
        });
    }

    /**
     * UPDATE INDIKATOR KPI
     */
    public function updateIndikator(Request $request, $id)
    {
        $indikator = KPIIndicator::findOrFail($id);

        $data = $request->validate([
            'nama' => 'required|string|max:150',

            'kategori' => [
                'required',
                Rule::in(self::KATEGORI),
            ],

            'jenis' => [
                'required',
                Rule::in(['target', 'rubrik']),
            ],

            'bobot' => 'required|numeric|min:0.01|max:100',
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use (
            $request,
            $data,
            $indikator,
            $id
        ) {

            $totalLain = KPIIndicator::where('kategori', $data['kategori'])
                ->where('id', '!=', $id)
                ->lockForUpdate()
                ->sum('bobot');

            $sisa = 100 - (float) $totalLain;

            if ((float) $data['bobot'] > $sisa + 0.000001) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'bobot' => 'Total bobot kategori '
                            . strtoupper($data['kategori'])
                            . ' tidak boleh melebihi 100%. Sisa bobot: '
                            . number_format(max(0, $sisa), 2)
                            . '%.'
                    ]);
            }

            $data['is_active'] = $request->boolean('is_active');

            $indikator->update($data);

            return redirect()
                ->route('hrd.kpi.indikator')
                ->with('success', 'Indikator KPI berhasil diperbarui.');
        });
    }

    /**
     * HALAMAN PENILAIAN KPI
     */
    public function penilaian()
    {
        $employees = User::orderBy('name')->get();

        $periods = DB::table('kpi_periods')
            ->where('is_active', 1)
            ->orderByDesc('id')
            ->get();

        $indikators = KPIIndicator::where('is_active', true)
            ->orderBy('kategori')
            ->orderBy('nama')
            ->get();

        return view('hrd.kpi.penilaian', compact(
            'employees',
            'periods',
            'indikators'
        ));
    }

    /**
     * HAPUS INDIKATOR KPI
     */
    public function destroyIndikator($id)
    {
        $indikator = KPIIndicator::findOrFail($id);

        // Jangan hapus indikator jika sudah digunakan
        // dalam detail penilaian KPI.
        if (
            Schema::hasTable('kpi_assessment_details') &&
            DB::table('kpi_assessment_details')
                ->where('indicator_id', $id)
                ->exists()
        ) {
            return redirect()
                ->route('hrd.kpi.indikator')
                ->withErrors([
                    'indikator' => 'Indikator sudah digunakan dalam penilaian KPI. '
                        . 'Silakan nonaktifkan indikator, bukan menghapusnya.'
                ]);
        }

        $indikator->delete();

        return redirect()
            ->route('hrd.kpi.indikator')
            ->with('success', 'Indikator KPI berhasil dihapus.');
    }
}