@inject('userService', 'App\Services\UserServices')
<!-- Sidebar -->
@php ( $route = app('router')->getRoutes()->match(app('request'))->getName() )
<ul class="navbar-nav sidebar sidebar-light accordion d-print-none shadow rounded bg-white me-4" id="accordionSidebar">
    
    <a class="sidebar-brand d-flex align-items-center justify-content-center mb-3" href="{{ route('me.dashboard') }}">
        <div class="sidebar-brand-icon p-3">
            <img src="/cdn/img/logo.png" alt="" class="img-fluid">
        </div>
        <div class="sidebar-brand-text mx-3 text-success">anyLEARN</div>
    </a>
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
            <span>@lang('Đơn hàng của tôi')</span></a>
    </li>
    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!-- End of Sidebar -->