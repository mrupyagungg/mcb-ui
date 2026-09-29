<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\File; // Pastikan File facade di-import

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil data user
        $users = User::orderBy('last_seen_at', 'desc')
            ->take(15) // Batasi jika ingin menampilkan 5 user saja
            ->get();

        // 2. Ambil data template dari storage/app/templates
        $destinationPath = storage_path('app/templates');

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

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

        // 3. Kirim KEDUANYA ke view dashboard
        return view('admin.dashboard', compact('users', 'templates'));
    }

}