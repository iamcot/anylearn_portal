@inject('userServ', 'App\Services\UserServices')
@inject('itemServ', 'App\Services\ItemServices')

<div class="card mb-3 shadow">
    <div class="card-header  font-weight-bold">Chính sách phí và ưu đãi</div>
    <div class="card-body">

        <div class="form-group row">
            <label for="is_paymentfee" class="col-md-3 col-form-label text-md-right ">{{ __('Thu hộ học phí') }}</label>
            <div class="col-md-8 form-check form-switch m-2">
                <input class="form-check-input" type="checkbox" name="is_paymentfee" id="is_paymentfee" {{ !empty($course) && $course['info']->is_paymentfee > 0 ? "checked" : "" }}>
            </div>
        </div>

        <div class="form-group row">
            <label for="price" class="col-md-3 col-form-label text-md-right ">{{ __('Học phí') }}</label>
            <div class="col-md-8">
                <input id="price" onchange="hp();" min="0" type="number" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ old('price', !empty($course) ? $course['info']->price : 0) }}">
            </div>
        </div>

        <div class="form-group row">
            <label for="org_price" class="col-md-3 col-form-label text-md-right ">{{ __('Học phí gốc') }}</label>
            <div class="col-md-8">
                <input id="org_price" onchange="hp();" min="0" type="number" class="form-control @error('org_price') is-invalid @enderror" name="org_price" value="{{ old('org_price', !empty($course) ? $course['info']->org_price : '') }}">
            </div>

        </div>

        <div class="form-group row">
            <label for="quantity" class="col-md-3 col-form-label text-md-right ">{{ __('Số lượng tuyển sinh') }}</label>
            <div class="col-md-8">
                <input id="quantity" type="number" class="form-control @error('quantity') is-invalid @enderror" name="seats" value="{{ old('quantity', !empty($course) ? $course['info']->seats : '') }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="quantity" class="col-md-3 col-form-label text-md-right ">{{ __('Chu kỳ học (ngày)') }}</label>
            <div class="col-md-8">
                <input id="quantity" type="number" class="form-control @error('quantity') is-invalid @enderror" name="seats" value="{{ old('quantity', !empty($course) ? $course['info']->seats : '') }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="quantity" class="col-md-3 col-form-label text-md-right ">{{ __('Chu kỳ học (buổi)') }}</label>
            <div class="col-md-8">
                <input id="quantity" type="number" class="form-control @error('quantity') is-invalid @enderror" name="seats" value="{{ old('quantity', !empty($course) ? $course['info']->seats : '') }}">
            </div>
        </div>
    </div>


</div>
@if(!empty($course['info']) && $course['info']->subtype == 'offline')
<div class="card mb-3 shadow">
    <div class="card-header"><strong>Quản lý phụ phí</strong></div>
    <div class="card-body">
        <form action="" method="get">
            <ul class="list-unstyled mb-0">
                @foreach ($extra as $item)
                <li>
                    <div class="d-flex align-items-center py-3 border-bottom border-300 row">
                        <p class="fs--1 mb-0 me-6 col-md-7">{{ $item->title }}</p>
                        <strong class="col-md-3">{{ number_format($item->price) }}₫</strong>
                        <div class="col-md-2">
                            <button type="button" class="icon-item icon-item-sm rounded-3 fs--2 btn btn-default btn-sm" onclick="update({{ $item->id }},'{{ $item->title }}',{{ $item->price }})"><i class="fas fa-pen"></i></button>
                            <input type="hidden" name="iddelete" value="{{ $item->id }}">
                            <button type="button" class="icon-item icon-item-sm rounded-3 fs--2 btn btn-default btn-sm" data-bs-toggle="modal" data-bs-target="#delete">
                                <i class="fas fa-trash"></i>
                            </button>
                            <!-- Modal -->
                            <div class="modal fade" id="delete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-danger" id="exampleModalLabel">Bạn đang yêu
                                                cầu xóa!!!</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Bạn có chắc muốn xóa khoản phụ phí này?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                            <button type="submit" name="action" value="deleteextrafee" class="btn btn-primary">Xóa</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <button type="submit" name="action" value="deleteextrafee"
                                    class="icon-item icon-item-sm rounded-3 fs--2 btn btn-default btn-sm"><i
                                        class="fas fa-trash"></i></button> --}}
                        </div>

                    </div>
                </li>
                @endforeach
            </ul>
        </form>
        <form action="" method="get" class="mt-3">
            <div class="position-relative mb-4">
                <div class="input-group">
                    <input type="hidden" name="idextrafee" value="" id="idextrafee">
                    <input name="titleextrafee" id="titleextrafee" required="" placeholder="Thêm phụ phí" type="text" class="pe-4 form-control" value="">
                    <input name="priceextrafee" id="priceextrafee" required="" placeholder="Số tiền phụ phí" type="number" class="pe-4 form-control" min="0" value="">
                    <div class="input-group-append">
                        <button class="btn btn-success" name="action" value="addextrafee" type="submit">Lưu</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
<div class="text-center mb-3">
    <a href="javascript:changeTab('info-tab')" class="btn btn-primary border-0 rounded">
        << Sửa thông tin chính</a>
            <button class="btn btn-success border-0 rounded" name="tab" value="price"><i class="fas fa-save"></i> @lang('Lưu thay đổi')</button>
            <a href="javascript:changeTab('content-tab')" class="btn btn-primary border-0 rounded">Sửa giới thiệu >></a>
</div>


@section('jscript')
@parent
<script>
    function hp() {
        var hp = document.getElementById("price").value;
        var hpg = document.getElementById("org_price").value;
        if ((hpg != "" || hpg > 0) && hpg < hp) {
            document.getElementById("org_price").value = hp;
        }
    }

    function update(id, title, price) {
        console.log(id, title, price);
        document.getElementById('idextrafee').value = id;
        document.getElementById('titleextrafee').value = title;
        document.getElementById('priceextrafee').value = price;
        // window.scrollTo(0, document.body.scrollHeight);
    }
</script>
@endsection
@include('dialog.company_commission')