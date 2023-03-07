@inject('itemServ','App\Services\ItemServices')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header font-weight-bold">Kế hoạch học tập</div>
            <div class="card-body">
            </div>
        </div>
        <div class="card shadow mt-3">
            <div class="card-header font-weight-bold">Lập/Chỉnh sửa kế hoạch học tập</div>
            <div class="card-body">
                <form method="POST">
                    @csrf
                    <div class="form-group ">
                        <label for="opening_title" class="form-label text-md-right">{{ __('Tiêu đề') }}*</label>
                        <div class="">
                            <input id="opening_title" type="text" class="form-control @error('opening_title') is-invalid @enderror" name="opening[title]" value="{{ old('opening_title', !empty($opening) ? $opening->title : '') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="province" class=" form-label text-md-right">{{ __('Địa chỉ / Chi nhánh') }}</label>
                        <div class="">
                            @if (empty($userLocations) || count($userLocations) == 0)
                            <p>@lang('Bạn chưa khởi tạo địa chỉ.') <a href="{{ route('location.create') }}">@lang('Tạo địa chỉ')</a></p>
                            @else
                            <select class="form-control" name="opening[location_id]" required>
                                @foreach($userLocations as $location)
                                <option value="{{ $location->id }}">{{ $location->title }}</option>
                                @endforeach
                            </select>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="weekdays" class="form-label ">{{ __('Ngày trong tuần') }}*</label>
                        <ul class="list-inline">
                            @for($i=1; $i<=7; $i++) <li class="list-inline-item"><input type="checkbox" name="d{{ $i }}" id="d{{ $i}}"> <label for="d{{  $i }}">{{ $i == 1 ? __('Chủ Nhật') : __("Thứ " . ($i)) }}</label> </li>
                                @endfor
                        </ul>
                    </div>
                    <div class="row">
                        <div class="form-group  col-sm-6">
                            <label for="opening_date" class="form-label text-md-right">{{ __('Ngày bắt đầu') }}*</label>
                            <div class="">
                                <input id="opening_date" type="date" class="form-control @error('opening_date') is-invalid @enderror" name="opening[date_start]" value="{{ old('opening_date', !empty($opening) ? $opening->date_start : '') }}">
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="opening_date" class="form-label text-md-right">{{ __('Giờ bắt đầu') }}*</label>
                            <div class="">
                                <input id="opening_date" type="time" class="form-control @error('opening_date') is-invalid @enderror" name="opening[date_start]" value="{{ old('opening_date', !empty($opening) ? $opening->date_start : '') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="opening_date" class="form-label text-md-right">{{ __('Ngày kết thúc') }}</label>
                            <div class="">
                                <input id="opening_date" type="date" class="form-control @error('opening_date') is-invalid @enderror" name="opening[date_start]" value="{{ old('opening_date', !empty($opening) ? $opening->date_start : '') }}">
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="opening_date" class="form-label text-md-right">{{ __('Giờ kết thúc') }}</label>
                            <div class="">
                                <input id="opening_date" type="time" class="form-control @error('opening_date') is-invalid @enderror" name="opening[date_start]" value="{{ old('opening_date', !empty($opening) ? $opening->date_start : '') }}">
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-success border-0">@lang('Lưu')</button>
                </form>
            </div>
        </div>
    </div>
</div>