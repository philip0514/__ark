@extends('ark::Dashboard.dashboard.app')

@section('sidebar')
{!! $sidebar !!}
@endsection

@section('content')
<div class="page">
    <form id="form1" name="form1" action="" method="POST">
        <div class="page-header">
            <h1 class="page-title">
                @if (isset($rows1['id']) && isset($rows1['name']))
                    {{ $rows1['name'] }}
                @elseif (isset($rows1['id']) && !isset($rows1['name']))
                    {{ $config['name'] }} - 編輯
                @else
                    {{ $config['name'] }} - 新增
                @endif
            </h1>
            <p class="page-description"></p>
        </div>
        <div class="page-content">

            <div class="container-fluid px-0">
                <div class="row">
                    <div class="col-xl-6 col-12">
                        <div class="panel panel-bordered">
                            <div class="panel-heading">
                                <h3 class="panel-title">一般</h3>
                                <div class="panel-actions">
                                    <a class="panel-action icon wb-minus" aria-expanded="true" data-toggle="panel-collapse" aria-hidden="true"></a>
                                    <a class="panel-action icon wb-expand" data-toggle="panel-fullscreen" aria-hidden="true"></a>
                                </div>
                            </div>
                            <div class="panel-body">

                                <div class="form-group p-b-10 labelauty-group">
                                    <input type="checkbox" class="to-labelauty btn-primary" name="display" value="1" data-plugin="labelauty" data-labelauty="停用|啟用" {{ (isset($rows1['display']) && $rows1['display']) ? 'checked' : null }}/>
                                    <input type="checkbox" class="to-labelauty btn-danger" name="deleted" value="1" data-plugin="labelauty" data-labelauty="刪除|已刪除" {{ (isset($rows1['deleted_at']) && $rows1['deleted_at']) ? 'checked' : null }}/>
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label required">名稱</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="名稱" required value="{{ isset($rows1['name']) ? $rows1['name'] : null }}" />
                                    <div class="invalid-feedback"></div>
                                    <div class="help-feedback"></div>
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label required">日期</label>
                                    <input type="text" class="form-control" id="news_time" name="news_time" placeholder="日期" required value="{{ isset($rows1['news_time']) ? date('Y-m-d', $rows1['news_time']) : null }}" />
                                    <div class="invalid-feedback"></div>
                                    <div class="help-feedback"></div>
                                </div>

                                <div class="row">
                                    @if(isset($rows1['created_at']))
                                    <div class="form-group col-md-6">
                                        <label class="form-control-label">
                                            新增時間
                                        </label>
                                        <div>
                                            {{ $rows1['created_at'] }}
                                        </div>
                                    </div>
                                    @endif
                
                                    @if(isset($rows1['updated_at']))
                                    <div class="form-group col-md-6">
                                        <label class="form-control-label">
                                            更新時間
                                        </label>
                                        <div>
                                            {{ $rows1['updated_at'] }}
                                        </div>
                                    </div>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-12">
                        <div class="panel panel-bordered">
                            <div class="panel-heading">
                                <h3 class="panel-title">設定</h3>
                                <div class="panel-actions">
                                    <a class="panel-action icon wb-minus" aria-expanded="true" data-toggle="panel-collapse" aria-hidden="true"></a>
                                    <a class="panel-action icon wb-expand" data-toggle="panel-fullscreen" aria-hidden="true"></a>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="form-control-label">
                                        敘述
                                    </label>
                                    <input type="text" class="form-control" id="description" name="description" placeholder="敘述" value="{{ isset($rows1['description']) ? $rows1['description'] : null }}" />
                                    <div class="invalid-feedback"></div>
                                    <div class="help-feedback"></div>
                                </div>
                                <div>
                                    <div class="form-group">
                                        <a href="javascript:;" class="btn btn-primary btn-ogimage-manager m-t-10 m-b-10"><i class="fas fa-plus"></i> 選擇社群分享圖片</a>
                                        <div class="alert alert-info">圖片可以拖曳排序，尺寸：寬 * 高 => 「600px * 315px」 ～ 「1200px * 630px」</div>
                                        <div class="row ogimage-area">@if(isset($rows1['ogimage_data'])) @each('ark::Dashboard.media.preview', $rows1['ogimage_data'], 'data') @endif</div>
                                        <input id="ogimage_input" name="ogimage_input" class="ogimage_input" type="hidden" value="{{ isset($rows1['ogimage_input']) ? $rows1['ogimage_input'] : '' }}" />
                                    </div>

                                    <div class="form-group">
                                        <a href="javascript:;" class="btn btn-primary btn-media-manager m-t-10 m-b-10"><i class="fa fa-plus"></i> 選擇新聞圖片</a>
                                        <div class="alert alert-info">圖片可以拖曳排序</div>
                                        <div class="row media-area">@if(isset($rows1['media_data'])) @each('ark::Dashboard.media.preview', $rows1['media_data'], 'data') @endif</div>
                                        <input id="media_input" name="media_input" class="media_input" type="hidden" value="{{isset($rows1['media_input']) ? $rows1['media_input'] : null}}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="panel panel-bordered">
                            <div class="panel-heading">
                                <h3 class="panel-title">內容</h3>
                                <div class="panel-actions">
                                    <a class="panel-action icon wb-minus" aria-expanded="true" data-toggle="panel-collapse" aria-hidden="true"></a>
                                    <a class="panel-action icon wb-expand" data-toggle="panel-fullscreen" aria-hidden="true"></a>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div id="editor"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="page-footer page-submit">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="fixed-bottom fixed-bottom-button">
                            <div class="text-right">
                                <div class="btn-group" role="group">
                                    <button type="submit" class="btn btn-lg btn-dark btn-submit" data-method="1">
                                        <i class="fas fa-save pr-5"></i> 儲存後繼續
                                    </button>
                                    <button type="submit" class="btn btn-lg btn-primary btn-submit" data-method="0">
                                        <i class="fas fa-save pr-5"></i> 儲存
                                    </button>
                                </div>
                                <input id="id" name="id" type="hidden" value="{{ isset($rows1['id']) ? $rows1['id'] : null }}">
                                <input type="hidden" id="__method" name="__method" value="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css" integrity="sha256-b5ZKCi55IX+24Jqn638cP/q3Nb2nlx+MH/vMMqrId6k=" crossorigin="anonymous" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/grapesjs/0.16.18/css/grapes.min.css">
