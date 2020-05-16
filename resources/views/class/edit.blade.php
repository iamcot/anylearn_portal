@extends('layout')

@section('rightFixedTop')
<a class="btn btn-sm btn-success" href="{{ route('class.create') }}"><i class="fas fa-plus"></i> @lang('Thêm mới')</a>
@endsection

@section('body')
<form action="" method="post" id="courseEditForm" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" value="{{ !empty($courseId) ? $courseId : 0 }}">
    <input type="hidden" name="action" value="{{ empty($courseId) ? 'create' : 'update' }}">
    <div class="row nav-pills-custom">
        <div class="col-2">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <a class="shadow nav-link {{ session('tab', 'info') == 'info' ? 'active' : '' }}" id="v-pills-info-tab" data-toggle="pill" href="#v-pills-info" role="tab" aria-controls="v-pills-info" aria-selected="true">@lang('Thông tin')</a>
                <a class="shadow nav-link {{ session('tab') == 'seo' ? 'active' : '' }}" id="v-pills-seo-tab" data-toggle="pill" href="#v-pills-seo" role="tab" aria-controls="v-pills-seo" aria-selected="false">@lang('SEO')</a>
                <a class="shadow nav-link {{ session('tab') == 'schedule' ? 'active' : '' }} {{ empty($courseId) ? 'disabled' : '' }}" id="v-pills-schedule-tab" data-toggle="pill" href="#v-pills-schedule" role="tab" aria-controls="v-pills-schedule" aria-selected="false" aria-disabled="{{ empty($courseId) ? 'true' : 'false' }}">@lang('Lịch học')</a>
                <a class="shadow nav-link {{ session('tab') == 'resource' ? 'active' : '' }} {{ empty($courseId) ? 'disabled' : '' }}" id="v-pills-resource-tab" data-toggle="pill" href="#v-pills-resource" role="tab" aria-controls="v-pills-resource" aria-selected="false" aria-disabled="{{ empty($courseId) ? 'true' : 'false' }}">@lang('Hình ảnh, Tài liệu')</a>
            </div>
        </div>
        <div class="col-10 pl-0 mb-3">
            <div class="tab-content shadow" id="v-pills-tabContent">
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
            </div>
        </div>
    </div>

</form>
@endsection
@section('jscript')
<script src="/cdn/vendor/ckeditor/ckeditor.js"></script>
<script src="/cdn/vendor/jquery/jquery.mask.js"></script>
<script src="/cdn/vendor/jquery/speakingurl.min.js"></script>
<script>
    CKEDITOR.replace('editor');
    $('.time').mask('00:00');

    function formatSlug(field, id) {
        $("#" + id).val(getSlug(field.value));
    }

    $('#courseSubmit').click(function() {
        $("#courseEditForm").submit();
    });

    $("#schedule_generate").click(function() {
        var num = $("#schedule_auto").find('#num').val();
        var configDays = $("#schedule_auto input:checkbox:checked").map(function() {
            return $(this).attr('name');
        }).get();
        var startDate = $("#date_start").val();
        var configStartTime = $("#time_start").val();
        var configEndTime = $("#time_end").val();
        var dt = new Date(startDate);
        var html = "";
        for (i = 0; i < num;) {
            var weekdayStr = "d" + dt.getDay();
            if (configDays.includes(weekdayStr)) {
                i++;
                html += "<div class=\"row mb-1\">" +
                    "<div class=\"col-6\"><input class=\"form-control\" value=\"" + dt.getFullYear() + "-" + dt.getMonth() + "-" + dt.getDate() + "\"/></div>" +
                    "<div class=\"col-3\"><input class=\"form-control\" value=\"" + configStartTime + "\"/></div>" +
                    "<div class=\"col-3\"><input class=\"form-control\" value=\"" + configEndTime + "\"/></div>" +
                    "</div>";
            }
            dt.setDate(dt.getDate() + 1);
        }
        $("#schedule_result").html(html);
    });
</script>
@endsection