<div class="modal fade" id="modal-url" tabindex="-1" role="dialog" aria-labelledby="modal-url" aria-hidden="true">
    <div class="modal-dialog modal-fw modal-dialog-top" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    網址
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
                
                <div class="panel-group panel-group-continuous" id="urlAccordion" aria-multiselectable="true" role="tablist">
                    <div class="panel">
                        <div class="panel-heading" id="urlHeading-1" role="tab">
                            <a class="panel-title" data-parent="#urlAccordion" data-toggle="collapse" href="#urlDefault" aria-controls="urlDefault" aria-expanded="true">
                                <b>預設網址</b>
                            </a>
                        </div>
                        <div class="panel-collapse collapse show" id="urlDefault" aria-labelledby="urlHeading-1" role="tabpanel" style="">
                            <div class="panel-body">
                                <div class="row">

                                    @for($i=0; $i<sizeof($default); $i++)
                                    <div class="col-xxl-1 col-xl-2 col-lg-2 col-md-3 col-sm-6 col-xs-6 col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="url"" id="url-{{ $default[$i]['id'] }}" value="{{ $default[$i]['value'] }}">
                                            <label class="form-check-label" for="url-{{ $default[$i]['id'] }}">
                                                {{ $default[$i]['name'] }}
                                            </label>
                                        </div>
                                    </div>
                                    @endfor

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading" id="urlHeading-1" role="tab">
                            <a class="panel-title" data-parent="#urlAccordion" data-toggle="collapse" href="#urlNews" aria-controls="urlNews" aria-expanded="true">
                                <b>最新消息</b>
                            </a>
                        </div>
                        <div class="panel-collapse collapse show" id="urlNews" aria-labelledby="urlHeading-1" role="tabpanel" style="">
                            <div class="panel-body">
                                <div class="row">

                                    @for($i=0; $i<sizeof($news); $i++)
                                    <div class="col-xxl-1 col-xl-2 col-lg-2 col-md-3 col-sm-6 col-xs-6 col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="url"" id="url-{{ $news[$i]['id'] }}" value="{{ $news[$i]['value'] }}">
                                            <label class="form-check-label" for="url-{{ $news[$i]['id'] }}">
                                                {{ $news[$i]['name'] }}
                                            </label>
                                        </div>
                                    </div>
                                    @endfor

                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cons btn-lg btn-default" data-dismiss="modal"><i class="fas fa-times"></i> 取消</button>
                <button type="button" class="btn btn-cons btn-lg btn-primary btn-url-save"><i class="fas fa-upload"></i> 送出</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('.btn-url-save').unbind('click');
        $('.btn-url-save').click(function(){
            var $value = $('input[name=url]:checked').val();
            PageBuilder.setUrl($value);
            $('#modal-url').modal('hide');
        });
    })
</script>