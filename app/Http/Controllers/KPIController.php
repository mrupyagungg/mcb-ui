<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KPIIndicator;
use Illuminate\Validation\Rule;

class KPIController extends Controller
{
    public function dashboard()
    {
        return view('hrd.kpi.dashboard');
    }

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

    public function storeIndikator(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'kategori' => [
                'required',
                Rule::in(['dokumen', 'lapangan', 'drafter', 'koordinator']),
            ],
            'jenis' => [
                'required',
                Rule::in(['target', 'rubrik']),
            ],
            'bobot' => 'required|numeric|min:0.01|max:100',
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        $total = KPIIndicator::where('kategori', $data['kategori'])
            ->sum('bobot');

        if (($total + $data['bobot']) > 100) {
            return back()
                ->withInput()
                ->withErrors([
                    'bobot' => 'Total bobot kategori ini tidak boleh melebihi 100%. Sisa bobot: ' . number_format(100 - $total, 2) . '%.'
                ]);
        }

        $data['is_active'] = $request->boolean('is_active');

        KPIIndicator::create($data);

        return redirect()
            ->route('hrd.kpi.indikator')
            ->with('success', 'Indikator KPI berhasil ditambahkan.');
    }

    public function updateIndikator(Request $request, $id)
    {
        $indikator = KPIIndicator::findOrFail($id);

        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'kategori' => [
                'required',
                Rule::in(['dokumen', 'lapangan', 'drafter', 'koordinator']),
            ],
            'jenis' => [
                'required',
                Rule::in(['target', 'rubrik']),
            ],
            'bobot' => 'required|numeric|min:0.01|max:100',
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        $totalLain = KPIIndicator::where('kategori', $data['kategori'])
            ->where('id', '!=', $id)
            ->sum('bobot');

        if (($totalLain + $data['bobot']) > 100) {
            return back()
                ->withInput()
                ->withErrors([
                    'bobot' => 'Total bobot kategori ini tidak boleh melebihi 100%. Sisa bobot: ' . number_format(100 - $totalLain, 2) . '%.'
                ]);
        }

        $data['is_active'] = $request->boolean('is_active');

        $indikator->update($data);

        return redirect()
            ->route('hrd.kpi.indikator')
            ->with('success', 'Indikator KPI berhasil diperbarui.');
    }

    public function penilaian()
    {
        $employees = \App\Models\User::orderBy('name')->get();

        $periods = \DB::table('kpi_periods')
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

    public function destroyIndikator($id)
    {
        $indikator = KPIIndicator::findOrFail($id);

        $indikator->delete();

        return redirect()
            ->route('hrd.kpi.indikator')
            ->with('success', 'Indikator KPI berhasil dihapus.');
    }
}