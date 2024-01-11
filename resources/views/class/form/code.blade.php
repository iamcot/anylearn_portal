@inject('userServ', 'App\Services\UserServices')
@inject('itemServ', 'App\Services\ItemServices')

<div class="card mb-3 shadow">
    <div class="card-header font-weight-bold">Thông tin kích hoạt</div>
    <div class="card-body">
        <div class="form-group row">
            <label for="" class="col-md-3 col-form-label text-md-right">{{ __('Chọn hình thức hỗ trợ') }}</label>
            <div class="col-md-8">
                <div class="pt-2 form-check form-check-inline">
                    <input class="mt-1 form-check-input" type="radio" name="activation_support" id="manual-support" 
                        value="{{ \App\Constants\ItemConstants::ACTIVATION_SUPPORT_MANUAL }}" 
                        {{ empty($course) || $course['info']->activation_support == \App\Constants\ItemConstants::ACTIVATION_SUPPORT_MANUAL ? 'checked' : '' }}>
                    <label class="form-check-label" for="inlineRadio1">Nhập thủ công</label>
                </div>
                <div class="pt-2 form-check form-check-inline">
                    <input class="mt-1 form-check-input" type="radio" name="activation_support" id="api-support" 
                        value="{{ \App\Constants\ItemConstants::ACTIVATION_SUPPORT_API }}"
                        {{ !empty($course) && $course['info']->activation_support == \App\Constants\ItemConstants::ACTIVATION_SUPPORT_API ? 'checked' : '' }} >

                    <label class="form-check-label" for="inlineRadio2">Lấy tự động</label>
                </div>
            </div>
        </div>
        <div class="form-group row" id="api-form">
            <label for="code" class="col-md-3 col-form-label text-md-right ">{{ __('Mã sản phẩm') }}</label>
            <div class="col-md-8">
                <input id="product-id" onchange="hp();" class="form-control @error('product_id') is-invalid @enderror" name="product_id" rows="3" 
                    value="{{ !empty($course) && $course['info']->product_id ? $course['info']->product_id : '' }}">{{ old('product_id') }}         
                @if (empty($config_api))  
                <p class="mt-1 small text-warning">@lang('Liên hệ với anyLEARN để thực hiện cấu hình cho hình thức này!')</p>
                @else 
                <p class="mt-1 small">@lang('Nhập mã sản phẩm trên hệ thống của bạn.')</p>
                @endif

            </div>
        </div>
        <div class="form-group row" id="manual-form">
            <label for="code" class="col-md-3 col-form-label text-md-right ">{{ __('Danh sách mã') }}</label>
            <div class="col-md-8">
                <textarea id="code" onchange="hp();" class="form-control @error('code') is-invalid @enderror" name="code" rows="3">{{ old('code') }}</textarea>
                <p class="mt-1 small">@lang('Mỗi dòng tương ứng với một mã kích hoạt.')</p>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3 shadow">
    <div class="card-header font-weight-bold">Tạo mẫu thông báo</div>
    <div class="card-body">
        <div class="form-group row">
            <label for="email" class="col-md-3 col-form-label text-md-right ">{{ __('Thông báo cho email') }}</label>
            <div class="col-md-8">
                <textarea id="email" onchange="hp();" class="form-control @error('email') is-invalid @enderror" name="email" rows="2">{{ old('email', !empty($notifTemplates) ? $notifTemplates->email_template : '') }}</textarea>
                <p class="m-1 small">@lang('Vui lòng tham chiếu phần tham số phía dưới để lấy cung cấp đầy đủ thông tin cho khách hàng của bạn.')</p>
            </div>
        </div> 
        <div class="form-group row">
            <label for="notif" class="col-md-3 col-form-label text-md-right ">{{ __('Thông báo cho phone') }}</label>
            <div class="col-md-8">
                <textarea id="notif" onchange="hp();" class="form-control @error('notif') is-invalid @enderror" name="notif" rows="2">{{ old('notif', !empty($notifTemplates) ? $notifTemplates->notif_template : '') }}</textarea>
                <p class="m-1 small">@lang('Vui lòng tham chiếu phần tham số phía trên để lấy cung cấp đầy đủ thông tin cho khách hàng của bạn.')</p>
            </div>  
       </div>
        <div class="form-group row">
            <label class="col-md-3"></label>
            <div class="col-md-8">
                <div class="border small">
                    <p class="m-1">Để thêm tham số, bạn có thể sử dụng danh sách các tham số có sẵn dưới đây:</p>
                    <ul>
                        <li><strong>username:</strong> tài khoản đăng nhập trên ứng dụng</li>
                        <li><strong>password:</strong> mật khẩu ban đầu của tài khoản</li> 
                        <li><strong>course:</strong> tên khóa học</li>
                        <li><strong>code:</strong> mã kích hoạt cho khóa học</li>                      
                    </ul>                  
                    <p class="m-1">Sử dụng: Nếu bạn muốn sử dụng tham số <strong>course</strong>, hãy đặt nó trong dấu ngoặc nhọn như sau: <code>{course}</code>.</p>
            
                </div>
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
    $(document).ready(function() {
        handleRadioClick();
        $('#manual-support, #api-support').click(function() {
            handleRadioClick();
        });
    });

    function handleRadioClick() {
        if ($('#manual-support').is(':checked')) {
            $('#manual-form').show();
            $('#api-form').hide();
        } else {
            $('#manual-form').hide();
            $('#api-form').show();
        }
    }
</script>
@endsection