@extends('anylearn.me.layout')
@section('spmb')
class_edit
@endsection
@section('rightFixedTop')
<a class="btn btn-sm btn-success border-0 rounded-pill" href="{{ route('me.class.create') }}"><i class="fas fa-plus"></i>
    <span class="mobile-no-text">@lang('Thêm mới')</span></a>
@endsection

@section('body')
@if (!empty($course) && $course['info']->subtype == 'offline' && count($userLocations) == 0)
<p class="bg-danger p-2 text-white">@lang('Bạn chưa khởi tạo một địa chỉ cho khoá học offline, Vui lòng') <a class="text-white" href="/location/create">@lang('cập nhật tại đây')</a></p>
@endif
<form action="" method="post" id="courseEditForm" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" value="{{ !empty($courseId) ? $courseId : 0 }}">
    <input type="hidden" name="action" value="{{ empty($courseId) ? 'create' : 'update' }}">
    <ul class="nav nav-tabs" id="classtab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link text-secondary fw-light {{ session('tab', 'info') == 'info' ? 'active' : '' }}" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true"><i class="fa fa-info-circle"></i> <span class="d-none d-sm-block">@lang('Thông tin')</span></button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-secondary fw-light {{ session('tab', 'info') == 'schedule' ? 'active' : '' }}" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" type="button" role="tab" aria-controls="schedule" aria-selected="true"><i class="fa fa-calendar-alt"></i> <span class="d-none d-sm-block">@lang('Lịch học')</span></button>
        </li>
        <li class="nav-item d-none" role="presentation" id="vdo">
            <button class="nav-link text-secondary fw-light {{ session('tab', 'info') == 'video' ? 'active' : '' }}" id="video-tab" data-bs-toggle="tab" data-bs-target="#video" type="button" role="tab" aria-controls="video" aria-selected="true"><i class="fas fa-play"></i> <span class="d-none d-sm-block">@lang('VIDEO')</span></button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-secondary fw-light {{ session('tab', 'info') == 'resource' ? 'active' : '' }}" id="resource-tab" data-bs-toggle="tab" data-bs-target="#resource" type="button" role="tab" aria-controls="resource" aria-selected="true"><i class="fa fa-image"></i> <span class="d-none d-sm-block">@lang('Hình ảnh')</span></button>
        </li>
        @if ($isSchool)
        <li class="nav-item" role="presentation">
            <button class="nav-link text-secondary fw-light {{ session('tab', 'info') == 'teachers' ? 'active' : '' }}" id="teachers-tab" data-bs-toggle="tab" data-bs-target="#teachers" type="button" role="tab" aria-controls="teachers" aria-selected="true"><i class="fa fa-chalkboard-teacher"></i> <span class="d-none d-sm-block">@lang('Giảng viên')</span></button>
        </li>
        @endif
        <li class="nav-item" role="presentation">
            <button class="nav-link text-secondary fw-light {{ session('tab', 'info') == 'seo' ? 'active' : '' }}" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button" role="tab" aria-controls="seo" aria-selected="true"><i class="fa fa-link"></i> <span class="d-none d-sm-block">@lang('SEO')</span></button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-secondary fw-light {{ session('tab', 'info') == 'registers' ? 'active' : '' }}" id="registers-tab" data-bs-toggle="tab" data-bs-target="#registers" type="button" role="tab" aria-controls="registers" aria-selected="true"><i class="fa fa-users-cog"></i> <span class="d-none d-sm-block">@lang('Đăng ký')</span></button>
        </li>
    </ul>

    <div class="tab-content border border-top-0 mb-5 shadow bg-white" id="myTabContent">
        <div class="tab-pane fade {{ session('tab', 'info') == 'info' ? 'show active' : '' }} p-2" id="info" role="tabpanel" aria-labelledby="info-tab">
            @include('class.form.info')
        </div>
        <div class="tab-pane fade {{ session('tab', 'info') == 'schedule' ? 'show active' : '' }} p-2" id="schedule" role="tabpanel" aria-labelledby="schedule-tab">
            @include('class.form.schedule')
        </div>
        <div class="tab-pane fade {{ session('tab', 'info') == 'video' ? 'show active' : '' }} p-2" id="video" role="tabpanel" aria-labelledby="video-tab">
            @include('class.form.video')
        </div>
        <div class="tab-pane fade {{ session('tab', 'info') == 'resource' ? 'show active' : '' }} p-2" id="resource" role="tabpanel" aria-labelledby="resource-tab">
            @include('class.form.resource')
        </div>
        @if ($isSchool)
        <div class="tab-pane fade {{ session('tab', 'info') == 'teachers' ? 'show active' : '' }} p-2" id="teachers" role="tabpanel" aria-labelledby="teachers-tab">
            @include('class.form.teachers')
        </div>
        @endif
        <div class="tab-pane fade {{ session('tab', 'info') == 'seo' ? 'show active' : '' }} p-2" id="seo" role="tabpanel" aria-labelledby="seo-tab">
            @include('class.form.seo')
        </div>
        <div class="tab-pane fade {{ session('tab', 'info') == 'registers' ? 'show active' : '' }} p-2" id="registers" role="tabpanel" aria-labelledby="registers-tab">
        </div>
    </div>


