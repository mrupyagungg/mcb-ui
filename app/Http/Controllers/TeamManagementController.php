<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TeamManagementController extends Controller
{
    /**
     * Halaman utama Team Management.
     */
    public function index(Request $request)
    {
        $query = Team::with([
            'coordinator',
            'members' => function ($q) {
                $q->with(['user', 'assignedBy'])
                    ->orderBy('tanggal_mulai', 'desc');
            },
        ]);

        // Pencarian tim, koordinator, dan anggota
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhereHas('coordinator', function ($c) use ($search) {
                        $c->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('members.user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter status tim
        if (in_array($request->status, ['aktif', 'nonaktif'])) {
            $query->where('is_active', $request->status === 'aktif');
        }

        // Data tim dengan pagination
        $teams = $query->latest()
            ->paginate(10)
            ->withQueryString();

        // Semua tim aktif untuk pilihan transfer
        $allTeams = Team::where('is_active', true)
            ->orderBy('nama')
            ->get();

        // Data karyawan
        $users = User::orderBy('name')->get();

        // Jabatan yang dapat menjadi koordinator
        $coordinators = User::whereIn('position', [
            'Coord Project',
            'Document Controller',
            'Coord Logistic',
            'Material Control',
        ])->orderBy('name')->get();

        // Statistik jumlah tim aktif
        $totalTimAktif = Team::where('is_active', true)->count();

        // Statistik jumlah karyawan dengan penempatan aktif
        $totalKaryawanAktif = TeamMember::where('status', 'aktif')
            ->distinct('user_id')
            ->count('user_id');

        return view('hrd.team-management', compact(
            'teams',
            'allTeams',
            'users',
            'coordinators',
            'totalTimAktif',
            'totalKaryawanAktif'
        ));
    }

    /**
     * Membuat tim baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'coordinator_id' => [
                'nullable',
                'exists:users,id',
            ],
            'deskripsi' => ['nullable', 'string'],
        ]);

        Team::create([
            'nama' => $validated['nama'],
            'coordinator_id' => $validated['coordinator_id'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('hrd.team-management.index')
            ->with('success', 'Tim berhasil dibuat.');
    }

    /**
     * Memperbarui data tim.
     */
    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'coordinator_id' => [
                'nullable',
                'exists:users,id',
            ],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $team->update([
            'nama' => $validated['nama'],
            'coordinator_id' => $validated['coordinator_id'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        return redirect()
            ->route('hrd.team-management.index')
            ->with('success', 'Data tim berhasil diperbarui.');
    }

    /**
     * Menonaktifkan tim dan seluruh keanggotaan aktifnya.
     * Riwayat penempatan tetap disimpan.
     */
    public function deactivate(Team $team)
    {
        if (!$team->is_active) {
            return back()->with('error', 'Tim sudah dalam keadaan nonaktif.');
        }

        DB::transaction(function () use ($team) {
            $team->members()
                ->where('status', 'aktif')
                ->update([
                    'status' => 'nonaktif',
                    'tanggal_selesai' => now()->toDateString(),
                ]);

            $team->update([
                'is_active' => false,
            ]);
        });

        return redirect()
            ->route('hrd.team-management.index')
            ->with('success', 'Tim dan seluruh penempatan aktifnya berhasil dinonaktifkan.');
    }

    /**
     * Mengaktifkan kembali tim.
     * Riwayat anggota lama tidak otomatis diaktifkan kembali.
     */
    public function activate(Team $team)
    {
        if ($team->is_active) {
            return back()->with('error', 'Tim sudah dalam keadaan aktif.');
        }

        $team->update([
            'is_active' => true,
        ]);

        return redirect()
            ->route('hrd.team-management.index')
            ->with('success', 'Tim berhasil diaktifkan kembali.');
    }

    /**
     * Menghapus tim dan seluruh riwayat keanggotaannya secara permanen.
     * Data karyawan pada tabel users tidak dihapus.
     */
    public function destroy(Team $team)
    {
        DB::transaction(function () use ($team) {
            // Hapus seluruh riwayat anggota tim
            $team->members()->delete();

            // Hapus data tim
            $team->delete();
        });

        return redirect()
            ->route('hrd.team-management.index')
            ->with('success', 'Tim dan seluruh riwayat penempatannya berhasil dihapus permanen.');
    }

    /**
     * Menambahkan karyawan ke tim.
     * Karyawan boleh memiliki beberapa tim aktif.
     */
    public function addMember(Request $request, Team $team)
    {
        abort_unless($team->is_active, 404);

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'region' => ['nullable', 'string', 'max:100'],
            'tanggal_mulai' => ['required', 'date'],
        ]);

        // Periksa apakah sudah menjadi anggota aktif di tim yang sama
        $alreadyMember = TeamMember::where('team_id', $team->id)
            ->where('user_id', $validated['user_id'])
            ->where('status', 'aktif')
            ->exists();

        if ($alreadyMember) {
            return back()
                ->withErrors([
                    'user_id' => 'Karyawan sudah menjadi anggota aktif tim ini.',
                ])
                ->withInput();
        }

        TeamMember::create([
            'team_id' => $team->id,
            'user_id' => $validated['user_id'],
            'region' => $validated['region'] ?? null,
            'assigned_by' => Auth::id(),
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => null,
            'status' => 'aktif',
        ]);

        return redirect()
            ->route('hrd.team-management.index')
            ->with('success', 'Anggota berhasil ditambahkan ke tim.');
    }

    /**
     * Memindahkan satu keanggotaan ke tim lain.
     * Keanggotaan aktif lainnya tidak ikut berubah.
     */
    public function transferMember(Request $request, TeamMember $teamMember)
    {
        if ($teamMember->status !== 'aktif') {
            return back()->with(
                'error',
                'Keanggotaan ini sudah tidak aktif.'
            );
        }

        $tanggalAwal = date(
            'Y-m-d',
            strtotime($teamMember->tanggal_mulai)
        );

        $validated = $request->validate([
            'target_team_id' => [
                'required',
                Rule::exists('teams', 'id')->where(function ($query) {
                    $query->where('is_active', true);
                }),
                Rule::notIn([$teamMember->team_id]),
            ],
            'region' => ['nullable', 'string', 'max:100'],
            'tanggal_mulai' => [
                'required',
                'date',
                'after:' . $tanggalAwal,
            ],
        ]);

        $targetTeam = Team::findOrFail($validated['target_team_id']);

        // Periksa anggota aktif pada tim tujuan
        $duplicate = TeamMember::where('team_id', $targetTeam->id)
            ->where('user_id', $teamMember->user_id)
            ->where('status', 'aktif')
            ->exists();

        if ($duplicate) {
            return back()->withErrors([
                'target_team_id' => 'Karyawan sudah menjadi anggota aktif tim tujuan.',
            ])->withInput();
        }

        DB::transaction(function () use ($teamMember, $targetTeam, $validated) {
            $tanggalMulai = $validated['tanggal_mulai'];

            // Akhiri penempatan lama sehari sebelum penempatan baru
            $teamMember->update([
                'status' => 'nonaktif',
                'tanggal_selesai' => date(
                    'Y-m-d',
                    strtotime($tanggalMulai . ' -1 day')
                ),
            ]);

            // Buat penempatan baru
            TeamMember::create([
                'team_id' => $targetTeam->id,
                'user_id' => $teamMember->user_id,
                'region' => $validated['region'] ?? $teamMember->region,
                'assigned_by' => Auth::id(),
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => null,
                'status' => 'aktif',
            ]);
        });

        return redirect()
            ->route('hrd.team-management.index')
            ->with('success', 'Anggota berhasil dipindahkan ke tim tujuan.');
    }

    /**
     * Mengeluarkan anggota dari satu tim.
     * Penempatan di tim lain tidak terpengaruh.
     */
    public function removeMember(TeamMember $teamMember)
    {
        if ($teamMember->status !== 'aktif') {
            return back()->with(
                'error',
                'Keanggotaan ini sudah tidak aktif.'
            );
        }

        $teamMember->update([
            'status' => 'nonaktif',
            'tanggal_selesai' => now()->toDateString(),
        ]);

        return redirect()
            ->route('hrd.team-management.index')
            ->with('success', 'Anggota berhasil dikeluarkan dari tim.');
    }

    /**
     * Menghapus riwayat penempatan anggota yang sudah nonaktif.
     */
    public function destroyMemberHistory(TeamMember $teamMember)
    {
        try {
            if ($teamMember->status === 'aktif') {
                return back()->with('error', 'Penempatan yang masih aktif tidak dapat dihapus.');
            }

            $id = $teamMember->id;

            // Hapus record secara permanen langsung dari database
            $deleted = DB::table('team_members')
                ->where('id', $id)
                ->delete();

            if (!$deleted) {
                return back()->with('error', 'Data riwayat penempatan tidak ditemukan.');
            }

            return redirect()
                ->route('hrd.team-management.index')
                ->with('success', 'Riwayat penempatan berhasil dihapus permanen.');

        } catch (\Throwable $e) {
            \Log::error('Gagal menghapus riwayat penempatan', [
                'team_member_id' => $teamMember->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Gagal menghapus riwayat: ' . $e->getMessage());
        }
    }
}