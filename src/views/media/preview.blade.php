<div class="col-xxl-3 col-xl-4 col-md-4 col-sm-4 col-xs-6 col-6 p-t-10 p-b-10 media-single" data-value="{{ $data['id'] }}">
    <figure class="m-b-0">
        <img class="img-fluid rounded-top" alt="{{ $data['title'] }}" src="{{ $data['path'] }}" />
    </figure>
    <div class="btn-group btn-group-justified" role="group">
        <button type="button" class="btn btn-primary btn-media-editor">
            <i class="fa fa-pencil"></i>
            <span>編輯</span>
        </button>
        <button type="button" class="btn btn-danger btn-media-delete">
            <i class="fa fa-remove"></i>
            <span>刪除</span>
        </button>
    </div>
</div>