</form>
@endsection
@section('jscript')
<script src="/cdn/vendor/ckeditor/ckeditor.js"></script>
<script src="/cdn/vendor/jquery/jquery.mask.js"></script>
<script src="/cdn/vendor/jquery/speakingurl.min.js"></script>
<script>
    @foreach(App\ Models\ I18nContent::$supports as $locale)
    CKEDITOR.replace('editor{{ $locale }}');
    @endforeach
    $('.time').mask('00:00');

    function formatSlug(field, id) {
        $("#" + id).val(getSlug(field.value));
    }

    $('#courseSubmit').click(function() {
        $("#courseEditForm").submit();
    });

    $('#companyCommission-action').click(function() {
        $('#companyCommissionModal').modal('show');
    });

    $("select[name=subtype]").on("change", function(e) {
        var select = $(this).val();
        if (select == "online") {
            $("#locationbox").show();
        } else {
            $("#locationbox").hide();
        }
    });
    // $("select[name=subtype]").on("change", function(e) {
    //     var select = $(this).val();
    //     if (select == "video") {
    //         $("#vdo").removeClass("d-none");
    //     } else {
    //         $("#vdo").addClass("d-none");
    //     }
    // });
    $(document).ready(function () {
        var select = $("select[name=subtype]").val();
        if (select == "video") {
            $("#vdo").removeClass("d-none");
        }
    })
    $("#schedule_generate").click(function() {
        var type = $(this).data("type");
        var num = $("#schedule_auto").find('#num').val();
        var configDays = $("#schedule_auto input:checkbox:checked").map(function() {
            return $(this).attr('name');
        }).get();
        var startDate = $("#opening_date").val() ?? $("#date_start").val();
        var configStartTime = $("#time_start").val();
        var configEndTime = $("#time_end").val();
        var dt = new Date(startDate);

        var html = "";
        if (configDays.length > 0) {
            for (i = 0; i < num;) {
                var weekdayStr = "d" + dt.getDay();
                if (configDays.includes(weekdayStr)) {
                    var month = dt.getMonth() + 1;
                    if (type == "create") {
                        html += "<tr>" +
                            "<td><input type=\"date\" name=\"schedule[" + i +
                            "][date]\" class=\"form-control\" value=\"" + dt.getFullYear() + "-" + (month < 10 ?
                                "0" : "") + month + "-" + (dt.getDate() < 10 ? "0" : "") + dt.getDate() +
                            "\"/></td>" +
                            "<td><input name=\"schedule[" + i +
                            "][time_start]\" class=\"time form-control\" value=\"" + configStartTime +
                            "\"/></td>" +
                            "<td></td></tr>";
                    } else {
                        $("#schedule_" + i + "_date").val(dt.getFullYear() + "-" + (month < 10 ? "0" : "") +
                            month + "-" + (dt.getDate() < 10 ? "0" : "") + dt.getDate());
                        if (configStartTime) {
                            $("#schedule_" + i + "_time_start").val(configStartTime);
                        }
                    }

                    i++;
                }
                dt.setDate(dt.getDate() + 1);
            }
        } else {
            alert("Vui lòng nhập số buổi và chọn ít nhất 1 ngày trong tuần.");
        }

        if (html.length > 0) {
            $("#schedule_result").html(html);
            $('.time').mask('00:00');
        }

    });
</script>
@endsection
