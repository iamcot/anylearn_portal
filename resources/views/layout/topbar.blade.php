<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow d-print-none">
    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <h1 class="h4 mb-0 text-gray-800">
        @if($hasBack ?? false)
            @if ($hasBack === true)
            <a href="javascript:window.history.back()"><i class="fas  fa-arrow-left"></i></a>
            @else 
            <a href="{{ $hasBack }}"><i class="fas  fa-arrow-left"></i></a>
            @endif
        @endif
        {{ $navText ?? '' }}
    </h1>
    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">
        <li  class="nav-item">
            <div class="float-right nav-link">
                @yield('rightFixedTop')
            </div>
        </li>
        <div class="topbar-divider d-none d-sm-block"></div>
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <!-- <a class="dropdown-item" href="">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    @lang('Đổi mật khẩu')
                </a> -->
                <!-- <div class="dropdown-divider"></div> -->
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    @lang('Đăng xuất')
                </a>
            </div>

        </li>
    </ul>
</nav>
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">@lang('Có chắc bạn buồn đăng xuất?')</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">@lang('Nhấn "Đăng xuất" để tắt phiên làm việc hiện tại.')</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">@lang('Bỏ qua')</button>
                <form action="{{ route('logout') }}" method="POST">
                    {{ csrf_field() }}
                    <button class="btn btn-{{ env('MAIN_COLOR', 'primary') }}" type="submit">@lang('Đăng xuất')</button>
                </form>
            </div>
        </div>
    </div>
</div>