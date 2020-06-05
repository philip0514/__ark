@extends('ark::Dashboard.dashboard.app')

@section('sidebar')
{!! $sidebar !!}
@endsection

@section('content')
<div class="page">
    <form id="form1" name="form1" action="" method="POST">
        <div class="page-header">
            <h1 class="page-title">
                @if (isset($rows1['id']) && isset($rows1['title']))
                    {{ $rows1['title'] }}
                @elseif (isset($rows1['id']) && !isset($rows1['title']))
                    {{ $config['title'] }} - 編輯
                @else
                    {{ $config['title'] }} - 新增
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
                                    <input type="text" class="form-control" id="title" name="title" placeholder="名稱" required value="{{ isset($rows1['title']) ? $rows1['title'] : null }}" />
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

                                <div class="form-group">
                                    <label class="form-control-label">
                                        標籤
                                    </label>
                                    <select class="form-control tag" id="tag" name="tag[]" multiple="multiple">
                                        @for($i=0; $i<sizeof($tag); $i++)
                                            <option value="{{ $tag[$i]['tag_id'] }}" selected>{{ $tag[$i]['text'] }}</option>
                                        @endfor
                                    </select>
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
                                <h3 class="panel-title">圖片</h3>
                                <div class="panel-actions">
                                    <a class="panel-action icon wb-minus" aria-expanded="true" data-toggle="panel-collapse" aria-hidden="true"></a>
                                    <a class="panel-action icon wb-expand" data-toggle="panel-fullscreen" aria-hidden="true"></a>
                                </div>
                            </div>
                            <div class="panel-body">

                                <div class="form-group p-b-10">
                                    <input type="checkbox" class="to-labelauty btn-primary" id="custom_crop" name="custom_crop" value="1" data-plugin="labelauty" data-labelauty="自訂裁切|自訂裁切" {{ (isset($rows1['custom_crop']) && $rows1['custom_crop']) ? 'checked' : null }}/>
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
                                                <img class="image" src="{{ config('ark.media.root') }}{{ config('ark.media.upload') }}/original/{{ $rows1['month'] }}/{{ $rows1['name'] }}">
                                            </div>
                                        </div>
                                        @endforeach
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
        },
        submitHandler: function(form){
            Ark.submitHandler(form);
        }
    });

    Ark.tag('.tag', true, false);
    Ark.submit();
    Cropper.init();
});
</script>
@endsection