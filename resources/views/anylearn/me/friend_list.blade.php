<div class="card shadow">
    <div class="card-body">
        <div class="">
            <h6><b>Danh Sách Bạn Bè</b></h6>
            <div class="form-group row">
                <!-- <p class="p-2">Bạn chưa có bạn bè</p> -->
                @foreach($userselect as $userselect)
                @if($userselect->user_id == auth()->user()->id)
                <div>
                    @if($userselect->image !=null)
                    <img class="avatar avatar-sm avatar-img p-2" src="{{ $userselect->image }}" alt="">
                    @else
                    <img class="avatar avatar-sm avatar-img bg-black border rounded-circle mt-1" src="http://anylearn.vn/cdn/anylearn/img/logo-color.svg" alt="">
                    @endif
                    <span class="mt-3 ml-2">{{ $userselect->name }}</span>
                    <!-- <input id="name" type="text" class="form-control" name="name" value="{{ $userselect->name }}" readonly> -->
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>
</div>