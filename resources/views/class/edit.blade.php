@inject('userServ','App\Services\UserServices')

@extends('layout')

@section('body')
@if(!$userServ->isSale() || @auth()->user()->role == App\Constants\UserConstants::ROLE_SALE_CONTENT)
<form action="" method="post" id="courseEditForm" enctype="multipart/form-data">
    @csrf
@endif
    <input type="hidden" name="id" value="{{ !empty($courseId) ? $courseId : 0 }}">
    <input type="hidden" name="action" value="{{ empty($courseId) ? 'create' : 'update' }}">
    <div class="row nav-pills-custom">
        <div class="col-2">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <a class="shadow nav-link {{ session('tab', 'info') == 'info' ? 'active' : '' }}" id="v-pills-info-tab" data-toggle="pill" href="#v-pills-info" role="tab" aria-controls="v-pills-info" aria-selected="true"><i class="fa fa-info-circle"></i> <span class="mobile-no-text">@lang('Thông tin')</span></a>
                <a class="shadow nav-link {{ session('tab') == 'schedule' ? 'active' : '' }} {{ empty($courseId) ? 'disabled' : '' }}" id="v-pills-schedule-tab" data-toggle="pill" href="#v-pills-schedule" role="tab" aria-controls="v-pills-schedule" aria-selected="false" aria-disabled="{{ empty($courseId) ? 'true' : 'false' }}"><i class="fa fa-calendar-alt"></i> <span class="mobile-no-text">@lang('Lịch học')</span></a>
                <a class="shadow nav-link {{ session('tab') == 'resource' ? 'active' : '' }} {{ empty($courseId) ? 'disabled' : '' }}" id="v-pills-resource-tab" data-toggle="pill" href="#v-pills-resource" role="tab" aria-controls="v-pills-resource" aria-selected="false" aria-disabled="{{ empty($courseId) ? 'true' : 'false' }}"><i class="fa fa-image"></i> <span class="mobile-no-text">@lang('Hình ảnh')</span></a>
                @if($isSchool)
                <a class="shadow nav-link {{ session('tab') == 'teachers' ? 'active' : '' }} {{ empty($courseId) ? 'disabled' : '' }}" id="v-pills-teachers-tab" data-toggle="pill" href="#v-pills-teachers" role="tab" aria-controls="v-pills-teachers" aria-selected="false" aria-disabled="{{ empty($courseId) ? 'true' : 'false' }}"><i class="fa fa-chalkboard-teacher"></i> <span class="mobile-no-text">@lang('Giảng viên')</span></a>
                @endif
                <a class="shadow nav-link {{ session('tab') == 'seo' ? 'active' : '' }}" id="v-pills-seo-tab" data-toggle="pill" href="#v-pills-seo" role="tab" aria-controls="v-pills-seo" aria-selected="false"><i class="fa fa-link"></i> <span class="mobile-no-text">@lang('SEO')</span></a>
                <a class="shadow nav-link {{ session('tab') == 'rating' ? 'active' : '' }}" id="v-pills-rating-tab" data-toggle="pill" href="#v-pills-rating" role="tab" aria-controls="v-pills-rating" aria-selected="false"><i class="fa fa-star"></i> <span class="mobile-no-text">@lang('Đánh giá')</span></a>
                <a class="shadow nav-link {{ session('tab') == 'registered' ? 'active' : '' }}" id="v-pills-registered-tab" data-toggle="pill" href="#v-pills-registered" role="tab" aria-controls="v-pills-registered" aria-selected="false"><i class="fa fa-star"></i> <span class="mobile-no-text">@lang('Học viên')</span></a>
            </div>
        </div>
        <div class="col-10 pl-0 mb-3">
            <div class="tab-content" id="v-pills-tabContent">
                <div class="tab-pane fade {{ session('tab', 'info') == 'info' ? 'show active' : '' }}" id="v-pills-info" role="tabpanel" aria-labelledby="v-pills-info-tab">
                    @include('class.form.info')
                </div>
                <div class="tab-pane fade {{ session('tab') == 'seo' ? 'show active' : '' }}" id="v-pills-seo" role="tabpanel" aria-labelledby="v-pills-seo-tab">
                    @include('class.form.seo')
                </div>
                <div class="tab-pane fade {{ session('tab') == 'schedule' ? 'show active' : '' }}" id="v-pills-schedule" role="tabpanel" aria-labelledby="v-pills-schedule-tab">
                    @include('class.form.schedule')
                </div>
                <div class="tab-pane fade {{ session('tab') == 'resource' ? 'show active' : '' }}" id="v-pills-resource" role="tabpanel" aria-labelledby="v-pills-resource-tab">
                    @include('class.form.resource')
                </div>
                @if($isSchool)
                <div class="tab-pane fade {{ session('tab') == 'teachers' ? 'show active' : '' }}" id="v-pills-teachers" role="tabpanel" aria-labelledby="v-pills-teachers-tab">
                    @include('class.form.teachers')
                </div>
                @endif
                <div class="tab-pane fade {{ session('tab') == 'rating' ? 'show active' : '' }}" id="v-pills-rating" role="tabpanel" aria-labelledby="v-pills-rating-tab">
                    @include('class.form.rating')
                </div>
                <div class="tab-pane fade {{ session('tab') == 'registered' ? 'show active' : '' }}" id="v-pills-registered" role="tabpanel" aria-labelledby="v-pills-registered-tab">
                    @include('class.form.registered')
                </div>
            </div>
        </div>
    </div>
@if(!$userServ->isSale())
</form>
@endif
@endsection
@section('jscript')
<script src="/cdn/vendor/ckeditor/ckeditor.js"></script>
<script src="/cdn/vendor/jquery/jquery.mask.js"></script>
<script src="/cdn/vendor/jquery/speakingurl.min.js"></script>
<script>
    CKEDITOR.replace('editorvi');
    CKEDITOR.replace('editoren');
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
                            "<td><input type=\"date\" name=\"schedule[" + i + "][date]\" class=\"form-control\" value=\"" + dt.getFullYear() + "-" + (month < 10 ? "0" : "") + month + "-" + (dt.getDate() < 10 ? "0" : "") + dt.getDate() + "\"/></td>" +
                            "<td><input name=\"schedule[" + i + "][time_start]\" class=\"time form-control\" value=\"" + configStartTime + "\"/></td>" +
                            "<td></td></tr>";
                    } else {
                        $("#schedule_" + i + "_date").val(dt.getFullYear() + "-" + (month < 10 ? "0" : "") + month + "-" + (dt.getDate() < 10 ? "0" : "") + dt.getDate());
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