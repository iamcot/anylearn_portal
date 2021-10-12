<nav class="navbar navbar-expand-lg navbar-dark bg-topbg">
    <div class="container">
        <a class="navbar-brand" href="/">
            <img src="/cdn/anylearn/img/logo-white.svg" alt="" height="59">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar10">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-collapse collapse" id="navbar10">
            <ul class="navbar-nav nav-fill w-100">
                <li class="nav-item">
                    <a class="nav-link" href="/schools">TRUNG TÂM</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/teachers">CHUYÊN GIA</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/classes">KHOÁ HỌC</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/ref/anylearn">TẢI APP</a>
                </li>
                @if (@auth()->check())
                <li class="nav-item dropdown no-arrow">
                    <a class=" nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="me-2 text-white small">{{ Auth::user()->name }}</span>
                        @if(Auth::user()->image)
                        <span class=""><img class="img-fluid border rounded-circle float-end" style="max-height:24px;" src="{{ Auth::user()->image }}" alt=""></span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                        @lang('Đăng xuất')
                            <i class="fas fa-sign-out-alt fa-sm fa-fw ms-2 text-gray-400"></i>
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
                <h5 class="modal-title" id="exampleModalLabel">@lang('Có chắc bạn buồn đăng xuất?')</h5>
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