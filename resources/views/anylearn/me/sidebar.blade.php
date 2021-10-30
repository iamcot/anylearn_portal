@inject('userService', 'App\Services\UserServices')
<!-- Sidebar -->
@php ( $route = app('router')->getRoutes()->match(app('request'))->getName() )
<ul class="navbar-nav sidebar sidebar-dark accordion d-print-none shadow rounded bg-success me-4 pt-4" id="accordionSidebar">
    <div class="sidebar-heading">
        @lang('Lớp học của tôi')
    </div>
    <li class="nav-item {{ in_array($route, ['me.class', 'me.class.create', 'me.class.edit']) ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('me.class') }}">
            <i class="fas fa-fw fa-university"></i>
            <span>@lang('Quản lý Lớp học')</span></a>
    </li>
    <li class="nav-item {{ in_array($route, ['location', 'location.create', 'location.edit']) ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('location') }}">
            <i class="fas fa-fw fa-map-marker"></i>
            <span>@lang('Sổ địa chỉ/Chi nhánh')</span></a>
    </li>
    <hr class="sidebar-divider d-none d-md-block text-secondary">
    <div class="sidebar-heading">
        @lang('Thông tin của tôi')
    </div>
    <li class="nav-item {{ $route == 'me.edit' ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('me.edit') }}">
            <i class="fas fa-fw fa-user-edit"></i>
            <span>@lang('Thông tin cá nhân')</span></a>
    </li>
    <li class="nav-item {{ $route == 'me.orders' ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('me.orders') }}">
            <i class="fas fa-fw fa-shopping-cart"></i>
            <span>@lang('Khoá học của tôi')</span></a>
    </li>
    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!-- End of Sidebar -->