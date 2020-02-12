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
                                    <input type="checkbox" class="to-labelauty btn-danger" name="deleted"" value="1" data-plugin="labelauty" data-labelauty="刪除|已刪除" {{ (isset($rows1['deleted_at']) && $rows1['deleted_at']) ? 'checked' : null }}/>
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label required">姓名</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="姓名" required value="{{ isset($rows1['name']) ? $rows1['name'] : null }}" />
                                    <div class="invalid-feedback"></div>
                                    <div class="help-feedback"></div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-control-label required">帳號</label>
                                    <input type="text" class="form-control" id="account" name="account" placeholder="帳號" required value="{{ isset($rows1['account']) ? $rows1['account'] : null }}" />
                                    <div class="invalid-feedback"></div>
                                    <div class="help-feedback"></div>
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label {{ isset($rows1['id']) ? null : 'required' }}">密碼</label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="密碼" autocomplete="new-password" {{ isset($rows1['id']) ? null : 'required' }}  />
                                    <div class="invalid-feedback"></div>
                                    <div class="help-feedback"></div>
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">
                                        角色
                                    </label>
                                    <select id="role" name="role[]" class="selectpicker" multiple data-style="btn-primary" data-width="100%">
                                        @for ($i=0; $i < sizeof($rows2); $i++)
                                        <option value="{{ $rows2[$i]['name'] }}" {{ isset($rows1['roles']) && in_array($rows2[$i]['name'], $rows1['roles']) ? 'selected' : null }}>{{ $rows2[$i]['name'] }}</option>
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
                                <input id="id" name="id" type="hidden" value="{{ isset($rows1['id']) ? $rows1['id'] : null }}">
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
            "name": {
                "required":true
            },
            "account": {
                "required": true,
                "minlength": 4,
                "remote": {
                    url: '{{ $config['path']['validate'] }}',
                    type: "post",
                    data: {
                        type: function(){
                            return 'account';
                        },
                        account: function() {
                            return $("#account").val();
                        },
                        id: function(){
                            return $('#id').val();
                        }
                    }
                }
            },
            "password": {
                @if (!isset($rows1['id']))
                "required": true,
                @endif
                "minlength": 6
            }
        },
        messages: {
            "name":{
                "required":"必填"
            },
            "account":{
                "required":"必填",
                "remote": "此帳號已存在",
                "minlength": "最少 4 碼，英數組合"
            },
            "password":{
                "required":"必填",
                "minlength": "最少 6 碼，英數組合"
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
});
</script>
@endsection