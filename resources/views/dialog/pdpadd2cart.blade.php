<form action="{{ route('add2cart') }}" method="post" id="pdpAdd2Cart">
    @csrf
    <input type="hidden" name="action" value="pdpAdd2Cart">
    <input type="hidden" name="class" value="{{ $class->id }}">
    <div id="pdpAdd2CartModal" class="modal fade shadow" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title m-0 text-primary }}">@lang('Bạn đang đăng ký khoá học')
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h3 class="fw-bold text-success }}">{{ $class->title }}</h3>
                    <p>{{ $author->role == 'teacher' ? 'Giảng viên' : 'Trung tâm' }}: {{ $author->name }}</p>
                    <p>Khai giảng: {{ date('d/m/Y', strtotime($class->date_start)) }}
                        {{ $num_schedule <= 1 ? '' : '(có ' . $num_schedule . ' buổi học)' }}</p>
                    <p>Học phí: <strong>{{ number_format($item->price, 0, ',', '.') }}</strong></p>
                    <p>Bạn sẽ nhận <strong>{{ number_format($commission, 0, ',', '.') }}</strong> anyPoint cho giao dịch
                        này</p>
                    @if (count($item->openings) > 0)
                        <p class="text-danger">Chọn lịch khai giảng</p>
                        <ul class="list-unstyled">
                            @foreach ($item->openings as $opening)
                                <li>
                                    <label for="class_{{ $opening->id }}"> <input required
                                            id="class_{{ $opening->id }}" type="radio" name="class"
                                            value="{{ $opening->id }}"> {{ $opening->title }}</label>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <div>
                        <p id="textvorcher"><a class="text-primary" href="javascript:onVoucher()"><strong>TÔI CÓ MÃ QUÀ TẶNG / GIẢM
                                    GIÁ</strong></a></p>
                        <div id="add2cartvoucher" style="display: none;">
                            <div class="row mb-2">
                                <div class="col-9">
                                    <input type="text" placeholder="Mã Khuyến Mãi Khóa Học" class="form-control"
                                        name="voucher">
                                </div>
                                <div class="col-3 mt-1">
                                    <button onclick="offVoucher()" type="button" class="form-control btn-close"
                                        aria-label="Close"></button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <p id="textchild"><a class="text-primary" href="javascript:onChild()"><strong>TÔI MUỐN ĐĂNG KÝ TÀI KHOẢN
                                        CON</strong></a></p>
                            <div id="add2cartchild" class="input-group-append mb-2" style="display: none;">
                                <div class="row mt-1">
                                    <div class="col-md-8">
                                        <select name="child" class="form-select"
                                            id="inputGroupSelect04">
                                            <option value="">Chọn thành viên<nav></nav></option>
                                            @if(!count($children) > 0)
                                                @foreach ($children as $child)
                                                    <option value="{{ $child->id }}"> {{ $child->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-1 mt-1">
                                        <a class="text-black" href="{{ route('me.child') }}"><i class="fas fa-user-plus"></i></a>
                                    </div>
                                    <div class="col-md-1 mt-1">
                                        <button onclick="offChild()" type="button" class="form-control btn-close"
                                            aria-label="Close"></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button id="add2CartBtn" name="add2cart" value="add2cart"
                            class="btn btn-success rounded-pill border-0">@lang('ĐĂNG KÝ KHOÁ HỌC')</button>
                    </div>
                </div>
            </div>
        </div>
</form>
