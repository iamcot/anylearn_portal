@inject('userServ','App\Services\UserServices')
@extends('layout')

@section('rightFixedTop')
<form class="row">
    <div class="col-xs-4 mr-1">
        <select class="form-control" name="t" id="">
            <option {{ app('request')->input('t') == 'name' ? 'selected' : '' }} value="name">Tên Thành viên</option>
            <option {{ app('request')->input('t') == 'phone' ? 'selected' : '' }} value="phone">Số điện thoại</option>
            <option {{ app('request')->input('t') == 'role' ? 'selected' : '' }} value="role">Loại thành viên</option>
        </select>
    </div>
    <div class="col-xs-7 mr-1">
        <input value="{{ app('request')->input('s') }}" type="text" class="form-control" name="s" placeholder="{{ __('Tìm kiếm') }}" />
    </div>
    <div class="col-xs-1">
        <button class="btn btn-primary btn"><i class="fas fa-search"></i></button>
    </div>
</form>
@endsection

@section('body')
<div class="card shadow">
    <div class="card-body p-0 table-responsive">
        <table class="table table-striped table-hover table-bordered">
            <thead class="">
                <tr>
                    <th class="text-center" width="5%" scope="col">#ID</th>
                    <th class="text-center">Hot</th>
                    <th width="10%" scope="col">Vai trò</th>
                    <th width="15%" scope="col">Họ tên</th>
                    <th width="10%" scope="col">SDT</th>
                    <th>Ví M</th>
                    <th>Ví C</th>
                    <th width="10%" scope="col">Người G/T</th>
                    <th width="5%" scope="col">H/H</th>
                    <th class="text-center" width="5%" scope="col">C/T</th>
                    <th class="text-center">Cập nhật</th>
                    <th class="text-right" scope="col">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($members))
                @foreach($members as $user)
                <tr>
                    <th class="text-center" scope="row">{{ $user->id }}</th>
                    <td class="text-center"><a href="{{ route('ajax.touch.ishot', ['table' => 'users', 'id' =>  $user->id ]) }}">{!! $userServ->hotIcon($user->is_hot) !!}</a></td>
                    <td>{{ $user->role }}</td>
                    <td>{!! $userServ->statusIcon($user->status) !!} {{ $user->name }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>{{ number_format($user->wallet_m) }}</td>
                    <td>{{ number_format($user->wallet_c) }}</td>
                    <td>{{ $user->refuser ? $user->refuser->name . ' (' . $user->refuser->phone . ')' : '' }}</td>
                    <td>{{ $user->commission_rate * 100 }}%</td>
                    <td class="text-center">{!! $userServ->requiredDocIcon($user) !!}</td>
                    <td class="text-center">{{ date('H:i d/m/y', strtotime($user->updated_at)) }}</td>
                    <td class="text-right">
                        @if($user->id != 1)
                        {!! $userServ->statusOperation($user->id, $user->status) !!}
                        <a class="btn btn-sm btn-info" href="{{ route('user.members.edit', ['userId' => $user->id]) }}"><i class="fas fa-edit"></i> Sửa</a>
                        @endif
                    </td>
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
        <div>{{ $members->links() }}</div>
    </div>
</div>
@include('dialog.user_doc')
@endsection