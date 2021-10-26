@inject('itemServ','App\Services\ItemServices')
<div class="row">
    <div class="col-12 p-4">

        <h4>@lang('Lịch khai giảng')</h4>
        <hr />
        <div class="row">
            <div class="col-md-8 ">
                <div class="card shadow">
                    <div class="card-header font-weight-bold">Các khai giảng
                        <a class="float-right" href="?tab=schedule&a=create-opening"><i class="fas fa-plus"></i> @lang('Tạo khai giảng')</a>
                    </div>
                    <div class="card-body p-4">
                        @if(empty($openings) || count($openings) == 0 )
                        <p class=""><i class="fa fa-info-circle"></i> Khoá học này đang sử dụng lịch khai giảng mặc định. Bạn có thể tạo lịch học cho lịch khai giảng mặc định này bên dưới.</p>
                        <p class="text-danger"><i class="fa fa-exclamation-triangle"></i> Bạn chỉ tạo được khai giảng khi khoá học chưa có học viên đăng ký.</p>
                        <p class="text-danger"><i class="fa fa-exclamation-triangle"></i> Lịch học gắn với khai giảng mặc định đã tạo sẽ bị xoá bỏ và bạn cần tạo lại lịch học tương ứng cho mỗi đợt khai giảng.</p>
                        <p class="text-danger"><i class="fa fa-exclamation-triangle"></i> Lịch khai giảng tạo ra không thể xoá, chỉ có thể chỉnh sửa hoặc tắt/mở.</p>
                        @else
                        <table class="table table-striped">
                            <thead>
                                <th>ID</th>
                                <th>Tiêu đề</th>
                                <th>Địa chỉ</th>
                                <th>Ngày</th>
                                <th></th>
                            </thead>
                            <tbody>
                                @foreach($openings as $op)
                                <tr>
                                    <td>{{ $op->id }}</td>
                                    <td>
                                    <a href="?tab=schedule&op={{ $op->id }}"><i class="fa fa-edit"></i>
                                    {{ $op->title }} </a></td>
                                    <td>{{ $op->location }}</td>
                                    <td>{{ date('d/m/Y', strtotime( $op->date_start )) }}</td>
                                    
                                    <td class="text-right">
                                        <a href="?tab=schedule&op={{ $op->id }}#schedule" class="btn btn-info btn-sm">Lịch học</a>
                                    {!! $itemServ->userStatusOperation($op->id, $op->user_status) !!}
                                </td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-1">
                @if(request()->get('a') == 'create-opening' || !empty($opening))
                <div class="card shadow {{ request()->get('a') == 'create-opening' ? 'border border-danger' : '' }}">
                    <div class="card-header font-weight-bold">{{ !empty($opening) ? 'Cập nhật' : 'Tạo mới'  }}
                        <button class="btn btn-sm btn-primary float-right" name="tab" value="opening"><i class="fas fa-save"></i> @lang('Lưu khai giảng')</button>
                    </div>
                    <div class="card-body">
                        @if(!empty($opening))
                        <input type="hidden" name="opening[id]" value="{{ $opening->id }}">
                        @endif
                        <div class="form-group ">
                            <label for="opening_title" class="form-label text-md-right">{{ __('Tiêu đề') }}</label>
                            <div class="">
                                <input id="opening_title" type="text" class="form-control @error('opening_title') is-invalid @enderror" name="opening[title]" value="{{ old('opening_title', !empty($opening) ? $opening->title : '') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="province" class=" form-label text-md-right">{{ __('Địa chỉ / Chi nhánh') }}</label>
                            <div class="">
                                @if (empty($userLocations) || count($userLocations) == 0)
                                <p>Bạn chưa khởi tạo địa chỉ. <a href="{{ route('location.create') }}">Tạo địa chỉ</a></p>
                                @else
                                <select class="form-control" name="opening[location_id]" required>
                                    @foreach($userLocations as $location)
                                    <option value="{{ $location->id }}">{{ $location->title }}</option>
                                    @endforeach
                                </select>
                                @endif
                            </div>
                        </div>
                        <div class="form-group ">
                            <label for="opening_date" class="form-label text-md-right">{{ __('Ngày bắt đầu') }}</label>
                            <div class="">
                                <input id="opening_date" type="date" class="form-control @error('opening_date') is-invalid @enderror" name="opening[date_start]" value="{{ old('opening_date', !empty($opening) ? $opening->date_start : '') }}">
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-12 p-4" id="schedule">
        <h4>@lang('Lịch học') {{ !empty($opening) ? $opening->title : '' }}
            <!-- <button class="btn btn-sm btn-primary float-right" name="tab" value="schedule"><i class="fas fa-save"></i> @lang('Lưu Lịch học')</button> -->
        </h4>
        <hr />
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header font-weight-bold">Lịch đã tạo</div>
                    <div class="card-body p-0 table-responsive">
                        @if(empty($course['schedule']) || count($course['schedule']) == 0)
                            @if (!empty($openings) && count($openings) > 0 && empty($opening)) 
                            <p class="p-2 text-danger">Vui lòng chọn một khai giảng để tạo lịch học</p>
                            @else
                            <p class="text-danger p-2">Chưa có lịch học được tạo. Vui lòng sử dụng tính năng <strong>"Tạo/Cập nhật tự động"</strong> để khởi tạo lịch học cho khai giảng này.</p>
                            @endif
                        @endif
                        <table class="table table-striped ">
                            <thead>
                                <tr>
                                    <th>@lang('Ngày')</th>
                                    <th>@lang('Giờ bắt đầu')</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody id="schedule_result">
                                @if(!empty($course['schedule']))
                                @foreach($course['schedule'] as $date)
                                <tr>
                                    <td>
                                        <input type="hidden" name="schedule[{{ $loop->index }}][id]" value="{{ $date->id }}">
                                        <input type="date" class="form-control" name="schedule[{{ $loop->index }}][date]" value="{{ $date->date}}" id="schedule_{{ $loop->index }}_date">
                                    </td>
                                    <td><input type="text" class="time form-control" name="schedule[{{ $loop->index }}][time_start]" value="{{ $date->time_start}}" id="schedule_{{ $loop->index }}_time_start"></td>
                                    <td>
                                        @if($course['info']->subtype == \App\Constants\ItemConstants::SUBTYPE_ONLINE)
                                        @php
                                            $contentData = json_decode($date->content, true);
                                        @endphp
                                        <input placeholder="URL phòng học" type="text" class="form-control" name="schedule[{{ $loop->index }}][content][url]" value="{{ empty($contentData) ? '' : $contentData['url'] }}" id="schedule_{{ $loop->index }}_content_url">
                                        <input placeholder="Chỉ dẫn vào phòng học" type="text" class="form-control mt-1" name="schedule[{{ $loop->index }}][content][info]" value="{{ empty($contentData) ? '' : $contentData['info'] }}" id="schedule_{{ $loop->index }}_content_info">
                                        @else
                                            <textarea class="form-control" name="schedule[{{ $loop->index }}][content]"  rows="1">{{ $date->content}}</textarea>
                                        @endif

                                    </td>
                                </tr>

                                @endforeach
                                @endif
                              
                            </tbody>
                        </table>
                        @if(!empty($course['info']) && $course['info']->subtype == \App\Constants\ItemConstants::SUBTYPE_ONLINE)
                        <p class="text-secondary ml-2"><i class="fa fa-info-circle"></i> Nếu bạn muốn cập nhật đường link cho từng lịch học, Lưu lịch học trước.</p>
                        @endif
                    </div>
                    <div class="card-footer">
                        @if(!empty($course['schedule']))
                        <a href="{{ route('class.del.schedule', ['id' => !empty($opening) ? $opening->id : $course['info']->id ]) }}" class="text-danger">Xóa toàn bộ lịch học</a>
                        @endif
                        <button class="btn btn-sm btn-primary float-right" name="tab" value="schedule"><i class="fas fa-save"></i> @lang('Lưu')</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-1">
                <div class="card shadow">
                    <div class="card-header font-weight-bold">
                        Tạo/Cập nhật tự động
                        <a href="#schedule" id="schedule_generate" class="btn btn-sm btn-warning float-right" data-type="{{  isset($course['schedule']) && sizeof($course['schedule']) > 0 ? 'update' : 'create' }}">Tạo</a>
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
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>