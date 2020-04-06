
<div class="modal fade" id="modal-media" tabindex="-1" role="dialog" aria-labelledby="modal-media" aria-hidden="true">
    <div class="modal-dialog modal-fw modal-dialog-top" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    編輯媒體
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form id="form-media-editor" name="form-media-editor" action="{{ route('media.editor') }}" method="POST">
                    <div class="form-group p-b-10">
                        <input type="checkbox" id="custom_crop" name="custom_crop" value="1" data-init-plugin="switchery" data-size="small" data-color="primary" {{ $rows1['custom_crop'] ? 'checked' : '' }} /> 自訂裁切
                    </div>

                    <div class="nav-tabs-vertical cropper" data-plugin="tabs">
                        <ul class="nav nav-tabs mr-25" role="tablist" style="width:120px;">
                            @php
                            $i=0;
                            @endphp
                            @foreach (config('ark.media.dimensions') as $dimension => $value)
                            @php
                            if(!$value['cropper']['aspectRatio']){
                                $value['cropper']['aspectRatio'] = $rows1['image_width']/$rows1['image_height'];
                            }
                            @endphp
                            <li class="nav-item" role="presentation">
                                <a class="nav-link text-center @if($dimension=='large') active show @endif" 
                                    data-toggle="tab" 
                                    href="#Tab{{ ucfirst($dimension) }}" 
                                    aria-controls="Tab{{ ucfirst($dimension) }}" 
                                    role="tab" 
                                    data-custom-crop="{{ $value['custom-crop'] }}"
                                    data-config='@json($value['cropper'], JSON_PRETTY_PRINT)'
                                    data-width='{{ $value['width'] }}'
                                    data-height='{{ $value['height'] }}'
                                    >
                                    <i class="fas fa-3x fa-image"></i><br/>{{ ucfirst($dimension) }}
                                    <input type="hidden" class="cropper_data" name="cropper_data[]" value='@json($rows1['crop_data'][$i], JSON_PRETTY_PRINT)'>
                                </a>
                            </li>
                            @php
                            $i++;
                            @endphp
                            @endforeach
                        </ul>
                        <div class="tab-content py-15">
                            @foreach (config('ark.media.dimensions') as $dimension => $value)
                            <div class="tab-pane @if($dimension=='large') active show @endif" id="Tab{{ ucfirst($dimension) }}" role="tabpanel">
                                <div>
                                    <img class="image" src="{{ $rows1['path'] }}">
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <input type="hidden" id="media_id" name="media_id" value="{{ $rows1['id'] }}">
                    <input type="hidden" id="size" name="size" value="{{ $size }}">
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cons btn-lg btn-default" data-dismiss="modal"><i class="fas fa-times"></i> 取消</button>
                <button type="button" class="btn btn-cons btn-lg btn-primary btn-editor-save"><i class="fas fa-upload"></i> 送出</button>
            </div>
        </div>
    </div>
</div>
<script>
$(function(){
    Cropper.init();

    $('.btn-editor-save').click(function(){
        $('#form-media-editor').ajaxSubmit(function(response){
            var res = response;
            var d = new Date();
            var n = d.getTime();
            $('.media-single[data-value="'+res.id+'"]').find('img').attr('src', res.path+'?t='+n);
            $('#modal-media').modal('hide');
        });
    });
});
</script>