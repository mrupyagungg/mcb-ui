<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Sistem Informasi</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body
    class="bg-gradient-to-br from-slate-50 via-indigo-50/30 to-slate-100 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">

    <div
        class="max-w-md w-full space-y-8 bg-white p-8 sm:p-10 rounded-2xl shadow-xl border border-slate-100 relative overflow-hidden">

        <!-- Aksen Dekoratif Modern di atas form -->
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500">
        </div>

        <!-- Header / Judul Halaman -->
        <div class="text-center">
            <div
                class="mx-auto h-12 w-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 mb-4 shadow-inner">
                <!-- Icon User Add -->
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">
                Buat Akun Baru
            </h2>
            <p class="mt-2 text-sm text-slate-500">
                Silakan isi data diri Anda untuk mendaftar ke sistem.
            </p>
        </div>

        <!-- Form Register -->
        <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                <div class="mt-1 relative">
                    <input id="name"
                        class="block w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition duration-150 ease-in-out"
                        type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                        placeholder="Nama Lengkap Anda" />
                </div>
                @error('name')
                    <p class="mt-2 text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                <div class="mt-1 relative">
                    <input id="email"
                        class="block w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition duration-150 ease-in-out"
                        type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                        placeholder="nama@domain.com" />
                </div>
                @error('email')
                    <p class="mt-2 text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                <div class="mt-1 relative">
                    <input id="password"
                        class="block w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition duration-150 ease-in-out"
                        type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                </div>
                @error('password')
                    <p class="mt-2 text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi
                    Password</label>
                <div class="mt-1 relative">
                    <input id="password_confirmation"
                        class="block w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition duration-150 ease-in-out"
                        type="password" name="password_confirmation" required autocomplete="new-password"
                        placeholder="••••••••" />
                </div>
                @error('password_confirmation')
                    <p class="mt-2 text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tombol Submit -->
            <div>
                <button type="submit"
                    class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 ease-in-out">
                    Daftar Sekarang
                </button>
            </div>
        </form>

        <!-- Link Kembali ke Halaman Login -->
        <div class="mt-6 pt-6 border-t border-slate-100 text-center">
            <p class="text-sm text-slate-500">
                Udah punya akun?
                <a href="{{ route('login') }}"
                    class="font-semibold text-indigo-600 hover:text-indigo-500 ml-1 transition-colors">
                    Masuk sini dong !!
                </a>
            </p>
        </div>

        <!-- Footer -->
        <div class="mt-4 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} Sistem Informasi Internal. Hak cipta dilindungi.
        </div>

    </div>

</body>

</html>