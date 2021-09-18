<form action="{{ route('add2cart') }}" method="post" id="pdpAdd2Cart">
    @csrf
    <input type="hidden" name="action" value="pdpAdd2Cart">
    <input type="hidden" name="class" value="{{ $class->id }}">
    <div id="pdpAdd2CartModal" class="modal fade shadow" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title m-0 font-weight-bold text-{{ env('MAIN_COLOR', 'primary') }}">@lang('Bạn đang đăng ký khoá học')
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                </div>
                <div class="modal-body">
                    <h3 class="font-weight-bold text-{{ env('MAIN_COLOR', 'primary') }}">{{ $class->title }}</h3>
                    <p>{{ $author->role == 'teacher' ? 'Giảng viên' : 'Trung tâm' }}: {{ $author->name }}</p>
                    <p>Khai giảng: {{ date('d/m/Y', strtotime($class->date_start)) }} {{ $num_schedule <= 1 ? '' : '(có ' . $num_schedule . ' buổi học)' }}</p>
                    <p>Học phí: <strong>{{ number_format($item->price, 0, ',', '.') }}</strong></p>
                    @if(count($item->openings) > 0)
                    <p class="text-danger">Chọn lịch khai giảng</p>
                    <ul class="list-unstyled">
                        @foreach($item->openings as $opening)
                            <li>
                                <label for="class_{{ $opening->id }}"> <input required id="class_{{ $opening->id }}" type="radio" name="class" value="{{ $opening->id }}"> {{ $opening->title }}</label>
                            </li>
                        @endforeach
                    </ul>
                    @endif
                    <p>[<a class="text-success" href="javascript:onVoucher()"><strong>TÔI CÓ MÃ QUÀ TẶNG KHOÁ HỌC</strong></a>]</p>
                    <div id="add2cartvoucher" style="display: none;">
                        <div class="row">
                            <div class="col-10">
                                <input type="text" class="form-control" name="voucher">
                            </div>
                            <div class="col-2"> <a href="javascript:offVoucher()">ĐÓNG</a></div>
                        </div>
                        <p class="small">Chỉ nhập mã quà tặng khoá học, không hỗ trợ mã thanh  toán ở đây.</p>
                    </div>
                    @if (@auth()->check())
                        @if(Auth::user()->wallet_m >= $item->price)
                        <p class=" text-danger">Số dư của bạn sau khi đăng ký là {{ number_format((Auth::user()->wallet_m - $item->price), 0 , ",", ".") }}</p>
                        @endif
                   @endif
                </div>
                <div class="modal-footer">
                   
                    <button id="add2CartBtn" name="add2cart" value="add2cart" class="btn btn-{{ env('MAIN_COLOR', 'primary') }}">@lang('ĐĂNG KÝ KHOÁ HỌC')</button>
                </div>
            </div>
        </div>
    </div>
</form> 