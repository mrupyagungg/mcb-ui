<aside class="main-sidebar {{ config('adminlte.classes_sidebar', 'sidebar-dark-primary elevation-4') }}">

    {{-- Sidebar Brand Logo --}}
    @if (config('adminlte.logo_img_xl'))
        @include('adminlte::partials.common.brand-logo-xl')
    @else
        @include('adminlte::partials.common.brand-logo-xs')
    @endif

    {{-- Sidebar Content --}}
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column {{ config('adminlte.classes_sidebar_nav', '') }}"
                data-widget="treeview" role="menu" @if (config('adminlte.sidebar_nav_animation_speed') != 300)
                data-animation-speed="{{ config('adminlte.sidebar_nav_animation_speed') }}" @endif
               @if (!config('adminlte.sidebar_nav_accordion')) data-accordion="false" @endif>

                @auth
                    {{-- ========================================== --}}
                    {{-- DASHBOARD (SESUAI ROLE) --}}
                    {{-- ========================================== --}}
                    @php
                        $role = auth()->user()->role;
                        $dashboardRoutes = [
                            'admin' => 'admin.dashboard',
                            'employee' => 'employee.dashboard',
                            'direktur' => 'direktur.dashboard',
                        ];
                        $userDashboard = $dashboardRoutes[$role] ?? null;
                    @endphp

                    @if ($userDashboard)
                        <li class="nav-item">
                            <a href="{{ route($userDashboard) }}"
                                class="nav-link {{ request()->routeIs($userDashboard) ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                    @endif


                    {{-- ========================================== --}}
                    {{-- MENU employee --}}
                    {{-- ========================================== --}}
                    @if ($role === 'employee')
                    @endif

                    {{-- ========================================== --}}
                    {{-- MENU ADMIN --}}
                    {{-- ========================================== --}}
                    @if ($role === 'admin')
                        {{-- ================= MANAJEMEN DOKUMEN ================= --}}
                        <li class="nav-header text-uppercase font-weight-bold text-md tracking-wider text-slate-00 mt-3 px-3">
                            Manajemen Dokumen
                        </li>

                        {{-- Dropdown Treeview: Master Data --}}
                        @php
                            $isMasterDataActive =
                                request()->routeIs('admin.masterdata*') || request()->routeIs('admin.template*');
                        @endphp
                        <li class="nav-item {{ $isMasterDataActive ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link rounded-xl transition-all duration-200 {{ $isMasterDataActive ? 'bg-indigo-600/10 text-indigo-600 font-semibold shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}"
                                style="{{ $isMasterDataActive ? 'background-color: rgba(79, 70, 229, 0.1) !important; color: #4f46e5 !important;' : '' }}">
                                <i
                                    class="nav-icon fas fa-database {{ $isMasterDataActive ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                                <p class="flex items-center justify-between">
                                    <span>Master Data</span>
                                    <i class="right fas fa-angle-left text-xs transition-transform duration-200"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview pl-3 space-y-1 mt-1">
                                <li class="nav-item">
                                    <a href="{{ route('admin.template.index') }}"
                                        class="nav-link rounded-lg transition-all duration-150 {{ request()->routeIs('admin.template.index') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100' }}"
                                        style="{{ request()->routeIs('admin.template.index') ? 'background-color: #4f46e5 !important; color: #ffffff !important;' : '' }}">
                                        <i
                                            class="far fa-circle nav-icon text-xs {{ request()->routeIs('admin.template.index') ? 'text-white' : 'text-amber-500' }}"></i>
                                        <p>Daftar Template</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.template.upload') }}"
                                        class="nav-link rounded-lg transition-all duration-150 {{ request()->routeIs('admin.template.upload') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100' }}"
                                        style="{{ request()->routeIs('admin.template.upload') ? 'background-color: #4f46e5 !important; color: #ffffff !important;' : '' }}">
                                        <i
                                            class="far fa-circle nav-icon text-xs {{ request()->routeIs('admin.template.upload') ? 'text-white' : 'text-emerald-500' }}"></i>
                                        <p>Upload Template</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- Dropdown Treeview: Dokumen (ATP / OPMC) --}}
                        @php
                            $isDocActive = request()->routeIs('admin.cwatp*') || request()->routeIs('admin.opmc*');
                        @endphp
                        <li class="nav-item {{ $isDocActive ? 'menu-open' : '' }} mt-1">
                            <a href="#"
                                class="nav-link rounded-xl transition-all duration-200 {{ $isDocActive ? 'bg-indigo-600/10 text-indigo-600 font-semibold shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}"
                                style="{{ $isDocActive ? 'background-color: rgba(79, 70, 229, 0.1) !important; color: #4f46e5 !important;' : '' }}">
                                <i
                                    class="nav-icon fas fa-folder-open {{ $isDocActive ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                                <p class="flex items-center justify-between">
                                    <span>Dokumen</span>
                                    <i class="right fas fa-angle-left text-xs transition-transform duration-200"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview pl-3 space-y-1 mt-1">
                                <li class="nav-item">
                                    <a href="{{ route('admin.cwatp') }}"
                                        class="nav-link rounded-lg transition-all duration-150 {{ request()->routeIs('admin.cwatp') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100' }}"
                                        style="{{ request()->routeIs('admin.cwatp') ? 'background-color: #4f46e5 !important; color: #ffffff !important;' : '' }}">
                                        <i
                                            class="far fa-circle nav-icon text-xs {{ request()->routeIs('admin.cwatp') ? 'text-white' : 'text-cyan-500' }}"></i>
                                        <p>CWC ATP</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.opmc') }}"
                                        class="nav-link rounded-lg transition-all duration-150 {{ request()->routeIs('admin.opmc') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100' }}"
                                        style="{{ request()->routeIs('admin.opmc') ? 'background-color: #4f46e5 !important; color: #ffffff !important;' : '' }}">
                                        <i
                                            class="far fa-circle nav-icon text-xs {{ request()->routeIs('admin.opmc') ? 'text-white' : 'text-amber-500' }}"></i>
                                        <p>OPMC</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.opmc.reader.index') }}"
                                        class="nav-link rounded-lg transition-all duration-150 {{ request()->routeIs('admin.opmc.reader.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100' }}"
                                        style="{{ request()->routeIs('admin.opmc.reader.*') ? 'background-color: #4f46e5 !important; color: #ffffff !important;' : '' }}">
                                        <i
                                            class="far fa-circle nav-icon text-xs {{ request()->routeIs('admin.opmc.reader.*') ? 'text-white' : 'text-rose-500' }}"></i>
                                        <p>OPMC/OTDR Reader</p>
                                    </a>
                                </li>
                            </ul>
                        </li>


                        {{-- ================= ADMINISTRASI ================= --}}
                        <li class="nav-header text-uppercase font-weight-bold text-md tracking-wider text-slate-400 mt-4 px-3">
                            Administrasi
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.laporan') }}"
                                class="disabled nav-link rounded-xl transition-all duration-150 {{ request()->routeIs('admin.laporan*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100' }}"
                                style="{{ request()->routeIs('admin.laporan*') ? 'background-color: #4f46e5 !important; color: #ffffff !important;' : '' }}">
                                <i
                                    class="nav-icon fas fa-file-alt {{ request()->routeIs('admin.laporan*') ? 'text-white' : 'text-slate-400' }} disabled"></i>
                                <p>Laporan Sistem</p>
                            </a>
                        </li>
                        <!-- Tombol Logout Khusus di Sidebar -->
                        <div class="sidebar-custom px-3 py-2 mb-3">
                            <a href="#"
                                onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();"
                                class="btn btn-secondary btn-block d-flex align-items-center justify-content-center py-2 shadow-sm"
                                style="border-radius: 8px; font-weight: 500;">
                                <i class="fas fa-sign-out-alt mr-2"></i> Sign Out
                            </a>

                            <!-- Form Logout Tersembunyi (Wajib ada untuk metode POST Laravel) -->
                            <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    @endif


                    {{-- ========================================== --}}
                    {{-- MENU DIREKTUR --}}
                    {{-- ========================================== --}}
                    @if ($role === 'direktur')
                        <li class="nav-header text-uppercase font-weight-bold text-muted small mt-2">Eksekutif</li>

                        <li class="nav-item">
                            <a href="{{ route('direktur.monitoring') }}"
                                class="nav-link {{ request()->routeIs('direktur.monitoring*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-chart-line"></i>
                                <p>Monitoring Kerja</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('direktur.laporan') }}"
                                class="nav-link {{ request()->routeIs('direktur.laporan*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-file-invoice"></i>
                                <p>Laporan Eksekutif</p>
                            </a>
                        </li>
                    @endif
                @endauth

            </ul>
        </nav>
    </div>

</aside>