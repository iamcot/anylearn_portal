@inject('userServ','App\Services\UserServices')
@extends('layout')

@section('body')
<form>
    <div class="card shadow mb-2">
        <div class="card-body row">
            <div class="col-xs-6 col-lg-4 ">
                <div class="form-group row">
                    <label class="col-12" for="">ID(s) <span class="small">Để trống đến ID nếu chỉ tìm 1</span></label>
                    <div class="col-lg-6 mb-1">
                        <input value="{{ app('request')->input('id_f') }}" type="text" class="form-control" name="id_f" placeholder="từ ID " />
                    </div>
                    <div class="col-lg-6">
                        <input value="{{ app('request')->input('id_t') }}" type="text" class="form-control" name="id_t" placeholder="đến ID" />
                    </div>
                </div>

            </div>
            <div class="col-xs-6 col-lg-4">
                <div class="form-group">
                    <label for="">Tên thành viên</label>
                    <input value="{{ app('request')->input('name') }}" type="text" class="form-control" name="name" placeholder="Tên thành viên" />
                </div>
            </div>
            <div class="col-xs-6 col-lg-4">
                <div class="form-group">
                    <label for="">Vai trò</label>
                    <select class="form-control" name="role" id="">
                        <option value="">---</option>
                        <option {{ app('request')->input('role') == 'member' ? 'selected' : '' }} value="member">Thành viên</option>
                        <option {{ app('request')->input('role') == 'school' ? 'selected' : '' }} value="school">Trung tâm</option>
                        <option {{ app('request')->input('role') == 'teacher' ? 'selected' : '' }} value="teacher">Chuyên gia</option>
                    </select>
                </div>
            </div>
            <div class="col-xs-6 col-lg-4">
                <div class="form-group">
                    <label for="">SDT</label>
                    <input value="{{ app('request')->input('phone') }}" type="text" class="form-control" name="phone" placeholder="SDT" />
                </div>
            </div>
            <div class="col-xs-6 col-lg-2">
                <div class="form-group">
                    <label for="">ID Người giới thiệu</label>
                    <input value="{{ app('request')->input('ref_id') }}" type="text" class="form-control" name="ref_id" placeholder="ID người giới thiệu" />
                </div>
            </div>
            <div class="col-xs-6 col-lg-3">
                <div class="form-group">
                    <label for="">Thời gian tạo từ</label>
                    <input value="{{ app('request')->input('date') }}" type="date" class="form-control" name="date" placeholder="Thời gian tạo" />
                </div>
            </div>
            <div class="col-xs-6 col-lg-3">
                <div class="form-group">
                    <label for="">Thời gian tạo đến</label>
                    <input value="{{ app('request')->input('datet') }}" type="date" class="form-control" name="datet" placeholder="Thời gian tạo đến" />
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-primary btn-sm" name="action" value="search"><i class="fas fa-search"></i> Tìm kiếm</button>
            <button class="btn btn-success btn-sm" name="action" value="file"><i class="fas fa-file"></i> Xuất file</button>
            <button class="btn btn-warning btn-sm" name="action" value="clear"> Xóa tìm kiếm</button>
        </div>
    </div>
</form>
@if(!$isSale)
<form method="post" enctype="multipart/form-data">
    @csrf
    <div class="card shadow mb-3">
        <div class="card-header">Phân khách cho sale
        <a href="/cdn/anylearn/example_saleassign.csv" download>File mẫu</a>

        </div>
        <div class="card-body">
            <div>
                <input type="file" name="saleassign" class="">
                <button class="btn btn-success btn-sm" name="action" value="saleassign">Tải lên</button>
            </div>
        </div>
    </div>
</form>
@endif
<div class="card shadow">
    <div class="card-header">
        Tổng tìm kiếm: <strong class="text-danger">{{ $members->total() }}</strong>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-striped table-hover table-bordered">
            <thead class="">
                <tr>
                    <th class="text-center" width="5%" scope="col">#ID</th>
                    <th class="text-center">Thao tác</th>
                    @if(!$isSale)
                    <th class="text-center">Hot</th>
                    <th class="text-center">Boost</th>
                    @endif
                    <th width="10%" scope="col">Vai trò</th>
                    <th width="15%" scope="col">Họ tên</th>
                    <th width="5%" scope="col">SDT</th>
                    <th width="5%" scope="col">Email</th>
                    <th width="10%" scope="col">Address</th>
                    <th>Ví C</th>
                    <th width="10%" scope="col">Người G/T</th>
                    <th width="5%" scope="col">H/H</th>
                    <th class="text-center" width="5%" scope="col">C/T</th>
                    <th class="text-center">Cập nhật</th>

                </tr>
            </thead>
            <tbody>
                @if(!empty($members))
                @foreach($members as $user)
                <tr>
                    <th class="text-center" scope="row">{{ $user->id }}</th>

                    <td class="text-right">
                        @if(!$isSale)
                        @if($user->id != 1)
                        {!! $userServ->statusOperation($user->id, $user->status) !!}
                        <a class="btn btn-sm btn-info mt-1" href="{{ route('user.members.edit', ['userId' => $user->id]) }}"><i class="fas fa-edit"></i> Sửa</a>
                        @endif
                        @endif
                        <a target="_blank" class="btn btn-sm btn-success mt-1" href="{{ route('crm.sale', ['userId' => $user->id]) }}"><i class="fas fa-briefcase"></i></a>
                    </td>

                    @if(!$isSale)
                    <td class="text-center"><a href="{{ route('ajax.touch.ishot', ['table' => 'users', 'id' =>  $user->id ]) }}">{!! $userServ->hotIcon($user->is_hot) !!}</a></td>
                    <td>{{ $user->boost_score }}</td>
                    @endif
                    <td>{{ $user->role }}</td>
                    <td>{!! $userServ->statusIcon($user->status) !!} {{ $user->name }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->address }}</td>
                    <td>{{ number_format($user->wallet_c) }}</td>
                    <td>{{ $user->refname ? $user->refname . ' (' . $user->refphone . ')' : '' }}</td>
                    <td>{{ $user->commission_rate * 100 }}%</td>
                    <td class="text-center">{!! $userServ->requiredDocIcon($user) !!}</td>
                    <td class="text-center">{{ date('H:i d/m/y', strtotime($user->updated_at)) }}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        <div class="small ml-3">
            <p><i class="fas fa-fire text-danger" title="Nổi bật"></i> Thành viên nổi bật. <i class="fas fa-check-circle text-success"></i> Thành viên đang hoạt động. <i class="fas fa-stop-circle text-danger"></i> Thành viên đang bị khóa.
                <i class="fas fa-cloud-upload-alt text-gray"></i> Giấy tờ chưa hợp lệ. <i class="fas fa-cloud-upload-alt text-success"></i> Đã cập nhật chứng chỉ, giấy tờ >>> Click để xem chi tiết.

            </p>
        </div>

    </div>
    <div class="card-footer">
        <div>{{ $members->appends(request()->query())->links() }}</div>
    </div>
</div>
@include('dialog.user_doc')
@endsection