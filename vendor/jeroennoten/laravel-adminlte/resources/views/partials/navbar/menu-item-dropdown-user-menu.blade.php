@php( $logout_url = View::getSection('logout_url') ?? config('adminlte.logout_url', 'logout') )
@php( $profile_url = View::getSection('profile_url') ?? config('adminlte.profile_url', 'logout') )

@if (config('adminlte.usermenu_profile_url', false))
@php(    $profile_url = Auth::user()->adminlte_profile_url() )
@endif

@if (config('adminlte.use_route_url', false))
@php(    $profile_url = $profile_url ? route($profile_url) : '' )
@php(    $logout_url = $logout_url ? route($logout_url) : '' )
@else
@php(    $profile_url = $profile_url ? url($profile_url) : '' )
@php(    $logout_url = $logout_url ? url($logout_url) : '' )
@endif

<li class="nav-item dropdown user-menu">

    {{-- User menu toggler --}}
    <a href="#" class="nav-link dropdown-toggle d-flex align-items-center py-0 px-2" data-toggle="dropdown"
        style="height: 50px;">

        {{-- Menggunakan Accessor User->initials --}}
        <div class="user-image img-circle elevation-1 d-flex align-items-center justify-content-center text-white font-weight-bold mr-2 shadow-sm"
            style="width: 34px; height: 34px; background: linear-gradient(135deg, #363636 0%, #646565 100%); font-size: 13px; letter-spacing: 0.5px;">
            {{ Auth::user()->initials }}
        </div>
        {{--
        <span class="font-weight-semibold text-sm d-none d-md-inline text-dark">
            {{ Auth::user()->name }}
        </span> --}}
        {{-- <i class="fas fa-chevron-down ml-2 text-xs text-muted d-none d-md-inline"></i> --}}
    </a>

    {{-- User menu dropdown --}}
    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right border-0 shadow-lg rounded-xl overflow-hidden mt-2"
        style="min-width: 260px;">

        {{-- User menu header --}}
        @if(!View::hasSection('usermenu_header') && config('adminlte.usermenu_header'))
            <li class="user-header text-white text-center py-4 px-3"
                style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);">

                <div class="mb-2 d-inline-flex align-items-center justify-content-center bg-white text-indigo-600 rounded-circle shadow-md font-weight-bold"
                    style="width: 65px; height: 65px; font-size: 24px;">
                    {{ Auth::user()->initials }}
                </div>

                <p class="mb-0 font-weight-bold text-base shadow-sm mt-1">
                    {{ Auth::user()->name }}
                    @if(config('adminlte.usermenu_desc'))
                        <span
                            class="d-block font-weight-normal text-xs opacity-80 mt-1">{{ Auth::user()->adminlte_desc() }}</span>
                    @endif
                </p>
            </li>
        @else
            @yield('usermenu_header')
        @endif

        {{-- Configured user menu links --}}
        @each('adminlte::partials.navbar.dropdown-item', $adminlte->menu("navbar-user"), 'item')

        {{-- User menu body --}}
        @hasSection('usermenu_body')
            <li class="user-body px-3 py-2 border-bottom">
                @yield('usermenu_body')
            </li>
        @endif

        {{-- User menu footer --}}
        <li class="user-footer bg-light p-3 d-flex align-items-center justify-between">
            @if($profile_url)
                <a href="{{ $profile_url }}"
                    class="btn btn-default btn-flat btn-sm px-3 font-weight-medium text-secondary shadow-sm rounded-lg d-flex align-items-center">
                    <i class="fa fa-user-circle mr-2 text-primary"></i>
                    {{ __('adminlte::menu.profile') }}
                </a>
            @endif

            <a class="btn btn-danger btn-flat btn-sm px-3 font-weight-medium shadow-sm rounded-lg d-flex align-items-center ml-auto @if(!$profile_url) btn-block justify-content-center @endif"
                href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa fa-power-off mr-2"></i>
                {{ __('adminlte::adminlte.log_out') }}
            </a>

            <form id="logout-form" action="{{ $logout_url }}" method="POST" style="display: none;">
                @if(config('adminlte.logout_method'))
                    {{ method_field(config('adminlte.logout_method')) }}
                @endif
                {{ csrf_field() }}
            </form>
        </li>

    </ul>

</li>