@inject('UserServices','App\Services\UserServices')
 {{-- <style type="text/css">
       .boxbank{
box-shadow: rgba(0, 0, 0, 0.4) 0px 0px 10px; box-sizing: inherit; margin-bottom: 10px; padding: 20px; box-sizing: inherit; line-height: 1.6em; padding: 15px;">
       }
       .hidemodals{
        display: none;
       }
   </style> --}}
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
                {{-- <li class="nav-item d-none d-md-inline">
                    <a class="nav-link" href="#">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">@lang('Rút tiền') </button>
                    </a>

                </li> --}}
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
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        @if (Session::get('locale') == null || Session::get('locale') == 'vi')
                            <span class="locale_flag-vi"></span>
                            @else
                               <span class="locale_flag-en"></span>
                            @endif
                      </a>
                      <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <li><a class="dropdown-item" href="{{ url('/') . '?language=vi' }}">
                            <img src="{{ url("").'/cdn/img/flag/vn.svg' }}" width="30"> Tiếng Việt</a></li>
                        <li><a class="dropdown-item" href="{{ url('/') . '?language=en' }}">
                            <img src="{{ url("").'/cdn/img/flag/en.svg' }}" width="30"> English</a></li>
                    </ul>
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
{{-- <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="" method="GET">
        <div class="modal-content hidemodals" id="addbank">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">@lang('Chọn ngân hàng')</button>
            </div>
            <div class="modal-body">
              <select class="form-select" id="banklist" aria-label="{{__('Chọn Ngân Hàng')}}">
                <option selected>@lang('Vui lòng chọn ngân hàng')</option>
                <option value="ACB-Ngân hàng TMCP Á Châu">ACB-Ngân hàng TMCP Á Châu</option>
                <option value="TPBank-Ngân hàng TMCP Tiên Phong">TPBank-Ngân hàng TMCP Tiên Phong</option>
                <option value="DAB-Ngân hàng TMCP Đông Á">DAB-Ngân hàng TMCP Đông Á</option>
                <option value="ABBANK-Ngân hàng TMCP An Bình">ABBANK-Ngân hàng TMCP An Bình</option>
                <option value="SeABank-Ngân Hàng TMCP Đông Nam Á">SeABank-Ngân Hàng TMCP Đông Nam Á</option>
                <option value="BacABank-Ngân hàng TMCP Bắc Á">BacABank-Ngân hàng TMCP Bắc Á</option>
                <option value="VietCapitalBank-Ngân hàng TMCP Bản Việt">VietCapitalBank-Ngân hàng TMCP Bản Việt</option>
                <option value="MSB-Ngân hàng TMCP Hàng hải Việt Nam">MSB-Ngân hàng TMCP Hàng hải Việt Nam</option>
                <option value="Techcombank-Ngân hàng TMCP Kỹ Thương Việt Nam">Techcombank-Ngân hàng TMCP Kỹ Thương Việt Nam</option>
                <option value="KienLongBank-Ngân hàng TMCP Kiên Long">KienLongBank-Ngân hàng TMCP Kiên Long</option>
                <option value="NAB-Ngân hàng TMCP Nam Á">NAB-Ngân hàng TMCP Nam Á</option>
                <option value="NCB-Ngân hàng TMCP Quốc Dân">NCB-Ngân hàng TMCP Quốc Dân</option>
                <option value="VPBank-Ngân hàng TMCP Việt Nam Thịnh Vượng">VPBank-Ngân hàng TMCP Việt Nam Thịnh Vượng</option>
                <option value="HDBank-Ngân hàng TMCP Phát triển Thành phố Hồ Chí Minh">HDBank-Ngân hàng TMCP Phát triển Thành phố Hồ Chí Minh</option>
                <option value="OCB-Ngân hàng TMCP Phương Đông">OCB-Ngân hàng TMCP Phương Đông</option>
                <option value="MB-Ngân hàng TMCP Quân đội">MB-Ngân hàng TMCP Quân đội</option>
                <option value="PVcombank-Ngân hàng TMCP Đại chúng">PVcombank-Ngân hàng TMCP Đại chúng</option>
                <option value="VIB-Ngân hàng TMCP Quốc tế Việt Nam">VIB-Ngân hàng TMCP Quốc tế Việt Nam</option>
                <option value="SCB-Ngân hàng TMCP Sài Gòn">SCB-Ngân hàng TMCP Sài Gòn</option>
                <option value="SGB-Ngân hàng TMCP Sài Gòn Công Thương">SGB-Ngân hàng TMCP Sài Gòn Công Thương</option>
                <option value="SHB-Ngân hàng TMCP Sài Gòn – Hà Nội">SHB-Ngân hàng TMCP Sài Gòn – Hà Nội</option>
                <option value="VietABank-Ngân hàng TMCP Việt Á">VietABank-Ngân hàng TMCP Việt Á</option>
                <option value="BaoVietBank-Ngân hàng TMCP Bảo Việt">BaoVietBank-Ngân hàng TMCP Bảo Việt</option>
                <option value="VietBank-Ngân hàng TMCP Việt Nam Thương Tín">VietBank-Ngân hàng TMCP Việt Nam Thương Tín</option>
                <option value="PG Bank-Ngân Hàng TMCP Xăng Dầu Petrolimex">PG Bank-Ngân Hàng TMCP Xăng Dầu Petrolimex</option>
                <option value="EIB-Ngân Hàng TMCP Xuất Nhập khẩu Việt Nam">EIB-Ngân Hàng TMCP Xuất Nhập khẩu Việt Nam</option>
                <option value="LienVietPostBank-Ngân Hàng TMCP Bưu điện Liên Việt">LienVietPostBank-Ngân Hàng TMCP Bưu điện Liên Việt</option>
                <option value="Vietcombank-Ngân Hàng TMCP Ngoại thương Việt Nam">Vietcombank-Ngân Hàng TMCP Ngoại thương Việt Nam</option>
                <option value="VietinBank-Ngân Hàng TMCP Công Thương Việt Nam">VietinBank-Ngân Hàng TMCP Công Thương Việt Nam</option>
                <option value="BIDV-Ngân Hàng TMCP Đầu tư và Phát triển Việt Nam">BIDV-Ngân Hàng TMCP Đầu tư và Phát triển Việt Nam</option>
                <option value="Agribank-Ngân hàng Nông nghiệp và Phát triển Nông thôn Việt Nam">Agribank-Ngân hàng Nông nghiệp và Phát triển Nông thôn Việt Nam</option>
              </select>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" onclick="black()">@lang('Hủy')</button>
              <button type="button" class="btn btn-primary" onclick="infobank()">@lang('Xác nhận')</button>
            </div>
          </div>
          <div class="modal-content" id="withdraw">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">@lang('Rút Tiền')</button>
            </div>
            <div class="modal-body">
              <strong> @lang('Số dư anyPoint:') {{auth()->user()->wallet_c}}</strong><br/>
              <span class="fst-italic">@lang('Mỗi anyPoint tương ứng với 1000 vnđ')</span> <br>
              <strong>@lang('Số anyPoint rút:')</strong>
              <div class="input-group mb-3">
                <input type="number" class="form-control" name="anypoint" id="anypoint" oninput="vnd()" placeholder="{{ __('Nhập số anyPoint bạn muốn rút')}}" aria-label="Recipient's username" aria-describedby="button-addon2">
                <button class="btn btn-outline-secondary" onclick="max()" type="button" id="button-addon2">@lang('Tối đa')</button>
              </div>
              <span>@lang('Số tiền tương ứng:') </span> <span id="vnd"></span><br>
              <strong class="pt-2">@lang('Phương thức nhận tiền')</strong> <br><br>
              <div id="option">
                  <div class="form-check boxbank">
                      <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1" checked>
                      <label class="form-check-label" for="exampleRadios1">
                        <span>@lang('Ngân hàng mặc định ở hợp đồng')</span> <br>
                        @if ($UserServices->bankaccount(auth()->user()->id) !=null)
                        <strong>{{ $UserServices->bankaccount(auth()->user()->id) }}</strong>
                        @else
                        <strong>@lang('Bạn không có hợp đồng nào')</strong>
                        @endif
                      </label>
                    </div>
              </div>
              <div class="d-grid gap-2">
                  <button class="btn btn-outline-primary" onclick="addbank()" type="button">@lang('Rút một ngân hàng khác')</button>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Hủy')</button>
              <button type="button" class="btn btn-primary" id="btnwd" name="btnwd" onclick="cfwithdraw()">@lang('Rút tiền')</button>
            </div>
          </div>
          <div class="modal-content hidemodals" id="infobank">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">@lang('Nhập thông tin')</button>
              </div>
              <div class="modal-body" >
                  <strong>@lang('Ngân hàng lựa chọn:')</strong><p id="selectedbank"></p>
                  <div class="mb-3">
                      <label class="form-label">@lang('Số tài khoản')</label>
                      <input type="text" class="form-control" name="stk" id="stk">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">@lang('Tên chủ sở hữu')</label>
                      <input type="text" class="form-control" name="namestk" id="namestk">
                  </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="black()">@lang('Hủy')</button>
                <button type="button" class="btn btn-primary" onclick="addoptions()">@lang('Xác nhận')</button>
              </div>
          </div>
          <div class="modal-content hidemodals" id="cfwithdraw">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">@lang('Rút Tiền')</button>
              </div>
              <div class="modal-body" >
                  <strong>@lang('Ngân hàng lựa chọn:')</strong><a id="bank"></a> <br>
                  <strong>@lang('Người hưởng thụ:')</strong><a id="Nametkselect"></a><br>
                  <strong>@lang('Số tài khoản:')</strong><a id="TKselect"></a><br>
                  <strong>@lang('Số tiền:')</strong><a id="money"></a><br>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="black()">@lang('Hủy')</button>
                <button type="Submit" class="btn btn-primary">@lang('Xác nhận')</button>
              </div>
          </div>
    </form>

  </div>
</div> --}}
{{-- <script type="text/javascript">
    function addbank() {
        document.getElementById("withdraw").style.display = "none";
        document.getElementById("addbank").style.display = "block";
    }
    function black() {
        document.getElementById("withdraw").style.display = "block";
        document.getElementById("addbank").style.display = "none";
        document.getElementById("infobank").style.display = "none";
        document.getElementById("cfwithdraw").style.display = "none";

    }
    function infobank() {
        var selectedbank = document.getElementById('banklist').value;
        document.getElementById("selectedbank").innerHTML = selectedbank;
        document.getElementById("name").innerHTML = name;
        document.getElementById("addbank").style.display = "none";
        document.getElementById("infobank").style.display = "block";
    }
    function addoptions() {
        var selectedbank = document.getElementById('banklist').value;
        var stk = document.getElementById('stk').value;
        var namestk = document.getElementById('namestk').value;

        var radiobox = document.createElement('input');
        radiobox.type = 'radio';
        radiobox.value = selectedbank+"*"+stk+"#"+namestk;
        radiobox.name='exampleRadios';
        radiobox.className ='form-check-input';
        var strong = document.createElement('strong');
        strong.innerHTML = selectedbank;
        // radiobox.addClass("form-check-input");

    var label = document.createElement('label')
    label.appendChild(strong);
    label.className ='form-check-label';
    // label.classlist.add("form-check-label");

    var newline = document.createElement('br');

    var div = document.createElement('div');
    div.className='form-check boxbank';
    // div.classlist.add("form-check boxbank");
    div.appendChild(radiobox);
    div.appendChild(label);
    div.appendChild(newline);
    var container = document.getElementById('option');
    container.appendChild(div);
        document.getElementById("withdraw").style.display = "block";
        document.getElementById("addbank").style.display = "none";
        document.getElementById("infobank").style.display = "none";
    }
    function vnd() {
        var x = document.getElementById("anypoint").value;
        if (x >{{auth()->user()->wallet_c}}) {
            document.getElementById("anypoint").value = {{auth()->user()->wallet_c}};
            x = {{auth()->user()->wallet_c}};
        }
        if(x<0){
            document.getElementById("anypoint").value = 0;
            x = 0;
        }
        document.getElementById("vnd").innerHTML = (x*1000).toLocaleString('vn-VN', {style : 'currency', currency : 'VND'});
    }
    function max() {
        document.getElementById("anypoint").value ={{auth()->user()->wallet_c}};
        x = {{auth()->user()->wallet_c}};
        document.getElementById("vnd").innerHTML = (x*1000).toLocaleString('vn-VN', {style : 'currency', currency : 'VND'});
    }
    function cfwithdraw() {
        document.getElementById("btnwd").onclick = function ()
            {
                var checkbox = document.getElementsByName('exampleRadios');
                for (var i = 0; i < checkbox.length; i++){
                    if (checkbox[i].checked === true){
                        var TKselect = checkbox[i].value.slice(checkbox[i].value.indexOf("*")+1,checkbox[i].value.indexOf('#'));
                        var Nametkselect = checkbox[i].value.slice(checkbox[i].value.indexOf('#')+1);
                        var bankselect = checkbox[i].value.slice(0,checkbox[i].value.indexOf('*'));
                        console.log(bankselect);
                        document.getElementById('bank').innerHTML=bankselect;
                        document.getElementById('Nametkselect').innerHTML=Nametkselect;
                        document.getElementById('TKselect').innerHTML=TKselect;
                        var x = document.getElementById("anypoint").value;
                        document.getElementById("money").innerHTML = (x*1000).toLocaleString('vn-VN', {style : 'currency', currency : 'VND'});

                        document.getElementById('bank').value=bankselect;
                        document.getElementById('Nametkselect').value=Nametkselect;
                        document.getElementById('TKselect').value=TKselect;

                        document.getElementById("withdraw").style.display = "none";
                        document.getElementById("cfwithdraw").style.display = "block";
                        document.getElementById("addbank").style.display = "none";
                        document.getElementById("infobank").style.display = "none";
                    }
                }
            };
    }
</script> --}}
