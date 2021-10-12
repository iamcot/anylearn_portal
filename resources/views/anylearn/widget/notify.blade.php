@if(!empty($notify))
<div class="toast show position-absolute end-0 bg-{{ $notify == "1" ? "success" : "danger" }} text-white" role="alert" aria-live="assertive" aria-atomic="true">
  <!-- <div class="toast-header">
    <span class="rounded me-2"><i class="fa fa-info-circle"></i></span>
    <strong class="me-auto">@lang('Thông báo')</strong>
    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
  </div> -->
  <div class="d-flex">
  <div class="toast-body">
  @if($notify == "0")
        @lang('Thao tác thất bại')
        @elseif($notify == "1")
        @lang('Thao tác thành công')
        @else
        {{ $notify }}
        @endif
  </div>
  <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
  </div>
</div>
@endif