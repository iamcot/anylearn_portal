@inject('userServ', 'App\Services\UserServices')
@inject('itemServ', 'App\Services\ItemServices')

<div class="card mb-3 shadow">
    <div class="card-header  font-weight-bold">Thông tin kích hoạt</div>
    <div class="card-body">

        <div class="form-group row">
            <label for="code" class="col-md-3 col-form-label text-md-right ">{{ __('Mã kích hoạt') }}</label>
            <div class="col-md-8">
                <textarea id="code" onchange="hp();" class="form-control @error('code') is-invalid @enderror" name="code" rows="3">{{ old('code') }}</textarea>
                <p class="mt-1 small">@lang('Một dòng tương ứng với một mã bạn cần nhập.')</p>
            </div>
        </div>

        <div class="form-group row">
            <label for="email" class="col-md-3 col-form-label text-md-right ">{{ __('Mẫu thông báo email') }}</label>
            <div class="col-md-8">
                <textarea id="email" onchange="hp();" class="form-control @error('email') is-invalid @enderror" name="email" rows="2">{{ old('email', !empty($notifTemplates) ? $notifTemplates->email_template : '') }}</textarea>
                <p class="mt-1 small">@lang('Nhập &#123;&#123; code }} để thay thế cho phần mã gửi tới khách hàng của bạn.')</p>
            </div>
        </div>

        <div class="form-group row">
            <label for="notif" class="col-md-3 col-form-label text-md-right ">{{ __('Mẫu thông báo điện thoại') }}</label>
            <div class="col-md-8">
                <textarea id="notif" onchange="hp();" class="form-control @error('notif') is-invalid @enderror" name="notif" rows="2">{{ old('notif', !empty($notifTemplates) ? $notifTemplates->notif_template : '') }}</textarea>
                <p class="mt-1 small">@lang('Nhập &#123;&#123; code }} để thay thế cho phần mã gửi tới khách hàng của bạn.')</p>
            </div>  
        </div>
    </div>
</div>

<div class="text-center mb-3 mt-3">
    <a href="javascript:changeTab('content-tab')" class="btn btn-primary border-0 rounded"><< Sửa giới thiệu</a>
    <button class="btn btn-success border-0 rounded" name="tab" value="code"><i class="fas fa-save"></i> @lang('Lưu thay đổi')</button>
    <a href="javascript:changeTab('schedule-tab')" class="btn btn-primary border-0 rounded">Sửa lịch học >></a>       
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