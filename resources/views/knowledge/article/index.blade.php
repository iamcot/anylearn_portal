@extends('layout')
@section('rightFixedTop')
<form class="row">
    <div class="col-xs-2 mr-1">
        <a class="btn btn-success" href="{{ route('knowledge.edit') }}"><i class="fas fa-plus"></i> @lang('Thêm mới')</a>
    </div>
</form>
@endsection


@section('body')
@if(sizeof($articles) == 0)
<div class="text-center mt-5 pt-5">
    @lang('Chưa có bài viết nào.')
</div>
@else
<div class="card shadow">
    <div class="card-body p-0 table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <thead>
                    <th class="text-center">#ID</th>
                    @foreach(App\Models\I18nContent::$supports as $locale)
                    <th>@lang('Tiêu đề') [{{ $locale }}]</th>
                    @endforeach
                    <th>Tạo lúc</th>
                    <th>Thao tác</th>
                </thead>
            <tbody>
                @foreach($articles as $article)
                <tr>
                    <th class="text-center">{{ $article->id }}</th>
                    @foreach(App\Models\I18nContent::$supports as $locale)
                    <td>{{ $article->title[$locale] }}

                    </td>
                    @endforeach
                    <td>{{ $article->updated_at }}</td>
                    <td>
                        <a href="{{ route('knowledge.edit', ['id' => $article->id]) }}"><i class="fa fa-edit"></i></a>
                        <a class="btn btn-sm btn-{{ $article->status == 1 ? 'danger' : 'success' }}" href="{{ route('service.touch.status', ['table' => 'knowledges', 'id' => $article->id]) }}">
                            {{ $article->status == 1 ? 'Khóa' : 'Mở' }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
            </thead>
        </table>
    </div>
    <div class="card-footer">
        {{ $articles->links() }}
    </div>
</div>

@endif

@endsection
