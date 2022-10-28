<div class="row">
    <div class="col-12 p-5">
        <h4>@lang('Học viên đã đăng ký')
            <!-- <button class="btn btn-sm btn-primary float-right" name="tab" value="teachers"><i class="fas fa-save"></i> @lang('Lưu thay đổi')</button> -->
        </h4>
        <hr />
        @if(count($students) > 0)
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th>Tên học viên</th>
                    <th>Ngày đăng ký</th>
                    <th>HV xác nhận (buổi)</th>
                    <th>Nhận xét <br>Chứng chỉ</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            @foreach($students as $student)
            <tr>
                <td><input type="checkbox" name="student_ids[{{ $student->id }}]" id=""></td>
                <td>{{ $student->name }}</td>
                <td>{{ date('d/m/Y', strtotime($student->created_at)) }}</td>
                <td>{{ $student->confirm_count }}</td>
                <td>{{ $student->organizer_comment }}</td>

                <td>
                    <a href="{{ route('class.author.confirmjoin' , ['itemId' =>  $course['info']->id ]) }}?join_user={{ $student->id }}" class="btn btn-success btn-sm">Xác nhận tham gia</a>
                    <a href="" class="btn btn-warning btn-sm mt-1">Cấp chứng chỉ</a>
                </td>
            </tr>
            @endforeach
        </table>
        @else
        <p>@lang('Chưa có học viên nào đăng ký')</p>
        @endif
    </div>
</div>