<link rel="stylesheet" href="/ark/pagebuilder/css/pagebuilder.css?t=<?=time()?>">
<link rel="stylesheet" href="/ark/pagebuilder/css/tooltip.css?t=<?=time()?>">
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js" integrity="sha256-4iQZ6BVL4qNKlQ27TExEhBN1HFPvAvAMbFavKKosSWQ=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js" integrity="sha256-5YmaxAwMjIpMrVlK84Y/+NjCpKnFYa8bWWBbUHSBGfU=" crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/grapesjs/0.16.18/grapes.js"></script>
<script src="/ark/pagebuilder/js/pagebuilder.js?t=<?=time()?>"></script>
<script src="/theme/bootstrap4/js/grapes.js?t=<?=time()?>"></script>

<script>
$(function(){
    $("#form1").validate({
        rules: {
            "name": {
                "required": true
            }
        },
        messages: {
            "name":{
                "required": "必填"
            }
        },
        onblur: true,
        onkeyup: false,
        onsubmit: true,
        highlight: function(element, errorClass, validClass){
            form_highlight(element, errorClass, validClass);
        },
        unhighlight: function(element, errorClass, validClass){
            form_unhighlight(element, errorClass, validClass);
        },
        errorElement: "div",
        errorPlacement: function($error, $element){
            form_error_text($error, $element);
        },
        submitHandler: function(form){
            Ark.submitHandler(form);
        }
    });

    Ark.datepicker.date('#news_time');
    Ark.ogimage();
    Ark.media();

    PageBuilder.gjs({
        plugins: [
            'bootstrap4',
        ],
        canvas: {
            styles: [
                'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css',
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css',
                '/theme/bootstrap4/css/main.css',
                '/theme/bootstrap4/css/grapesjs.css',
            ],
            scripts: [
                'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js',
                'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/js/bootstrap.bundle.min.js',
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js'
            ],
        },
    });

    PageBuilder.load({!! $rows1['json'] ? $rows1['json'] : '{}' !!}, "{!! $rows1['css'] !!}");

    $('.btn-submit').click(function(){
        PageBuilder.save({
            'html': '#htmlContent',
            'css': '#cssContent',
            'json': '#jsonContent',
        });
    });

    Ark.submit();
});
</script>
@endsection