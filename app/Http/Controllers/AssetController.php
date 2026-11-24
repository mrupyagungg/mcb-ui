<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AssetController extends Controller
{
    // Dashboard Assets
    public function dashboard()
    {
        $totalAssets = Asset::count();
        $availableAssets = Asset::where('status', 'Available')->count();
        $assignedAssets = Asset::whereNotNull('assigned_to')->count();
        $maintenanceAssets = Asset::where('status', 'Maintenance')->count();

        return view('hrd.assets.dashboard', compact(
            'totalAssets',
            'availableAssets',
            'assignedAssets',
            'maintenanceAssets'
        ));
    }

    // Data Assets
    public function index(Request $request)
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

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter kategori
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Data tabel
        $assets = $query->latest()->paginate(10)->withQueryString();

        // Data kategori
        $categories = Asset::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        // Data karyawan untuk dropdown PIC
        $employees = User::where('role', 'employee')
            ->orderBy('name')
            ->get();

        // Statistik Assets
        $totalAssets = Asset::count();

        $availableAssets = Asset::where('status', 'Available')->count();

        $assignedAssets = Asset::whereNotNull('assigned_to')->count();

        $maintenanceAssets = Asset::where('status', 'Maintenance')->count();

        $damagedAssets = Asset::whereIn('condition', [
            'Damaged',
            'Unrepairable'
        ])->count();

        return view('hrd.assets.index', compact(
            'assets',
            'categories',
            'employees',
            'totalAssets',
            'availableAssets',
            'assignedAssets',
            'maintenanceAssets',
            'damagedAssets'
        ));
    }
    // Form Tambah Assets
    public function create()
    {
        $employees = User::where('role', 'employee')
            ->orderBy('name')
            ->get();

        return view('hrd.assets.create', compact('employees'));
    }

    // Simpan Assets
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_code' => 'required|string|max:50|unique:assets,asset_code',
            'category' => 'required|string|max:100',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:150',
            'serial_number' => 'nullable|string|max:150',
            'assigned_to' => 'nullable|exists:users,id',
            'area' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:150',
            'vendor' => 'nullable|string|max:150',
            'status' => 'required|string|max:50',
            'condition' => 'required|string|max:50',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'acquisition_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'warranty_expiry' => 'nullable|date',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')
                ->store('assets', 'public');
        }

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        Asset::create($validated);

        return redirect()
            ->route('hrd.assets.index')
            ->with('success', 'Data aset berhasil ditambahkan.');
    }

    // Form Edit Assets
    public function edit(Asset $asset)
    {
        $employees = User::where('role', 'employee')
            ->orderBy('name')
            ->get();

        return view('hrd.assets.edit', compact('asset', 'employees'));
    }

    // Update Assets
    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'asset_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('assets', 'asset_code')->ignore($asset->id),
            ],
            'category' => 'required|string|max:100',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:150',
            'serial_number' => 'nullable|string|max:150',
            'assigned_to' => 'nullable|exists:users,id',
            'area' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:150',
            'vendor' => 'nullable|string|max:150',
            'status' => 'required|string|max:50',
            'condition' => 'required|string|max:50',
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

            $validated['photo'] = $request->file('photo')
                ->store('assets', 'public');
        }

        $validated['updated_by'] = Auth::id();

        $asset->update($validated);

        return redirect()
            ->route('hrd.assets.index')
            ->with('success', 'Data aset berhasil diperbarui.');
    }

    // Hapus Assets
    public function destroy(Asset $asset)
    {
        if ($asset->photo) {
            Storage::disk('public')->delete($asset->photo);
        }

        $asset->delete();

        return redirect()
            ->route('hrd.assets.index')
            ->with('success', 'Data aset berhasil dihapus.');
    }
}