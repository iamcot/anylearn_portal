@inject('userServ','App\Services\UserServices')
@inject('itemServ','App\Services\ItemServices')
@extends('anylearn.me.layout')

@section('body')
<ul class="nav nav-tabs" id="classtab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link text-secondary {{ session('tab', 'open') == 'done' ? 'active' : '' }}" id="done-tab" data-bs-toggle="tab" data-bs-target="#done" type="button" role="tab" aria-controls="done" aria-selected="true">Đã xong</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link text-secondary {{ session('tab', 'open') == 'open' ? 'active' : '' }}" id="open-tab" data-bs-toggle="tab" data-bs-target="#open" type="button" role="tab" aria-controls="open" aria-selected="true">Đang mở</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link text-secondary {{ session('tab', 'open') == 'fav' ? 'active' : '' }}" id="fav-tab" data-bs-toggle="tab" data-bs-target="#fav" type="button" role="tab" aria-controls="fav" aria-selected="true">Ưa thích</button>
    </li>
</ul>

<div class="tab-content border border-top-0 mb-5 shadow bg-white" id="myTabContent">
    <div class="tab-pane fade {{ session('tab', 'open') == 'done' ? 'show active' : '' }} p-2" id="done" role="tabpanel" aria-labelledby="done-tab">
        @if(empty($orders['done'])  || count($orders['done']) == 0)
        <p class="p-2">Bạn chưa có khoá học nào hoàn thành.</p>
        @else
        <table class="table text-secondary table-hover table-borderless">
            @foreach($orders['done'] as $item)
            <tr>
                <td>{{ date("d/m", strtotime($item->date) ) }}</td>
                <td>
                    <div class="">[{{ $item->item_subtype }}] {{ $item->title }}</div>

                </td>
                <td>

                </td>
            </tr>
            @endforeach
        </table>
        @endif
    </div>
    <div class="tab-pane fade {{ session('tab', 'open') == 'open' ? 'show active' : '' }} p-2" id="open" role="tabpanel" aria-labelledby="open-tab">
        @if(empty($orders['open']) || count($orders['open']) == 0)
        <p class="p-2">Bạn không có khoá học nào đang diễn ra.</p>
        @else
        <table class="table  text-secondary table-hover table-borderless">
            @foreach($orders['open'] as $item)
            <tr>
                <td width="15%">{{ date("d/m", strtotime($item->date) ) }}  {{ $item->time }}</td>
                <td>
                    <div class="">[{{ $item->item_subtype }}] {{ $item->title }}</div>

                </td>
                <td>

                </td>
            </tr>
            @endforeach
        </table>
        @endif
    </div>
    <div class="tab-pane fade {{ session('tab', 'open') == 'fav' ? 'show active' : '' }} p-2" id="fav" role="tabpanel" aria-labelledby="fav-tab">
        @if(empty($orders['fav']) || count($orders['fav']) == 0)
         <p class="p-2">Bạn chưa đánh dấu khoá học nào là ưa thích.</p>
        @else
        <table class="table text-secondary table-hover table-borderless">
            @foreach($orders['fav'] as $item)
            <tr>
                <td>
                    <div class="">[{{ $item->item_subtype }}] {{ $item->title }}</div>
                </td>
                <td>
                    <a href="{{ $itemServ->classUrl($item->item_id) }}" target="_BLANK" class="btn btn-success btn-sm border-0 rounded-pill">ĐĂNG KÝ</a>
                </td>
            </tr>
            @endforeach
        </table>
        @endif
    </div>
</div>
@endsection