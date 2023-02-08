<div class="mb-3 mb-lg-0 card">
    <h5 class="bg-light card-header">Quản lý Nội Dung Mail</h5>
    <form action="" method="get">
        <div class="card-body">
            <input type="hidden" name="tab" value="templatemail">
            <div class="mb-3">
                <textarea rows="13" placeholder="Nội dung mail" name="mailcontent" id="mailcontent" class="form-control">{{!empty($course['info']->mailcontent) ? $course['info']->mailcontent : null}}</textarea>
            </div>
            <div class="text-end">
                <button class="btn btn-success" name="action" value="mailsave" type="submit">Lưu</button>
            </div>
        </div>
    </form>
</div>
@section('jscript')
    @parent
    <script src="/cdn/vendor/ckeditor/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('editormail');
    </script>
@endsection
