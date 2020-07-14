@extends('layout')
@section('body')
@if(sizeof($feedbacks) == 0)
<div class="text-center mt-5 pt-5">
    Chưa có phản hồi nào 
</div>
@else
<div class="card shadow">
    <div class="card-body p-0">
        <table class="table table-bordered table-striped">
            <thead>
                <thead>
                    <th class="text-center">#ID</th>
                    <th>Thành viên</th>
                    <th>Nội dung</th>
                    <th>Screenshot</th>
                    <th>Thời gian</th>
                </thead>
            <tbody>
                @foreach($feedbacks as $feedback)
                <tr>
                    <th class="text-center">{{ $feedback->id }}</th>
                    <td>{{ $feedback->user->name }}</td>
                    <td>{{ $feedback->content }}</td>
                    <td><a target="_blank" href="{{ $feedback->file }}"><img style="height: 50px;" src="{{ $feedback->file }}" alt=""></a></td>
                    <td>{{ $feedback->created_at }}</td>
                </tr>
                @endforeach
            </tbody>
            </thead>
        </table>
    </div>
    <div class="card-footer">
        {{ $feedbacks->links() }}
    </div>
</div>

@endif

@endsection