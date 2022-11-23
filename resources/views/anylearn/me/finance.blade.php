@inject('userServ', 'App\Services\UserServices')
@inject('UserServices', 'App\Services\UserServices')
@extends('anylearn.me.layout')
<style type="text/css">
    .boxbank {
        box-shadow: rgba(0, 0, 0, 0.4) 0px 0px 10px;
        box-sizing: inherit;
        margin-bottom: 10px;
        padding: 20px;
        box-sizing: inherit;
        line-height: 1.6em;
        padding: 15px;

    }
</style>
@section('rightFixedTop')
    <a class="nav-link" href="#">
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
            data-bs-target="#exampleModal">@lang('Rút tiền') </button>
        <button type="button" id="btn_popup" style="display:none;" class="btn btn-outline-primary" data-bs-toggle="modal"
            data-bs-target="#popup"></button>
    </a>

    {{-- <button class="btn btn-success rounded rounded-pill btn-sm my-2 my-sm-0 ml-2 " data-bs-toggle="modal"
        data-bs-target="#exampleModal">@lang('Tạo Tài khoản')</button> --}}
@endsection
@section('body')
    @if ($UserServices->bankaccount(auth()->user()->id) != null)
        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form action="" method="GET" id="myForm">
                    <div class="modal-content" id="withdraw">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">@lang('Rút Tiền')</button>
                        </div>
                        <div class="modal-body">
                            <strong> @lang('Số dư anyPoint:') {{ auth()->user()->wallet_c }}</strong><br />
                            <span class="fst-italic">@lang('Mỗi anyPoint tương ứng với 1000 vnđ')</span> <br>
                            <strong>@lang('Số anyPoint rút:')</strong>
                            <div class="input-group mb-3">
                                <input type="number" class="form-control" name="anypoint" id="anypoint" oninput="vnd()"
                                    placeholder="{{ __('Nhập số anyPoint bạn muốn rút') }}"
                                    aria-label="Recipient's username" aria-describedby="button-addon2">
                                <button class="btn btn-outline-secondary" onclick="max()" type="button"
                                    id="button-addon2">@lang('Tối đa')</button>
                            </div>
                            <span>@lang('Số tiền tương ứng:') </span> <span id="vnd"></span><br>
                            <strong class="pt-2">@lang('Phương thức nhận tiền')</strong> <br><br>
                            <div id="option">
                                <div class="form-check boxbank">
                                    <span>@lang('Ngân hàng mặc định ở hợp đồng')</span> <br>
                                    @if ($UserServices->bankaccount(auth()->user()->id) != null)
                                        <strong>{{ $UserServices->bankaccount(auth()->user()->id)->bank_name }}</strong>
                                    @else
                                        <strong>@lang('Bạn không có hợp đồng nào')</strong>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">@lang('Hủy')</button>
                            <button type="button" class="btn btn-primary" id="btnwd" name="btnwd"
                                onclick="cfwithdraw()">@lang('Rút tiền')</button>
                        </div>
                    </div>
                    <div class="modal-content" style="display:none;" id="cfwithdraw">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">@lang('Rút Tiền')</button>
                        </div>
                        <div class="modal-body">
                            <strong>@lang('Ngân hàng lựa chọn:') </strong><a
                                id="bank"> {{ $UserServices->bankaccount(auth()->user()->id)->bank_name }}</a> <br>
                            <strong>@lang('Người hưởng thụ:') </strong><a
                                id="Nametkselect"> {{ $UserServices->bankaccount(auth()->user()->id)->bank_account }}</a><br>
                            <strong>@lang('Số tài khoản:') </strong><a
                                id="TKselect"> {{ $UserServices->bankaccount(auth()->user()->id)->bank_no }}</a><br>
                            <strong>@lang('Số tiền:') </strong><a id="money"></a><br>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="black()">@lang('Hủy')</button>
                            <button type="Submit" name="withdraw" id="withdraw" value="withdraw"
                                class="btn btn-primary">@lang('Xác nhận')</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    @else
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form action="" method="GET">
                    <div class="modal-content" id="withdraw">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">@lang('Rút Tiền')</button>
                        </div>
                        <div class="modal-body">
                            Tính Năng Chưa Khả Dụng cho đối tác <br>
                            <strong>Lý do: Hợp đồng của đối tác chưa được xét duyệt</strong><br><br>
                            Kiểm tra hợp đồng của đối tác <a href="{{ route('me.contract') }}">tại đây</a><br><br>
                            Liên hệ với anyLEARN để biết thêm chi tiết <br>
                            Hotline: <a href="">+84 37 490 0344</a>
                            <br>
                            <Strong>Cảm ơn bạn đã luôn đồng hàng cùng anyLEARN</Strong>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">@lang('Quay về')</button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    @endif

    <div class="modal fade text-center" id="popup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="" method="GET" >
                <div class="modal-content" id="withdraw">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">@lang('Kết quả giao dịch')</button>
                    </div>
                    <div class="modal-body">
                        <strong class="text-center"> Yêu cầu thành công</strong><br>
                        Giao dịch sẽ được duyệt và thanh toán trong vòng 24h<br>
                        <p class="text-danger">Lưu Ý:Yêu cầu rút tiền trong các ngày lễ tết sẽ chậm hơn bình thường</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">@lang('Quay về')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @if (!empty(Session::get('bignotify')))
    @parent
        <!-- Bootstrap core JavaScript-->
    <script src="/cdn/vendor/jquery/jquery.min.js"></script>
    <script src="/cdn/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="/cdn/vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="/cdn/js/sb-admin-2.min.js"></script>
        <script type="text/javascript">
            $(function() {
                $('#popup').modal('show');
            });

        </script>
    @endif

@endsection
@section('jscript')
@parent
        <!-- Bootstrap core JavaScript-->
 <script src="/cdn/vendor/jquery/jquery.min.js"></script>
 <script src="/cdn/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
 <!-- Core plugin JavaScript-->
 <script src="/cdn/vendor/jquery-easing/jquery.easing.min.js"></script>
 <!-- Custom scripts for all pages-->
 <script src="/cdn/js/sb-admin-2.min.js"></script>
<script>
    $(document).ready(function() {
        $("form").bind("keypress", function(e) {
            if (e.keyCode == 13) {
                return false;
            }
        });
    });
</script>
@endsection

<script type="text/javascript">

    function black() {
        document.getElementById("withdraw").style.display = "block";
        document.getElementById("cfwithdraw").style.display = "none";
    }
    // format vnd
    function vnd() {
        var x = document.getElementById("anypoint").value;
        if (x > {{ auth()->user()->wallet_c }}) {
            document.getElementById("anypoint").value = {{ auth()->user()->wallet_c }};
            x = {{ auth()->user()->wallet_c }};
        }
        if (x < 0) {
            document.getElementById("anypoint").value = 0;
            x = 0;
        }
        document.getElementById("vnd").innerHTML = (x * 1000)+" VND"
    }
    //nút tối da
    function max() {
        document.getElementById("anypoint").value = {{ auth()->user()->wallet_c }};
        x = {{ auth()->user()->wallet_c }};
        document.getElementById("vnd").innerHTML = (x * 1000)+" VND"
    }

    function cfwithdraw() {
        document.getElementById("withdraw").style.display = "none";
        document.getElementById("cfwithdraw").style.display = "block";
        var x = document.getElementById("anypoint").value;
        document.getElementById("money").innerHTML = (x * 1000)+" VND"
    }
</script>
