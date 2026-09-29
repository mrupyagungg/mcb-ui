<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi</title>
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
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                    </path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">
                Selamat Datang Kembali
            </h2>
            <p class="mt-2 text-sm text-slate-500">
                Silakan masukkan akun Anda untuk mengakses sistem.
            </p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="mb-4 text-sm font-medium text-green-600 bg-green-50 p-3 rounded-lg border border-green-200">
                {{ session('status') }}
            </div>
        @endif

        <!-- Form Login -->
        <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                <div class="mt-1 relative">
                    <input id="email"
                        class="block w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition duration-150 ease-in-out"
                        type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        placeholder="nama@domain.com" />
                </div>
                @error('email')
                    <p class="mt-2 text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                    @if (Route::has('password.request'))
                        <a class="text-xs font-medium text-indigo-600 hover:text-indigo-500 transition-colors"
                            href="{{ route('password.request') }}">
                            Lupa password?
                        </a>
                    @endif
                </div>
                <div class="mt-1 relative">
                    <input id="password"
                        class="block w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition duration-150 ease-in-out"
                        type="password" name="password" required autocomplete="current-password"
                        placeholder="••••••••" />
                </div>
                @error('password')
                    <p class="mt-2 text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox"
                        class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-4 h-4"
                        name="remember">
                    <span class="ms-2 text-sm text-slate-600 select-none">Ingat saya</span>
                </label>
            </div>

            <!-- Tombol Submit Login -->
            <div>
                <button type="submit"
                    class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 ease-in-out">
                    Masuk ke Sistem
                </button>
            </div>
        </form>

        <!-- Bagian Tombol / Link Registrasi -->
        @if (Route::has('register'))
            <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                <p class="text-sm text-slate-500">
                    Belum punya akun?
                    <a href="{{ route('register') }}"
                        class="font-semibold text-indigo-600 hover:text-indigo-500 ml-1 transition-colors">
                        Daftar dulu dong !!
                    </a>
                </p>
            </div>
        @endif

        <!-- Footer -->
        <div class="mt-4 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} Sistem Informasi Internal. Hak cipta dilindungi.
        </div>

    </div>

</body>

</html>