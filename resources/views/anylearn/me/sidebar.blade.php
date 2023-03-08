@inject('userService', 'App\Services\UserServices')
<!-- Sidebar -->
@php(
    $route = app('router')->getRoutes()->match(app('request'))->getName()
)
@php($role = Auth::user()->role)
<ul class="navbar-nav sidebar sidebar-dark accordion d-print-none shadow rounded bg-success me-4 pt-4"
    id="accordionSidebar">
    @if (Auth::user()->role == 'school' || Auth::user()->role == 'teacher')
        <li class="nav-item {{ in_array($route, ['me.dashboard']) ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('me.dashboard') }}" data-spm="nav.dashboard">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>@lang('Tổng quan')</span></a>
        </li>
        <div class="sidebar-heading">
            @lang('Chức năng đối tác')
        </div>
        <li
            class="nav-item {{ in_array($route, ['location', 'location.create', 'location.edit', 'me.contract', 'helpcenter.parnter.index', 'me.transactionhistory','me.class', 'me.class.create', 'me.class.edit','me.introduce']) ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" data-bs-target="#home-collapse" aria-expanded="false">
                <i class="fas fa-fw fa-book"></i>
                <span>@lang('QUẢN LÝ TUYỂN SINH')</span>
            </a>
            <div class="collapse {{ in_array($route, ['location', 'location.create', 'location.edit', 'me.contract', 'helpcenter.parnter.index', 'me.transactionhistory','me.class', 'me.class.create', 'me.class.edit','me.introduce']) ? 'show' : '' }}"
                id="home-collapse" style="">
                <div class="bg-gray-300 py-2 collapse-inner rounded">
                    {{-- <a class="collapse-item {{ in_array($route, ['location', 'location.create', 'location.edit']) ? 'active' : '' }}"
                        href="{{ route('location') }}">
                        <i class="fas fa-fw fa-fire"></i>
                        <span>Quản lý chi nhánh</span>
                    </a> --}}
                    <a class="collapse-item {{ in_array($route, ['me.introduce','location.create', 'location.edit']) ? 'active' : '' }}"
                        href="{{ route('me.introduce') }}">
                        <i class="fas fa-fw fa-info-circle"></i>
                        <span>Giới thiệu</span>
                    </a>
                    <a class="collapse-item {{ in_array($route, ['me.contract']) ? 'active' : '' }}"
                        href="{{ route('me.contract') }}">
                        <i class="far fa-fw fa-sun"></i>
                        <span>Hợp Đồng</span>
                    </a>
                    <a class="collapse-item {{ in_array($route, ['me.transactionhistory']) ? 'active' : '' }}"
                        href="{{ route('me.transactionhistory') }}">
                        <i class="far fa-fw fa-sun"></i>
                        <span>Lịch Sử Giao Dịch</span>
                    </a>
                    <a class="collapse-item {{ in_array($route, ['me.class', 'me.class.create', 'me.class.edit']) ? 'active' : '' }}"
                        href="{{ route('me.class') }}">
                        <i class="fas fa-fw fa-university"></i>
                        <span>Lớp Học Của Tôi</span>
                    </a>
                    <a class="collapse-item {{ in_array($route, ['helpcenter.parnter.index']) ? 'active' : '' }}"
                        href="{{ route('helpcenter.parnter.index') }}">
                        <i class="fas fa-fw fa-headset"></i>
                        <span>Trung Tâm Hỗ Trợ</span>
                    </a>
                </div>
            </div>
        </li>
        {{-- <li class="nav-item {{ in_array($route, ['me.class', 'me.class.create', 'me.class.edit']) ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('me.class') }}" data-spm="nav.class">
                <i class="fas fa-fw fa-university"></i>
                <span>@lang('Lớp học của tôi')</span>
            </a>
        </li> --}}
        {{-- <li class="nav-item {{ in_array($route, ['location', 'location.create', 'location.edit']) ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('location') }}" data-spm="nav.locations">
            <i class="fas fa-fw fa-map-marker"></i>
            <span>@lang('Quản lý chi nhánh')</span>
        </a>
    </li> --}}
        {{-- <li class="nav-item {{ in_array($route, ['me.contract']) ? 'active' : '' }}" data-spm="nav.contract">
        <a class="nav-link" href="{{ route('me.contract') }}">
            <i class="far fa-fw fa-sun"></i>
            <span>@lang('Quản lý hợp đồng')</span></a>
    </li> --}}
        {{-- <li class="nav-item {{ in_array($route, ['me.certificate']) ? 'active' : '' }}" data-spm="nav.cert">
            <a class="nav-link" href="{{ route('me.certificate') }}">
                <i class="fas fa-fw fa-scroll"></i>
                <span>@lang('Quản lý chứng chỉ')</span></a>
        </li> --}}
        <li class="nav-item {{ in_array($route, ['me.finance']) ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('me.finance') }}">
                <i class="fas fa-fw fa-money-bill"></i>
                <span>@lang('Quản lý tài chính')</span></a>
        </li>
        <hr class="sidebar-divider d-none d-md-block text-secondary">
    @endif
    <div class="sidebar-heading">
        @lang('Thông tin cơ bản')
    </div>
    <li class="nav-item {{ in_array($route, ['me.profile','me.friend','me.edit']) ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('me.profile') }}" data-spm="nav.edit">
            <i class="fas fa-fw fa-user-edit"></i>
            <span>@lang('Thông tin chung')</span></a>
    </li>
    <li class="nav-item {{ $route == 'me.orders' ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('me.orders') }}" data-spm="nav.orders">
            <i class="fas fa-fw fa-calendar"></i>
            <span>@lang('Khoá học tôi tham gia')</span></a>
    </li>
    <li class="nav-item {{ $route == 'me.pendingorders' ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('me.pendingorders') }}" data-spm="nav.pending_orders">
            <i class="fas fa-fw fa-shopping-cart"></i>
            <span>@lang('Chờ thanh toán')</span></a>
    </li>
    <li class="nav-item {{ $route == 'me.resetpassword' ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('me.resetpassword') }}" data-spm="nav.resetpass">
            <i class="fas fa-fw fa-lock"></i>
            <span>@lang('Đổi mật khẩu')</span></a>
    </li>
    <li class="nav-item {{ $route == 'me.child' ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('me.child') }}" data-spm="nav.child">
            <i class="fas fa-fw fa-child"></i>
            <span>@lang('Quản lý tài khoản con')</span></a>
    </li>
    <li class="nav-item {{ $route == 'me.history' ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('me.history') }}" data-spm="nav.history">
            <i class="fas fa-fw fa-wallet"></i>
            <span>@lang('Giao dịch của tôi')</span></a>
    </li>
    @if (Auth::user()->role == 'school' || Auth::user()->role == 'teacher')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('helpcenter.parnter.index') }}" data-spm="nav.class">
                <i class="fas fa-fw fa-headset"></i>
                <span>@lang('Trung tâm hỗ trợ')</span></a>
        </li>
    @endif
    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!-- End of Sidebar -->
