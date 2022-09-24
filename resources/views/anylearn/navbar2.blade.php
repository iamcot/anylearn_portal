@inject('userServ','App\Services\UserServices')
<nav class="navbar navbar-expand-lg navbar-light bg-topbg">
    <div class="container">
        <a class="d-lg-none navbar-brand ">
            <img src="/cdn/anylearn/img/logo-color.svg" alt="" height="59">
        </a>
        <a class="d-none d-lg-block navbar-brand background-over" href="/">
            <div>
                <img src="/cdn/anylearn/img/logo-white.svg" alt="" height="59">
            </div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar10">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-collapse collapse  p-2" id="navbar10">
            <ul class="navbar-nav nav-fill w-100 text-bold">
                <li class="nav-item active">
                    <a class="nav-link " href="/info">Giới thiệu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/schools">anySCHOOL</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/teachers">anyPROFESSOR</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/classes">anyCOURSE</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/ref/anylearn">TẢI APP</a>
                </li>
                @if (@auth()->check())
                <li class="nav-item dropdown no-arrow d-flex">
                    <a class=" nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="me-2">{{ Auth::user()->name }}</span>

                    </a>
                    <a class=" nav-link" href="{{ route('cart') }}">
                        <i class="fa fa-shopping-cart position-relative"><span class="position-absolute top-0 start-100 translate-middle badge bg-danger rounded-circle">{{ $userServ->countItemInCart(Auth::user()->id) }}</span></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="/me">
                            <i class="fas fa-user-circle fa-sm fa-fw ms-2 text-secondary"></i>
                            @lang('Trang Quản lý')
                        </a>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw ms-2 text-secondary"></i>
                            @lang('Đăng xuất')
                        </a>
                    </div>
                </li>

                @else
                <li class="nav-item">
                    <a class="nav-link" href="/login">ĐĂNG NHẬP</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-success rounded-pill fw-bold" href="/ref/anylearn">ĐĂNG KÝ</a>
                </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="exampleModalLabel">@lang('Có chắc bạn buồn đăng xuất?')</h2>
                <button class=" btn-close close" type="button" data-bs-dismiss="modal" aria-label="Close">
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