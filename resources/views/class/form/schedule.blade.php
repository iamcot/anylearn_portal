<div class="row">
    <div class="col-12 p-5">
        <h4>@lang('Lịch học')
            <button class="btn btn-sm btn-primary float-right" name="tab" value="schedule"><i class="fas fa-save"></i> @lang('Lưu thay đổi')</button>
        </h4>
        <hr />
        <div class="row">
            <div class="col-6">
                <div class="card shadow">
                    <div class="card-header font-weight-bold">Lịch đã tạo</div>
                    <div class="card-body p-0">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('Ngày')</th>
                                    <th>@lang('Giờ bắt đầu')</th>
                                    <th>@lang('Giờ kết thúc')</th>
                                    <th width="10%"></th>
                                </tr>
                            </thead>
                            <tbody id="schedule_result">
                                @if(!empty($course['schedule']))
                                @foreach($course['schedule'] as $date)
                                <tr>
                                    <td>
                                        <input type="hidden" name="schedule[{{ $loop->index }}][id]" value="{{ $date->id }}">
                                        <input type="date" class="form-control" name="schedule[{{ $loop->index }}][date]" value="{{ $date->date}}" id="schedule_{{ $loop->index }}_date"></td>
                                    <td><input type="text" class="time form-control" name="schedule[{{ $loop->index }}][time_start]" value="{{ $date->time_start}}" id="schedule_{{ $loop->index }}_time_start"></td>
                                    <td><input type="text" class="time form-control" name="schedule[{{ $loop->index }}][time_end]" value="{{ $date->time_end}}" id="schedule_{{ $loop->index }}_time_end"></td>
                                    <td></td>
                                </tr>

                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        @if(!empty($course['schedule']))
                        <a href="{{ route('class.del.schedule', ['id' => $course['info']->id]) }}" class="btn btn-sm btn-danger">Xóa toàn bộ lịch học</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card shadow">
                    <div class="card-header font-weight-bold">
                        Tạo/Cập nhật tự động
                        <a href="#" id="schedule_generate" class="btn btn-sm btn-warning float-right" data-type="{{  isset($course['schedule']) && sizeof($course['schedule']) > 0 ? 'update' : 'create' }}">Tạo</a>
                    </div>
                    <div class="card-body" id="schedule_auto">
                        <div class="form-group">
                            <label for="num" class="form-label font-weight-bold">{{ __('Số buổi') }}</label>
                            <input id="num" type="number" class="form-control" {{ isset($course['schedule']) && sizeof($course['schedule']) > 0 ? 'readonly' : ''}} value="{{ isset($course['schedule']) && sizeof($course['schedule']) > 0 ? sizeof($course['schedule']) : ''}}">
                            <small><i class="fas fa-info-circle"></i> Lưu ý: Số buổi chỉ được khởi tạo 1 lần duy nhất</small>
                        </div>
                        <div class="form-group">
                            <label for="weekdays" class="form-label  font-weight-bold">{{ __('Ngày trong tuần') }}</label>
                            <ul class="list-inline">
                                @for($i=0; $i<=6; $i++) <li class="list-inline-item"><input type="checkbox" name="d{{ $i }}" id="d{{ $i}}"> <label for="d{{  $i }}">{{ $i == 0 ? 'Chủ Nhật' : "Thứ " . ($i + 1) }}</label> </li>
                                    @endfor
                            </ul>
                        </div>
                        <div class="form-group">
                            <label for="time_start" class="form-label font-weight-bold">{{ __('Giờ bắt đầu') }}</label>
                            <input id="time_start" type="text" class="time form-control">
                        </div>
                        <div class="form-group">
                            <label for="time_end" class="form-label font-weight-bold">{{ __('Giờ kết thúc') }}</label>
                            <input id="time_end" type="text" class="time form-control">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>