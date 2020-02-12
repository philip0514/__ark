@extends('ark::dashboard.app')

@section('sidebar')
{!! $sidebar !!}
@endsection

@section('content')
<div class="page">
    <form id="form1" name="form1" action="" method="POST">
        <div class="page-header">
            <h1 class="page-title">
                個人資料
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
                                    <label class="form-control-label">密碼 </label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="密碼" autocomplete="new-password" />
                                    <div class="invalid-feedback"></div>
                                    <div class="help-feedback"></div>
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