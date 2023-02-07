@inject('userServ', 'App\Services\UserServices')
@inject('itemServ', 'App\Services\ItemServices')
    <div class="row">
        <div class="col-12 p-4">
            <h5>@lang('Thông tin chính')
                <button class="btn btn-sm btn-success border-0 rounded float-right" name="tab" value="info"><i class="fas fa-save"></i> @lang('Lưu thay đổi')</button>
            </h5>
            <div class="card p-3 mb-3 shadow">
                @if($userServ->isMod())
                <div class="form-group row">
                    <label for="user_id" class="col-md-3 col-form-label text-md-right  font-weight-bold">{{ __('Đối tác') }}</label>
                    <div class="col-md-8">
                        <select class="form-control" name="user_id" id="user_id" required @if(!empty($course)) disabled @endif>
                            <option value="">@lang('Chọn một đối tác')</option>
                            @foreach($partners as $partner)
                                <option value="{{ $partner->id }}" {{ !empty($course) && $course['info']->user_id == $partner->id ? 'selected' : '' }}>{{ $partner->name }}</option>
                            @endforeach
                           </select>
                    </div>
                </div>
                @endif
                <div class="form-group row">
                    <label for="subtype" class="col-md-3 col-form-label text-md-right  font-weight-bold">{{ __('Loại khoá học') }}</label>
                    <div class="col-md-8">
                        <select class="form-control" name="subtype" required @if(!empty($course) && $course['info']->subtype != "") disabled @endif>
                            <option value="{{ \App\Constants\ItemConstants::SUBTYPE_OFFLINE }}" {{ !empty($course) && $course['info']->subtype == \App\Constants\ItemConstants::SUBTYPE_OFFLINE ? 'selected' : '' }}>@lang('Khoá học Chính khóa Tại trung tâm')</option>
                            <option value="{{ \App\Constants\ItemConstants::SUBTYPE_EXTRA }}" {{ !empty($course) && $course['info']->subtype == \App\Constants\ItemConstants::SUBTYPE_EXTRA ? 'selected' : '' }}>@lang('Khoá học Ngoại khóa')</option>
                            <option value="{{ \App\Constants\ItemConstants::SUBTYPE_ONLINE }}" {{ !empty($course) && $course['info']->subtype == \App\Constants\ItemConstants::SUBTYPE_ONLINE ? 'selected' : '' }}>@lang('Khoá học Online')</option>
                            <option value="{{ \App\Constants\ItemConstants::SUBTYPE_DIGITAL }}" {{ !empty($course) && $course['info']->subtype == \App\Constants\ItemConstants::SUBTYPE_DIGITAL ? 'selected' : '' }}>@lang('Mã code Ứng dụng khác')</option>
                            <option value="{{ \App\Constants\ItemConstants::SUBTYPE_VIDEO }}" {{ !empty($course) && $course['info']->subtype == \App\Constants\ItemConstants::SUBTYPE_VIDEO ? 'selected' : '' }}>@lang('Khóa học video')</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card mb-3 shadow">
                <div class="card-header font-weight-bold">Thông tin chính</div>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="title" class="col-md-3 col-form-label text-md-right ">{{ __('Tiêu đề') }}</label>
                        <div class="col-md-8">
                            <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title[{{ App\Models\I18nContent::DEFAULT }}]" value="{{ old('title', !empty($course) ? $course['info']->title[App\Models\I18nContent::DEFAULT] : '') }}" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="categories" class="col-md-3 col-form-label text-md-right ">{{ __('Chuyên mục') }}</label>
                        <div class="col-md-8">
                            <select class="form-control" name="categories[]" multiple>
                                @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ !empty($itemCategories) && in_array($category->id, $itemCategories) ? 'selected' : '' }}>
                                    {{ $category->title }}
                                </option>
                                @endforeach
                            </select>
                            <p class="small">@lang('Có thể chọn nhiều chuyên mục bằng giữ phím Ctrl (hoặc Cmd)')</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="date_start" class="col-md-3 col-form-label text-md-right ">{{ __('Ngày bắt đầu') }}</label>
                        <div class="col-md-8">
                            <input id="date_start" type="date" class="form-control @error('date_start') is-invalid @enderror" name="date_start" value="{{ old('date_start', !empty($course) ? $course['info']->date_start : '') }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="time_start" class="col-md-3 col-form-label text-md-right ">{{ __('Thời gian bắt đầu') }}</label>
                        <div class="col-md-8">
                            <input id="time_start" type="time" class="time form-control @error('time_start') is-invalid @enderror" name="time_start" value="{{ old('time_start', !empty($course) ? $course['info']->time_start : '') }}" placeholder="hh:mm" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="nolimit_time" class="col-md-3 col-form-label text-md-right ">{{ __('Không giới hạn') }}</label>
                        <div class="col-md-8 form-check form-switch m-2">
                            <input class="form-check-input" type="checkbox" name="nolimit_time" id="nolimit_time" {{ !empty($course) && $course['info']->nolimit_time > 0 ? "checked" : "" }}>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="age-range" class="col-md-3 col-form-label text-md-right ">{{ __('Độ tuổi') }}</label>
                        <div class="col-md-8">
                            <input type="text" id="age-amount" name="ages_range" readonly style="border:0;" data-ages_min="{{ !empty($course['info']->ages_min) ? $course['info']->ages_min : 0 }}" data-ages_max="{{ !empty($course['info']->ages_max) ? $course['info']->ages_max : 30 }}">
                            <div id="age-range" class="mt-2"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="tags" class="col-md-3 col-form-label text-md-right ">{{ __('Tags') }}</label>
                        <div class="col-md-8">
                            <input id="tags" type="text" class="form-control @error('tags') is-invalid @enderror" name="tags" value="{{ old('tags', !empty($course) ? $course['info']->tags : '') }}">
                        </div>
                    </div>

                    <div class="form-group row" id="locationbox" {{ !empty($course) && !empty($course['info']->subtype) && $course['info']->subtype != \App\Constants\ItemConstants::SUBTYPE_ONLINE ? 'style=display:none' : '' }}>
                        <label for="location" class="col-md-3 col-form-label text-md-right ">{{ __('URL Room học') }}</label>
                        <div class="col-md-8">
                            <input id="location_type" type="hidden" name="location_type" value="offline">
                            <input id="location" type="text" class="form-control @error('location') is-invalid @enderror" name="location" value="{{ old('location', !empty($course) ? $course['info']->location : '') }}">
                        </div>
                    </div>
                </div>
            </div>

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
                            <input id="price" onchange="hp();" min="0" type="number" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ old('price', !empty($course) ? $course['info']->price : '') }}" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="org_price" class="col-md-3 col-form-label text-md-right ">{{ __('Học phí gốc') }}</label>
                        <div class="col-md-8">
                            <input id="org_price" onchange="hp();" min="0" type="number" class="form-control @error('org_price') is-invalid @enderror" name="org_price" value="{{ old('org_price', !empty($course) ? $course['info']->org_price : '') }}" required>
                        </div>

                    </div>

                    <div class="form-group row">
                        <label for="quantity" class="col-md-3 col-form-label text-md-right ">{{ __('Số lượng') }}</label>
                        <div class="col-md-8">
                            <input id="quantity" type="number" class="form-control @error('quantity') is-invalid @enderror" name="seats" value="{{ old('quantity', !empty($course) ? $course['info']->seats : '') }}">
                        </div>
                    </div>
                </div>
            </div>
            @if ($userServ->isMod() || $userServ->haveAccess(Auth::user()->role, 'user.sale'))
            <div class="card mb-3 shadow">
                <div class="card-header  font-weight-bold">Thông tin hệ thống</div>
                <div class="card-body">
                    @if ($userServ->haveAccess(Auth::user()->role, 'user.sale'))
                    <div class="form-group row">
                        <label for="sale_id" class="col-md-3 col-form-label text-md-right">{{ __('ID Sale chăm sóc') }}</label>
                        <div class="col-md-8">
                            <input id="sale_id" type="text" class="form-control @error('sale_id') is-invalid @enderror" name="sale_id" value="{{ old('sale_id', !empty($course) ? $course['info']->sale_id : '') }}">
                            <small>@lang('Có thể tham khảo ID ngoài danh sách nhân viên.')</small>
                        </div>
                    </div>
                    @endif
                    @if ($userServ->isMod())
                    <div class="form-group row">
                        <label for="commission_rate" class="col-md-3 col-form-label text-md-right ">{{ __('Hoa hồng người bán') }}</label>
                        <div class="col-md-8">
                            <input id="commission_rate" type="text" class="form-control @error('commission_rate') is-invalid @enderror" name="commission_rate" value="{{ old('commission_rate', !empty($course) ? $course['info']->commission_rate : '') }}">
                            <div class="small">@lang('Số thập phân, để trống nếu không thay đổi so với tỉ lệ trong hợp đồng.')</div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="company_rate" class="col-md-3 col-form-label text-md-right ">{{ __('Hoa hồng công ty') }}</label>
                        <div class="col-md-8">
                            @if ($companyCommission != null)
                            @foreach ($companyCommission as $key => $config)
                            @if ($config != null)
                            {{ $key }}= {{ $config }};
                            @endif
                            @endforeach
                            @endif
                            <a class="btn btn-sm btn-danger" id="companyCommission-action" href="#"><i class="fa fa-edit"></i></a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <div class="card mb-3 shadow">
                <div class="card-header  font-weight-bold">Thông tin giới thiệu</div>
                <div class="card-body">

                    <ul class="nav nav-tabs" id="i18ntab" role="tablist">
                        @foreach (App\Models\I18nContent::$supports as $locale)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $locale == App\Models\I18nContent::DEFAULT ? 'active' : '' }}" id="{{ $locale }}-tab" data-toggle="tab" data-bs-toggle="tab" data-target="#{{ $locale }}box" data-bs-target="#{{ $locale }}box" type="button" role="tab" aria-controls="{{ $locale }}" aria-selected="{{ $locale == App\Models\I18nContent::DEFAULT ? 'true' : 'false' }}">{{ $locale }}</button>
                        </li>
                        @endforeach
                    </ul>
                    <div class="tab-content border-0" id="i18ntabContent">
                        <p class="text-warning">! Hiện tại chức năng tải ảnh lên vẫn đang hoàn hiện, vui lòng nhấn nút mũi tên bên cạnh icon để chèn link ảnh hoặc dán trực tiếp link ảnh vào form nhập liệu.</p>
                        @foreach (App\Models\I18nContent::$supports as $locale)
                        <div class="p-3 tab-pane fade {{ $locale == App\Models\I18nContent::DEFAULT ? 'show active' : '' }}" id="{{ $locale }}box" role="tabpanel" aria-labelledby="{{ $locale }}-tab">
                            @if ($locale != App\Models\I18nContent::DEFAULT)
                            <div class="form-group">
                                <label for="title{{ $locale }}" class="col-form-label font-weight-bold">{{ __('Tiêu đề') }}
                                    [{{ $locale }}]</label>
                                <div class="">
                                    <input id="title{{ $locale }}" type="text" class="form-control" name="title[{{ $locale }}]" value="{{ old('title', !empty($course) ? $course['info']->title[$locale] : '') }}">
                                </div>
                            </div>
                            @endif

                            <div class="form-group ">
                                <label for="short_content" class="col-form-label font-weight-bold">{{ __('Thông tin tóm tắt') }}
                                    [{{ $locale }}]</label>
                                <div class="">
                                    <textarea name="short_content[{{ $locale }}]" class="editor">{{ old('short_content', !empty($course) ? $course['info']->short_content[$locale] : '') }}</textarea>
                                </div>
                            </div>
                            @foreach(App\Services\ItemServices::$CONTENT_FIELDS as $type => $name)
                            @include('class.form.content_box', ['boxType' => $type, 'boxName' => $name, 'locale' => $locale, 'data' => !empty($course['info']->content[$locale][$type]) ? $course['info']->content[$locale][$type] : ''])
                            @endforeach

                        </div>
                        @endforeach

                    </div>
                </div>

                <div class="text-center mb-3">
                    <button class="btn btn-success border-0 rounded" name="tab" value="info"><i class="fas fa-save"></i> @lang('Lưu thay đổi')</button>
                </div>
            </div>
        </div>
    </div>

@section('jscript')
<script>
    function hp() {
        var hp = document.getElementById("price").value;
        var hpg = document.getElementById("org_price").value;
        if (hpg < hp) {
            document.getElementById("org_price").value = hp;
        }
    }
</script>
@endsection
@include('dialog.company_commission')
