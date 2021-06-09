@inject('userServ','App\Services\UserServices')
<div class="row">
    <div class="col-12 p-5">
        <h4>@lang('Thông tin cơ bản lớp học')
            <button class="btn btn-sm btn-primary float-right" name="tab" value="info"><i class="fas fa-save"></i> @lang('Lưu thay đổi')</button>
        </h4>
        <hr />
        <div class="form-group row">
            <label for="title" class="col-md-3 col-form-label text-md-right font-weight-bold">{{ __('Tiêu đề') }}</label>
            <div class="col-md-8">
                <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', !empty($course) ? $course['info']->title : '') }}" required>
            </div>
        </div>

        <div class="form-group row">
            <label for="price" class="col-md-3 col-form-label text-md-right font-weight-bold">{{ __('Học phí') }}</label>
            <div class="col-md-8">
                <input id="price" type="number" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ old('price', !empty($course) ? $course['info']->price : '') }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="org_price" class="col-md-3 col-form-label text-md-right font-weight-bold">{{ __('Học phí gốc') }}</label>
            <div class="col-md-8">
                <input id="org_price" type="number" class="form-control @error('org_price') is-invalid @enderror" name="org_price" value="{{ old('org_price', !empty($course) ? $course['info']->org_price : '') }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="commission_rate" class="col-md-3 col-form-label text-md-right font-weight-bold">{{ __('Hoa hồng người bán') }}</label>
            <div class="col-md-8">
                <input id="commission_rate" type="text" class="form-control @error('commission_rate') is-invalid @enderror" name="commission_rate" value="{{ old('commission_rate', !empty($course) ? $course['info']->commission_rate : '') }}">
                <div class="small">Số thập phân, để trống nếu không thay đổi so với tỉ lệ trong hợp đồng.</div>
            </div>
        </div>
        @if($userServ->isMod())
        <div class="form-group row">
            <label for="company_rate" class="col-md-3 col-form-label text-md-right font-weight-bold">{{ __('Hoa hồng công ty') }}</label>
            <div class="col-md-8">
                @if($companyCommission != null) 
                    @foreach($companyCommission as $key => $config)
                        @if ($config != null) 
                        {{ $key }}= {{ $config }};
                        @endif
                    @endforeach
                @endif
                <a class="btn btn-sm btn-danger" id="companyCommission-action" href="#"><i class="fa fa-edit"></i></a>
            </div>
        </div>
        @endif
        <div class="form-group row">
            <label for="date_start" class="col-md-3 col-form-label text-md-right font-weight-bold">{{ __('Ngày bắt đầu') }}</label>
            <div class="col-md-8">
                <input id="date_start" type="date" class="form-control @error('date_start') is-invalid @enderror" name="date_start" value="{{ old('date_start', !empty($course) ? $course['info']->date_start : '') }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="time_start" class="col-md-3 col-form-label text-md-right font-weight-bold">{{ __('Thời gian bắt đầu') }}</label>
            <div class="col-md-8">
                <input id="time_start" type="text" class="time form-control @error('time_start') is-invalid @enderror" name="time_start" value="{{ old('time_start', !empty($course) ? $course['info']->time_start : '') }}" placeholder="hh:mm" required>
            </div>
        </div>
        <div class="form-group row">
            <label for="nolimit_time" class="col-md-3 col-form-label text-md-right font-weight-bold">{{ __('Không giới hạn kết thúc') }}</label>
            <div class="col-md-8">
                <input type="checkbox" name="nolimit_time" id="nolimit_time" {{ !empty($course) && $course['info']->nolimit_time > 0 ? "checked" : "" }}>
            </div>
        </div>
        <div class="form-group row">
            <label for="date_end" class="col-md-3 col-form-label text-md-right font-weight-bold">{{ __('Ngày kết thúc') }}</label>
            <div class="col-md-8">
                <input id="date_end" type="date" class="form-control @error('date_end') is-invalid @enderror" name="date_end" value="{{ old('date_end', !empty($course) ? $course['info']->date_end : '') }}">
            </div>
        </div>

        <div class="form-group row">
            <label for="location" class="col-md-3 col-form-label text-md-right font-weight-bold">{{ __('Địa điểm học') }}</label>
            <div class="col-md-8">
                <input id="location_type" type="hidden" name="location_type" value="offline">
                <input id="location" type="text" class="form-control @error('location') is-invalid @enderror" name="location" value="{{ old('location', !empty($course) ? $course['info']->location : '') }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="tags" class="col-md-3 col-form-label text-md-right font-weight-bold">{{ __('Tags') }}</label>
            <div class="col-md-8">
                <input id="tags" type="text" class="form-control @error('tags') is-invalid @enderror" name="tags" value="{{ old('tags', !empty($course) ? $course['info']->tags : '') }}">
            </div>
        </div>

        <div class="form-group row">
            <label for="short_content" class="col-md-3 col-form-label text-md-right font-weight-bold">{{ __('Thông tin tóm tắt') }}</label>
            <div class="col-md-8">
                <textarea name="short_content" class="form-control">{{ old('short_content', !empty($course) ? $course['info']->short_content : '') }}</textarea>
            </div>
        </div>

        <div class="form-group row">
            <label for="editor" class="col-md-3 col-form-label text-md-right font-weight-bold @error('content') is-invalid @enderror">{{ __('Thông tin chi tiết') }}</label>
            <div class="col-md-8">
                <textarea id="editor" name="content">{!! old('content', !empty($course) ? $course['info']->content : '') !!}</textarea>
            </div>
        </div>
    </div>
</div>
@include('dialog.company_commission')