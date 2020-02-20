@extends('ark::dashboard.app')

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

                                <div class="form-group p-b-10">
                                    <input type="checkbox" class="to-labelauty btn-primary" name="display" value="1" data-plugin="labelauty" data-labelauty="停用|啟用" {{ (isset($rows1['display']) && $rows1['display']) ? 'checked' : null }}/>
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label required">
                                        名稱
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="名稱" required value="{{ isset($rows1['name']) ? $rows1['name'] : null }}" />
                                    <div class="invalid-feedback"></div>
                                    <div class="help-feedback"></div>
                                </div>

								<div class="form-group">
									<label for="type" class="form-control-label">頁面類型</label>
									<div>
										<select class="selectpicker" id="type" name="type" data-style="btn-dark" data-width="100%">
											@foreach($type as $index => $value){
											<option data-editable="{{ $value['editable'] }}" value="{{ $value['id'] }}" {{ isset($rows1['type']) && $value['id']==$rows1['type']?'selected':'' }}>{{ $value['name'] }}</option>
											@endforeach
										</select>
									</div>
									<div class="invalid-feedback"></div>
									<div class="help-feedback"></div>
								</div>

								<div class="form-group">
									<label for="url" class="form-control-label required">網址</label>
									<input type="text" class="form-control required" id="url" name="url" placeholder="網址" value="{{ isset($rows1['url']) ? $rows1['url'] : null }}">
									<div class="invalid-feedback"></div>
									<div class="help-feedback">如：/about 或 /page/about，無須填寫網域。</div>
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

                                    @if(isset($rows1['created_by']))
                                    <div class="form-group col-md-6">
                                        <label class="form-control-label">
                                            新增者
                                        </label>
                                        <div>
                                            {{ $rows1['created_by'] }}
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                <div class="row">
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

                                    @if(isset($rows1['updated_by']))
                                    <div class="form-group col-md-6">
                                        <label class="form-control-label">
                                            更新者
                                        </label>
                                        <div>
                                            {{ $rows1['updated_by'] }}
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
                                        標題
                                    </label>
                                    <input type="text" class="form-control" id="title" name="title" placeholder="標題" value="{{ isset($rows1['title']) ? $rows1['title'] : null }}" />
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
                                <div>
                                    <div class="form-group">
                                        <a href="javascript:;" class="btn btn-primary btn-ogimage-manager m-t-10 m-b-10"><i class="fas fa-plus"></i> 選擇社群分享圖片</a>
                                        <div class="alert alert-info">圖片可以拖曳排序，尺寸：寬 * 高 => 「600px * 315px」 ～ 「1200px * 630px」</div>
                                        <div class="row ogimage-area">@if(isset($rows1['ogimage_data'])) @each('ark::media.preview', $rows1['ogimage_data'], 'data') @endif</div>
                                        <input id="ogimage_input" name="ogimage_input" class="ogimage_input" type="hidden" value="{{isset($rows1['ogimage_input']) ? $rows1['ogimage_input'] : ''}}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row type_1">
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
                                <textarea name="content" id="content">{{ isset($rows1['content']) ? $rows1['content'] : null }}</textarea>
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

@section('js')
<script src="https://cdn.tiny.cloud/1/{{config('ark.tinymce.key')}}/tinymce/5/tinymce.min.js"></script>
<script>
var elfinder_url = '/elfinder/tinymce5';
function elFinderBrowser (callback, value, meta) {
    tinymce.activeEditor.windowManager.openUrl({
        title: 'File Manager',
        url: elfinder_url,
        /**
         * On message will be triggered by the child window
         * 
         * @param dialogApi
         * @param details
         * @see https://www.tiny.cloud/docs/ui-components/urldialog/#configurationoptions
         */
        onMessage: function (dialogApi, details) {
            if (details.mceAction === 'fileSelected') {
                const file = details.data.file;

                console.log(file.url);
                
                // Make file info
                const info = file.name;
                
                // Provide file and text for the link dialog
                if (meta.filetype === 'file') {
                    callback(file.url, {text: info, title: info});
                }
                
                // Provide image and alt text for the image dialog
                if (meta.filetype === 'image') {
                    callback(file.url, {alt: info});
                }
                
                // Provide alternative source and posted for the media dialog
                if (meta.filetype === 'media') {
                    callback(file.url);
                }
                
                dialogApi.close();
            }
        }
    });
}

$(function(){
    tinymce.init({
        selector:'#content',
        height: 700,
        //menubar: false,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table paste code help wordcount'
        ],
        toolbar: 'undo redo | formatselect | ' +
        ' bold italic backcolor | alignleft aligncenter ' +
        ' alignright alignjustify | bullist numlist outdent indent |' +
        ' link image media |'+
        ' removeformat | help',
        content_css: [
            '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
            '//www.tiny.cloud/css/codepen.min.css'
        ],
        file_picker_callback : elFinderBrowser,
        language_url : '/tinymce/langs/zh_TW.js',
        language: 'zh_TW'
    });
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

    $('.btn-ogimage-manager').media({
        area: 					'.ogimage-area',
        input_field: 			'.ogimage_input',
        selectable_limit:		3,
        selectable_multiple:	1,
    });

	$('#type').on('changed.bs.select', function(e){
		var value = parseInt($(this).val());
		var editable = $("option[value=" + $(this).val() + "]", this).data('editable');
		
		if(!value){
			$("#url").rules("add", {
				required: true,
				messages: {
					required: "必填",
				}
			});
			$("#url").parent('.form-group').show();
			//$('.type_1').show();
		}else{
			$("#url").rules( "remove", "required" );
			$("#url").parent('.form-group').hide();
			//$('.type_1').hide();
		}
		
		if(editable){
			$('.type_1').show();
		}else{
			$('.type_1').hide();
		}
	});
	
	if(!parseInt($('#type').val())){
		$("#url").rules("add", {
			required: true,
			messages: {
				required: "必填",
			}
		});
		$("#url").parent('.form-group').show();
	}else{
		$("#url").parent('.form-group').hide();
		
		var editable = $('#type').find('option[value='+$('#type').val()+']').data('editable');
		
		if(editable){
			$('.type_1').show();
		}else{
			$('.type_1').hide();
		}
	}

    Ark.submit();
});
</script>
@endsection