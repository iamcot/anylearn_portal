@inject('userServ', 'App\Services\UserServices')
@inject('itemServ', 'App\Services\ItemServices')

<div class="card mb-3 shadow">
    <div class="card-header  font-weight-bold">Thông tin kích hoạt</div>
    <div class="card-body">

        <div class="form-group row">
            <label for="code" class="col-md-3 col-form-label text-md-right ">{{ __('Danh sách mã') }}</label>
            <div class="col-md-8">
                <textarea id="code" onchange="hp();" class="form-control @error('code') is-invalid @enderror" name="code" rows="3">{{ old('code') }}</textarea>
                <p class="mt-1 small">@lang('Một dòng tương ứng với một mã bạn cần nhập.')</p>
            </div>
        </div>

        <div class="form-group row">
            <label for="notif" class="col-md-3 col-form-label text-md-right ">{{ __('Mẫu thông báo') }}</label>
            <div class="col-md-8">
                <textarea id="notif" onchange="hp();" class="form-control @error('notif') is-invalid @enderror" name="notif" rows="3">{{ old('notif', !empty($notifTemplates) ? $notifTemplates->notif_template : '') }}</textarea>
                <p class="mt-1 small">@lang('Nhập {code} để thay thế cho phần mã gửi tới khách hàng của bạn.')</p>
            </div>
        </div>

        <div class="form-group row">
            <label for="email" class="col-md-3 col-form-label text-md-right ">{{ __('Mẫu thư điện tử') }}</label>
            <div class="col-md-8">
                <textarea id="email" onchange="hp();" class="form-control editor @error('email') is-invalid @enderror" name="email" rows="2">{{ old('email', !empty($notifTemplates) ? $notifTemplates->email_template : '') }}</textarea>
                <p class="mt-1 small">@lang('Nhập {code} để thay thế cho phần mã gửi tới khách hàng của bạn.')</p>
            </div>
        </div>
    </div>
</div>

<div class="text-center mb-3 mt-3">
    <a href="javascript:changeTab('content-tab')" class="btn btn-primary border-0 rounded">
        << Sửa giới thiệu</a>
            <button class="btn btn-success border-0 rounded" name="tab" value="code"><i class="fas fa-save"></i> @lang('Lưu thay đổi')</button>
</div>

<div class="card shadow mt-3">
    <div class="card-body p-0 table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <th class="text-center">Partner Id</th>
                <th>ID#Khóa học</th>
                <th>Mã kích hoạt</th>
                <th>Người dùng</th>
                <th>Đơn hàng</th>
                <th>Cập nhật</th>
                <th width="16%">Thao tác</th>
            </thead>
            <tbody>
                @if(!empty($itemCodes))
                @foreach ($itemCodes as $code)
                <tr>
                    <th class="text-center">{{ $code->partner_id }}</th>
                    <td>{{ $code->item_id }}#{{ $code->class }}</td>
                    <td>{{ $code->code }}</td>
                    <td>{{ $code->user_id  ? ($code->name . "(" . $code->phone . ")") : "" }}</td>
                    <td>{{ $code->order_detail_id }}</td>
                    <td>{{ $code->updated_at }}</td>
                    <td>
                        @if(isset($code->user_id))
                        <a class="btn btn-sm btn-primary mt-1" href="{{ route('codes.resend', ['id' => $code->id]) }}"><i class="fa fa-paper-plane"></i> Gửi lại</a>
                        @else
                        <a class="btn btn-sm btn-success mt-1" href="{{ route('codes.refresh', ['id' => $code->id]) }}"><i class="	fa fa-bolt"></i> Sử dụng</a>
                        @endif
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $itemCodes->appends(request()->query())->links() }}
    </div>
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