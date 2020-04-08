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

                                <div class="form-group p-b-10 labelauty-group">
                                    <input type="checkbox" class="to-labelauty btn-primary" name="display" value="1" data-plugin="labelauty" data-labelauty="停用|啟用" {{ (isset($rows1['display']) && $rows1['display']) ? 'checked' : null }}/>
                                    <input type="checkbox" class="to-labelauty btn-danger" name="deleted" value="1" data-plugin="labelauty" data-labelauty="刪除|已刪除" {{ (isset($rows1['deleted_at']) && $rows1['deleted_at']) ? 'checked' : null }}/>
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
                                    <label class="form-control-label required">類型</label>
                                    <div>
                                        <select id="type" name="type" class="selectpicker" data-header="類型" data-live-search="true" data-style="btn-primary" data-width="100%" required>
                                            @for ($i=0; $i<sizeof($type); $i++)
                                            <option value="{{ $type[$i]['id'] }}" {{ isset($rows1['type']) && $type[$i]['id']==$rows1['type'] ? 'selected' : null }}>{{ $type[$i]['name'] }}</option>
                                            @endfor
                                        </select>
                                        <div class="invalid-feedback">必填</div>
                                        <div class="help-feedback"></div>
                                    </div>
                                </div>

								<div class="form-row p-t-10">
									<div class="form-group col-md-6">
										<label class="form-control-label" for="start_time">上架時間</label>
										<input type="text" class="form-control" id="start_time" name="start_time" placeholder="開始時間" value="{{ isset($rows1['start_time']) && $rows1['start_time'] ? date('Y/m/d H:i', $rows1['start_time']) : null }}">
										<div class="invalid-feedback"></div>
										<div class="help-feedback"></div>
									</div>
									<div class="form-group col-md-6">
										<label class="form-control-label" for="end_time">下架時間</label>
										<input type="text" class="form-control" id="end_time" name="end_time" placeholder="結束時間" value="{{ isset($rows1['end_time']) && $rows1['end_time'] ? date('Y/m/d H:i', $rows1['end_time']) : null }}">
										<div class="invalid-feedback"></div>
										<div class="help-feedback">可不填寫，代表無期限</div>
									</div>
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

                                <div class="row">
                                    @if(isset($rows1['deleted_at']))
                                    <div class="form-group col-md-6">
                                        <label class="form-control-label">
                                            刪除時間
                                        </label>
                                        <div>
                                            {{ $rows1['deleted_at'] }}
                                        </div>
                                    </div>
                                    @endif

                                    @if(isset($rows1['deleted_by']))
                                    <div class="form-group col-md-6">
                                        <label class="form-control-label">
                                            刪除者
                                        </label>
                                        <div>
                                            {{ $rows1['deleted_by'] }}
                                        </div>
                                    </div>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="panel panel-bordered">
                            <div class="panel-heading">
                                <h3 class="panel-title">信件</h3>
                                <div class="panel-actions">
                                    <a class="panel-action icon wb-minus" aria-expanded="true" data-toggle="panel-collapse" aria-hidden="true"></a>
                                    <a class="panel-action icon wb-expand" data-toggle="panel-fullscreen" aria-hidden="true"></a>
                                </div>
                            </div>
                            <div class="panel-body">

                                <div class="form-group">
                                    <label class="form-control-label required">
                                        標題
                                    </label>
                                    <input type="text" class="form-control" id="title" name="title" placeholder="標題" required value="{{ isset($rows1['title']) ? $rows1['title'] : null }}" />
                                    <div class="invalid-feedback"></div>
                                    <div class="help-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label">
                                        內容
                                    </label>
                                    <div>
                                        <textarea name="content" id="content">{{ isset($rows1['content']) ? $rows1['content'] : null }}</textarea>
                                    </div>
                                </div>
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
<script src="https://cdn.tiny.cloud/1/{{ config('ark.tinymce.key') }}/tinymce/5/tinymce.min.js"></script>
<script>tinymce.init({selector:'#content', height : "500"});</script>
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

    $('.btn-ogimage-manager').media({
        area: 					'.ogimage-area',
        input_field: 			'.ogimage_input',
        selectable_limit:		3,
        selectable_multiple:	1,
        size:                   'facebook',
    });

    $('.btn-submit').click(function(){
        tinymce.triggerSave();
    });

    Ark.submit();
});
</script>
@endsection