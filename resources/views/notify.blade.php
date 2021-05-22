@if(!empty($notify))
<div style="min-width:300px; position: absolute; right: 15px;z-index:999; top: {{ $top ?? '80px' }};">
    <div class="toast shadow" role="alert" aria-live="assertive" data-delay="2500" data-animation="true" aria-atomic="true">
        <div class="toast-header bg-{{ env('MAIN_COLOR', 'primary') }} text-white">
            <strong class="mr-auto"><i class="fas fa-info-circle"></i> @lang('Thông báo')</strong>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
            @if($notify == "0")
                @lang('Thao tác thất bại')
            @elseif($notify == "1")
                @lang('Thao tác thành công')
            @else
                {{ $notify }}
            @endif
        </div>
    </div>
</div>
@endif