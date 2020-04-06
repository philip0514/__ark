@extends('ark::dashboard.app')

@section('sidebar')
{!! $sidebar !!}
@endsection

@section('content')
<div class="page">
    <form id="form1" name="form1" action="" method="POST">
        <div class="page-header">
            <h1 class="page-title">
                {{ $config['name'] }}
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

                                <div class="form-group">
                                    <label class="form-control-label required">名稱</label>
                                    <input type="text" class="form-control" id="title" name="title" placeholder="名稱" required value="{{ isset($rows1['title']) ? $rows1['title'] : null }}" />
                                    <div class="invalid-feedback"></div>
                                    <div class="help-feedback"></div>
                                </div>

								<div class="form-group">
									<label class="form-control-label" for="tag">關鍵字</label>
                                    <select class="form-control tag" id="tag" name="tag[]" multiple="multiple">
										@for($i=0; $i<sizeof($tag); $i++)
										<option value="{{ $tag[$i]['tag_id'] }}" selected="selected">{{ $tag[$i]['text'] }}</option>
										@endfor
									</select>
									<div class="invalid-feedback"></div>
									<div class="help-feedback"></div>
								</div>

                                <div class="form-group">
                                    <label class="form-control-label">
                                        敘述
                                    </label>
                                    <input type="text" class="form-control" id="description" name="description" placeholder="敘述" value="{{ isset($rows1['description']) ? $rows1['description'] : null }}" />
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
                                    <a href="javascript:;" class="btn btn-primary btn-ogimage-manager m-t-10 m-b-10"><i class="fas fa-plus"></i> 選擇社群分享圖片</a>
                                    <div class="alert alert-info">圖片可以拖曳排序，尺寸：寬 * 高 => 「600px * 315px」 ～ 「1200px * 630px」</div>
                                    <div class="row ogimage-area">@if(isset($rows1['ogimage_data'])) @each('ark::media.preview', $rows1['ogimage_data'], 'data') @endif</div>
                                    <input id="ogimage_input" name="ogimage_input" class="ogimage_input" type="hidden" value="{{ isset($rows1['ogimage_input']) ? $rows1['ogimage_input'] : '' }}" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-12">
                        <div class="panel panel-bordered">
                            <div class="panel-heading">
                                <h3 class="panel-title">Passport Token</h3>
                                <div class="panel-actions">
                                    <a class="panel-action icon wb-minus" aria-expanded="true" data-toggle="panel-collapse" aria-hidden="true"></a>
                                    <a class="panel-action icon wb-expand" data-toggle="panel-fullscreen" aria-hidden="true"></a>
                                </div>
                            </div>
                            <div class="panel-body">
                                <table class="table table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>id</th>
                                            <th>name</th>
                                            <th>secret</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for($i=0; $i<sizeof($rows1['client']); $i++)
                                        <tr>
                                            <td>{{ $rows1['client'][$i]['id'] }}</td>
                                            <td>{{ $rows1['client'][$i]['name'] }}</td>
                                            <td>{{ $rows1['client'][$i]['secret'] }}</td>
                                        </tr>
                                        @endfor
                                    </tbody>
                                </table>
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
                                <button class="btn btn-lg btn-cons btn-primary">
                                    <i class="fas fa-save pr-5"></i> 儲存
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
$(function(){
    $("#form1").validate({
        rules: {
            "title": {
                "required": true
            }
        },
        messages: {
            "title":{
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
        }
    });

    $('.btn-ogimage-manager').media({
        area: 					'.ogimage-area',
        input_field: 			'.ogimage_input',
        selectable_limit:		3,
        selectable_multiple:	1,
        size:					'facebook',
    });

    Ark.tag('.tag', true, false);
});
</script>
@endsection