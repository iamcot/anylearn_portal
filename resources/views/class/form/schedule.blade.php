<div class="row">
    <div class="col-12 p-5">
        <h4>@lang('Lịch học')
            <button class="btn btn-sm btn-primary float-right" name="tab" value="schedule"><i class="fas fa-save"></i> @lang('Lưu thay đổi')</button>
        </h4>
        <hr />
        <div class="row">
            <div class="col-6">
                <div class="card shadow">
                    <div class="card-header font-weight-bold">
                        Tạo/Cập nhật tự động
                        <a href="#" id="schedule_generate" class="btn btn-sm btn-warning float-right">Tạo</a>
                    </div>
                    <div class="card-body" id="schedule_auto">
                        <div class="form-group">
                            <label for="num" class="form-label">{{ __('Số buổi') }}</label>
                            <input id="num" type="number" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="weekdays" class="form-label">{{ __('Ngày trong tuần') }}</label>
                            <ul class="list-inline">
                                @for($i=0; $i<=6; $i++) 
                                <li class="list-inline-item"><input type="checkbox" name="d{{ $i }}" id="d{{ $i}}"> <label for="d{{  $i }}">{{ $i == 0 ? 'Chủ Nhật' : "Thứ " . ($i + 1) }}</label> </li>
                                @endfor 
                            </ul> 
                        </div> 
                        <div class="form-group">
                                    <label for="time_start" class="form-label">{{ __('Giờ bắt đầu') }}</label>
                                    <input id="time_start" type="text" class="time form-control">
                        </div>
                        <div class="form-group">
                            <label for="time_end" class="form-label">{{ __('Giờ kết thúc') }}</label>
                            <input id="time_end" type="text" class="time form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card shadow">
                    <div class="card-header font-weight-bold">Lịch đã tạo</div>
                    <div class="card-body" id="schedule_result"></div>
                </div>
            </div>
        </div>
    </div>
</div>