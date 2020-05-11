@inject('fileService', 'App\Services\FileServices')
@extends('layout')

@section('body')
<div class="row">
    <div class="col-md-4">
        <form action="{{ route('config.banner') }}" method="POST" role="form" id="formnews" enctype="multipart/form-data">
            <div class="card shadow">
                <div class="card-body">
                    @csrf
                    <input name="file" type="file" id="imgInp" multiple />
                    <div id="newsImages" style="max-width:300px"></div>
                </div>
                <div class="card-footer">
                <p class="small"><i class="fas fa-info-circle"></i> Banner sẽ sắp xếp theo tên file. File trùng tên sẽ không upload được. Các banner nên có chung 1 size để đảm bảo thẩm mĩ.</p>
                    <button type="button" class="btn  btn-sm btn-{{ env('MAIN_COLOR', 'primary') }}" id="saveButton" onclick="submit()">
                        <i class="fas fa-cloud-upload-alt"></i> Upload</button>

                </div>
            </div>
        </form>
    </div>
    <div class="col-md-8">
        @foreach($files as $file)
        <div class="card mb-2 shadow">
            <div class="card-body p-0">
                <img class="img-fluid" src="{{ Storage::disk('banners')->url($file) }}">
            </div>
            <div class="card-footer">
                <a href="{{ route('config.banner.del', ['img' => $fileService->encodeFileName($file) ]) }}" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash-alt"></i> Xóa banner {{ $file }}</a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection