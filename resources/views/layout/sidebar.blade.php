@inject('userService', 'App\Services\UserServices')
<!-- Sidebar -->
@php ( $route  = app('router')->getRoutes()->match(app('request'))->getName() )
<ul class="navbar-nav bg-gradient-{{ env('MAIN_COLOR', 'primary') }} sidebar sidebar-dark accordion d-print-none" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-book-open"></i>
            <!-- <i class="fas fa-gem"></i> -->
        </div>
        <div class="sidebar-brand-text mx-3">{{ env('APP_NAME') }}</div>
    </a>
    @if($userService->haveAccess(Auth::user()->role, 'course'))
    <hr class="sidebar-divider d-none d-md-block">
    <div class="sidebar-heading">
        @lang('Quản lý khóa/lớp học')
    </div>
    <li class="nav-item {{ in_array($route, ['course', 'course.create', 'course.edit']) ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('course') }}">
            <i class="fas fa-fw fa-chalkboard-teacher"></i>
            <span>@lang('Chỉnh sửa Khóa học')</span></a>
    </li>
    <li class="nav-item {{ in_array($route, ['class', 'class.create', 'class.edit']) ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('class') }}">
            <i class="fas fa-fw fa-university"></i>
            <span>@lang('Chỉnh sửa Lớp học')</span></a>
    </li>
    <li class="nav-item {{ $route == 'confirm' ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('confirm') }}">
            <i class="fas fa-fw fa-check-double"></i>
            <span>@lang('Xác nhận tham gia')</span></a>
    </li>
    @endif
    @if($userService->haveAccess(Auth::user()->role, 'school'))
    <hr class="sidebar-divider d-none d-md-block">
    <div class="sidebar-heading">
        @lang('Quản lý sản phẩm')
    </div>
    <li class="nav-item {{ $route == 'product' ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('product') }}">
            <i class="fas fa-fw fa-boxes"></i>
            <span>@lang('Chỉnh sửa sản phẩm')</span></a>
    </li>
    <li class="nav-item {{ $route == 'order' ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('order') }}">
            <i class="fas fa-fw fa-shopping-cart"></i>
            <span>@lang('Quản lý đơn hàng')</span></a>
    </li>
    @endif
    @if($userService->haveAccess(Auth::user()->role, 'admin'))
    <hr class="sidebar-divider d-none d-md-block">
    <div class="sidebar-heading">
        @lang('Quản lý người dùng') 
    </div>
   
    <li class="nav-item {{ strpos($route, 'user.members') !== false ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('user.members') }}">
            <i class="fas fa-fw fa-users-cog"></i>
            <span>@lang('Duyệt thành viên')</span></a>
    </li> 
    @if($userService->haveAccess(Auth::user()->role, 'root'))
    <li class="nav-item {{ strpos($route, 'user.mods') !== false ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('user.mods') }}">
            <i class="fas fa-fw fa-user-shield"></i>
            <span>@lang('Quản lý Mods')</span></a>
    </li>
    @endif

    <hr class="sidebar-divider d-none d-md-block">
    <div class="sidebar-heading">
        @lang('Quản lý hệ thống')
    </div>
    <li class="nav-item {{ $route == 'config.site' ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('config.site') }}">
            <i class="fas fa-fw fa-cogs"></i>
            <span>@lang('Các thông số')</span></a>
    </li>
    <li class="nav-item {{ $route == 'config.banner' ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('config.banner') }}">
            <i class="fas fa-fw fa-images"></i>
            <span>@lang('Banners')</span></a>
    </li>
    <li class="nav-item {{ $route == 'config.guide' ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="{{ $route == 'config.guide' ? true : false }}" aria-controls="collapsePages">
            <i class="fas fa-fw fa-book"></i>
            <span>@lang('Tài liệu - Hướng dẫn')</span>
        </a>
        <div id="collapsePages" class="collapse {{ $route == 'config.guide' ? 'show' : '' }}" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-gray-300 py-2 collapse-inner rounded">
                @foreach(App\Constants\ConfigConstants::$guideTitle as $type => $title)
                <a class="collapse-item {{ isset($guideType) && $guideType == $type ? 'active' : '' }}" href="{{ route('config.guide', ['type' => $type]) }}">
                    <i class="fas fa-fw fa-book"></i>
                    <span>{{ __($title) }}</span></a>
                @endforeach
            </div>
        </div>
    </li>
    @endif
    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!-- End of Sidebar -->