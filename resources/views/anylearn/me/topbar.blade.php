   <style type="text/css">
       .boxbank{
box-shadow: rgba(0, 0, 0, 0.4) 0px 0px 10px; box-sizing: inherit; margin-bottom: 10px; padding: 20px; box-sizing: inherit; line-height: 1.6em; padding: 15px;">
       }
       .hidemodals{
        display: none;
       }
   </style>
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow d-print-none">
        <div class="container">
            <a class="navbar-brand" href="{{ route('me.dashboard') }}">
                <img src="/cdn/img/logo.png" alt="" width="30" height="30" class="d-block d-sm-none">
                <img src="/cdn/img/logo-full.png" alt="" width="120" height="30" class="d-none d-sm-block">
            </a>
            <!-- Sidebar Toggle (Topbar) -->
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>
            <!-- Topbar Navbar -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <div class="float-right nav-link">
                        @yield('rightFixedTop')
                    </div>
                </li>
                <li class="nav-item d-none d-md-inline">
                    <a class="nav-link" href="#">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">@lang('Rút tiền') </button>
                    </a>

                </li>
                <li class="nav-item d-none d-md-inline">
                    <a class="nav-link" href="#">
                        @if(Auth::user()->image)
                        <span class=""><img class="img-fluid border rounded-circle float-end" style="height:32px;width:32px;" src="{{ Auth::user()->image }}" alt=""></span>
                        @endif
                        <span class="ms-2 text-black small">{{ Auth::user()->name }}</span>
                    </a>

                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/">
                        <i class="fas fa-home fa-fw mr-2 text-success"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('me.notification') }}">
                        <i class="fas fa-bell fa-fw mr-2 text-success"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                        <i class="fas fa-sign-out-alt fa-fw mr-2  text-danger"></i>
                    </a>
                </li>
            </ul>
        </div>
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
                    <form action="{{ route('logout') }}" method="POST">
                        {{ csrf_field() }}
                        <button class="btn btn-danger border-0 rounded-pill" type="submit">@lang('Đăng xuất')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content hidemodals" id="addbank">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">@lang('Chọn ngân hàng')</button>
      </div>
      <div class="modal-body">
        <select class="form-select" aria-label="{{__('Chọn Ngân Hàng')}}">
          <option selected>@lang('Vui lòng chọn ngân hàng')</option>
          <option value="ACB">ACB-Ngân hàng TMCP Á Châu</option>
          <option value="VietinBank">TPBank-Ngân hàng TMCP Tiên Phong</option>
          <option value="Vietcombank">Vietcombank-Ngoại Thương Việt Nam</option>
          <option value="VPBank">VPBank-Việt Nam Thịnh Vượng</option>
          <option value="MB">MB-Quân Đội</option>
          <option value="Techcombank">Techcombank-Kỹ Thương</option>
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="black()">@lang('Hủy')</button>
        <button type="button" class="btn btn-primary">@lang('Xác nhận')</button>
      </div>
    </div>
    <div class="modal-content" id="withdraw">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">@lang('Rút Tiền')</button>
      </div>
      <div class="modal-body">
        <strong> @lang('Số dư anyPoint: 1000')</strong><br/>
        <span class="fst-italic">@lang('Mỗi anyPoint tương ứng với 1000 vnđ')</span> <br>
        <strong>@lang('Số anyPoint rút:')</strong>
        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="{{ __('Nhập số anyPoint bạn muốn rút')}}" aria-label="Recipient's username" aria-describedby="button-addon2">
          <button class="btn btn-outline-secondary" type="button" id="button-addon2">@lang('Tối đa')</button>
        </div>
        <span>@lang('Số tiền tương ứng: anyPoint x 1000 đồng') </span><br>
        <strong class="pt-2">@lang('Phương thức nhận tiền')</strong> <br>
                    <div class="form-check boxbank">
                  <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1" checked>
                  <label class="form-check-label" for="exampleRadios1">
                    <span>@lang('Ngân hàng mặc định ở hợp đồng')</span> <br>
                                <strong>@lang('Tên ngân hàng')</strong>
                  </label>
                </div>
                <div class="form-check boxbank">
                  <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option2">
                  <label class="form-check-label" for="exampleRadios2">
                                @lang('Tên ngân hàng được thêm')
                  </label>
                </div>
                <div class="d-grid gap-2">
                  <button class="btn btn-outline-primary" onclick="addbank()" type="button">@lang('Rút một ngân hàng khác')</button>
                </div>
                
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Hủy')</button>
        <button type="button" class="btn btn-primary">@lang('Rút tiền')</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
    function addbank() {
        document.getElementById("withdraw").style.display = "none";
        document.getElementById("addbank").style.display = "block";
    }
    function black() {
        document.getElementById("withdraw").style.display = "block";
        document.getElementById("addbank").style.display = "none";
    }
</script>