@inject('userServ', 'App\Services\UserServices')
@inject('itemServ', 'App\Services\ItemServices')

@extends('anylearn.me.layout')

@section('body')
    <ul class="nav nav-tabs" id="classtab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link text-secondary {{ session('tab', 'open') == 'done' ? 'active' : '' }}" id="done-tab"
                data-bs-toggle="tab" data-bs-target="#done" type="button" role="tab" aria-controls="done"
                aria-selected="true">@lang('Đã xong')</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-secondary {{ session('tab', 'open') == 'open' ? 'active' : '' }}" id="open-tab"
                data-bs-toggle="tab" data-bs-target="#open" type="button" role="tab" aria-controls="open"
                aria-selected="true">@lang('Đang mở')</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-secondary {{ session('tab', 'open') == 'fav' ? 'active' : '' }}" id="fav-tab"
                data-bs-toggle="tab" data-bs-target="#fav" type="button" role="tab" aria-controls="fav"
                aria-selected="true">@lang('Ưa thích')</button>
        </li>
    </ul>

    <div class="tab-content border border-top-0 mb-5 shadow bg-white" id="myTabContent">
        <div class="tab-pane fade {{ session('tab', 'open') == 'done' ? 'show active' : '' }} p-2" id="done"
            role="tabpanel" aria-labelledby="done-tab">
            @if (empty($orders['done']) || count($orders['done']) == 0)
                <form action="" method="">
                    <div class="input-group">
                        <div class="row">
                            <div class="input-group mb-3">
                                <div class="row mr-1">
                                    <div class="mx-auto">
                                        <div class="input-group">
                                            <!-- <input class="form-control border-end-0 border" name="search" type="search" value="{{ !empty($input) ? $input : null }}" placeholder="Tìm Khóa học" id="example-search-input"> -->
                                            <!-- <input class="form-control border-end-0 border" name="search" type="search" value="{{ !empty($input) ? $input : null }}" placeholder="Tìm Khóa học" id="example-search-input"> -->
                                            <input class="form-control border-end-0 border" name="search" type="search"
                                                value="{{ !empty($input) ? $input : null }}"
                                                placeholder="@lang('Tìm Khóa học')" id="example-search-input">
                                            <span class="input-group-append">
                                                <!-- <button class="btn btn-outline-secondary bg-white border-start-0  border ms-n5" type="submit"> -->
                                                <button id="sm"
                                                    class="btn btn-outline-secondary bg-white border-start-0  border ms-n5"
                                                    type="submit">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </span>
                                        </div>  
                                    </div>
                                </div>
                                <div class="input-group-append">
                                    &nbsp
                                    <select name="myselect" class="custom-select" id="inputGroupSelect04"
                                        onchange="this.form.submit()">
                                        <option value="all">@lang('Tất cả tài khoản học')</option>
                                        @foreach ($childuser as $item)
                                            <option <?php if ($inputselect == $item->id) {
                                                echo "selected='selected'";
                                            } ?> value="{{ $item->id }}"> {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                                &nbsp&nbsp
                                <!-- <a type="reset" href="/me/orders" style="margin-top:6px;text-decoration:none;">xóa bộ lọc</a> -->
                                <a type="reset" href="{{ route('me.orders') }}"
                                    style="margin-top:6px;text-decoration:none;">@lang('xóa bộ lọc')</a>
                            </div>
                        </div>
                    </div>
                </form>
                <table class="table  text-secondary table-hover">
                    <tr>
                        <p class="p-2">@lang('Không tìm thấy khóa học nào')</p>
                    </tr>
                </table>
            @else
                <form name="myform" action="" method="">
                    <div class="input-group">
                        <div class="row">
                            <div class="input-group mb-3">
                                <div class="row mr-1">
                                    <div class="mx-auto">
                                        <div class="input-group">
                                            <input class="form-control border-end-0 border" name="search" type="search"
                                                value="{{ !empty($input) ? $input : null }}" placeholder="@lang('Tìm Khóa học')"
                                                id="example-search-input">
                                            <span class="input-group-append">
                                                <button id="sm"
                                                    class="btn btn-outline-secondary bg-white border-start-0  border ms-n5"
                                                    type="submit">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="input-group-append">
                                    &nbsp
                                    <select name="myselect" class="custom-select" id="inputGroupSelect04"
                                        onchange="this.form.submit()">
                                        <option value="all">@lang('Tất cả tài khoản học')</option>
                                        @foreach ($childuser as $item)
                                            <option <?php if ($inputselect == $item->id) {
                                                echo "selected='selected'";
                                            } ?> value="{{ $item->id }}"> {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                                &nbsp&nbsp
                                <a type="reset" href="{{ route('me.orders') }}"
                                    style="margin-top:6px;text-decoration:none;">@lang('xóa bộ lọc')</a>
                            </div>
                        </div>
                    </div>
                </form>
                <table class="table text-secondary table-hover table-borderless">
                    <thead class="table-secondary text-secondary">
                        <tr>
                            <th class="border-0">@lang('Ngày đăng ký')</th>
                            <th class="border-0">@lang('Ngày hoàn thành')</th>
                            <th class="border-0">@lang('Tên khóa học')</th>
                            <th class="border-0">@lang('Tài khoản học')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders['done'] as $item)
                            <tr>
                                <td>{{ date('d/m', strtotime($item->date)) }}</td>
                                <td>{{ date('d/m', strtotime($item->date_end)) }}</td>
                                <td>
                                    <div class="">[{{ $item->item_subtype }}] {{ $item->title }}</div>
                                </td>
                                <td>
                                    <div class="">{{ $item->name }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            @endif
        </div>
        <div class="tab-pane fade {{ session('tab', 'open') == 'open' ? 'show active' : '' }} p-2" id="open"
            role="tabpanel" aria-labelledby="open-tab">
            @if (empty($orders['open']) || count($orders['open']) == 0)
                <form action="" method="">
                    <div class="input-group">
                        <div class="row">
                            <div class="input-group mb-3">
                                <div class="row mr-1">
                                    <div class="mx-auto">
                                        <div class="input-group">
                                            <input class="form-control border-end-0 border" name="search" type="search"
                                                value="{{ !empty($input) ? $input : null }}" placeholder="@lang('Tìm Khóa học')"
                                                id="example-search-input">
                                            <span class="input-group-append">
                                                <button id="sm"
                                                    class="btn btn-outline-secondary bg-white border-start-0  border ms-n5"
                                                    type="submit">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="input-group-append">
                                    <!--  &nbsp -->
                                    <select name="myselect" class="custom-select" id="inputGroupSelect04"
                                        onchange="this.form.submit()">
                                        <option value="all">@lang('Tất cả tài khoản học')</option>
                                        @foreach ($childuser as $item)
                                            <option <?php if ($inputselect == $item->id) {
                                                echo "selected='selected'";
                                            } ?> value="{{ $item->id }}"> {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                                &nbsp&nbsp
                                <a type="reset" href="{{ route('me.orders') }}"
                                    style="margin-top:6px;text-decoration:none;">@lang('xóa bộ lọc')</a>
                            </div>
                        </div>
                    </div>
                </form>
                <table class="table  text-secondary table-hover">
                    <tr>
                        <p class="p-2">@lang('Không tìm thấy khóa học nào')</p>
                    </tr>
                </table>
            @else
                <form action="" method="">
                    <div class="input-group">
                        <div class="row">
                            <div class="input-group mb-3">

                                <div class="row mr-1">
                                    <div class="mx-auto">
                                        <div class="input-group">
                                            <input class="form-control border-end-0 border" name="search" type="search"
                                                value="{{ !empty($input) ? $input : null }}" placeholder="@lang('Tìm Khóa học')"
                                                id="example-search-input">
                                            <span class="input-group-append">
                                                <button id="sm"
                                                    class="btn btn-outline-secondary bg-white border-start-0  border ms-n5"
                                                    type="submit">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="input-group-append">
                                    &nbsp
                                    <select name="myselect" class="custom-select" id="inputGroupSelect04"
                                        onchange="this.form.submit()">
                                        <option value="all">@lang('Tất cả tài khoản học')</option>
                                        @foreach ($childuser as $item)
                                            <option <?php if ($inputselect == $item->id) {
                                                echo "selected='selected'";
                                            } ?> value="{{ $item->id }}"> {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                                &nbsp&nbsp
                                <!-- <a type="reset" href="/me/orders" style="margin-top:6px;text-decoration:none;">xóa bộ lọc</a> -->
                                <a type="reset" href="{{ route('me.orders') }}"
                                    style="margin-top:6px;text-decoration:none;">@lang('xóa bộ lọc')</a>
                            </div>
                        </div>
                    </div>
                </form>
                <table class="table  text-secondary table-hover">
                    <thead class="table-secondary text-secondary">
                        <tr>
                            <th class="border-0">@lang('Ngày đăng ký')</th>
                            <th class="border-0">@lang('Tên khóa học')</th>
                            <th class="border-0">@lang('Tài khoản học')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders['open'] as $item)
                            <tr>
                                <td width="18%">{{ date('d/m', strtotime($item->date)) }} {{ $item->time }}</td>
                                <td>
                                    <div class="">[{{ $item->item_subtype }}] {{ $item->title }}</div>
                                </td>
                                <td>
                                    <div class="">{{ $item->name }}</div>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            @endif
        </div>
        <div class="tab-pane fade {{ session('tab', 'open') == 'fav' ? 'show active' : '' }} p-2" id="fav"
            role="tabpanel" aria-labelledby="fav-tab">
            @if (empty($orders['fav']) || count($orders['fav']) == 0)
                <p class="p-2">@lang('Bạn chưa đánh dấu khoá học nào là ưa thích.')</p>
            @else
                <table class="table text-secondary table-hover">
                    <thead class="table-secondary text-secondary">
                        <tr>
                            <td>@lang('Tên khóa học')</td>
                            <td>@lang('Hành động')</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders['fav'] as $item)
                            <tr>
                                <td>
                                    <div class="">[{{ $item->item_subtype }}] {{ $item->title }}</div>
                                </td>
                                <td>
                                    <a href="{{ $itemServ->classUrl($item->item_id) }}" target="_BLANK"
                                        class="btn btn-success btn-sm border-0 rounded-pill">@lang('ĐĂNG KÝ')</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            @endif
        </div>
    </div>
@endsection
<script>
    function customReset() {
        document.getElementById("name").value = "";
        document.getElementById("country").value = "";
    }
</script>
