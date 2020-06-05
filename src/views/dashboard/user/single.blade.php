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
                                    <input type="checkbox" class="to-labelauty btn-success" name="checked" value="1" data-plugin="labelauty" data-labelauty="未驗證|已驗證" {{ (isset($rows1['checked']) && $rows1['checked']) ? 'checked' : null }}/>
                                    <input type="checkbox" class="to-labelauty btn-info" name="newsletter" value="1" data-plugin="labelauty" data-labelauty="電子報：未訂閱|電子報：訂閱" {{ (isset($rows1['newsletter']) && $rows1['newsletter']) ? 'checked' : null }}/>
                                    <input type="checkbox" class="to-labelauty btn-danger" name="deleted" value="1" data-plugin="labelauty" data-labelauty="刪除|已刪除" {{ (isset($rows1['deleted_at']) && $rows1['deleted_at']) ? 'checked' : null }}/>
                                </div>

                                <div class="clearfix"></div>

                                <div class="form-group">
                                    <label class="form-control-label required">姓名</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="姓名" required value="{{ isset($rows1['name']) ? $rows1['name'] : null }}" />
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-lg-8">
                                        <label class="form-control-label required">帳號</label>
                                        <input type="text" class="form-control" id="email" name="email" placeholder="帳號" required value="{{ isset($rows1['email']) ? $rows1['email'] : null }}" />
                                        <div class="invalid-feedback"></div>
                                        <div class="help-feedback"></div>
                                    </div>

                                    <div class="form-group col-lg-4">
                                        <label class="form-control-label {{ isset($rows1['id']) ? null : 'required' }}">密碼</label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="密碼" autocomplete="new-password" {{ isset($rows1['id']) ? null : 'required' }} />
                                        <div class="invalid-feedback"></div>
                                        <div class="help-feedback">如不修改，請留白</div>
                                    </div>
                                </div>

                                @if(isset($rows1['facebook_id']))
                                <div class="form-group">
                                    <label class="form-control-label">
                                        Facebook ID
                                    </label>
                                    <div>
                                        {{ $rows1['facebook_id'] }}
                                    </div>
                                </div>
                                @endif

                                @if(isset($rows1['google_id']))
                                <div class="form-group">
                                    <label class="form-control-label">
                                        Google ID
                                    </label>
                                    <div>
                                        {{ $rows1['google_id'] }}
                                    </div>
                                </div>
                                @endif

                                @if(isset($rows1['twitter_id']))
                                <div class="form-group">
                                    <label class="form-control-label">
                                        Twitter ID
                                    </label>
                                    <div>
                                        {{ $rows1['twitter_id'] }}
                                    </div>
                                </div>
                                @endif

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

                                <div class="form-row">
                                    @if(isset($rows1['login_time']))
                                    <div class="form-group col-md-4">
                                        <label class="form-control-label">
                                            最後登入時間
                                        </label>
                                        <div>
                                            {{ $rows1['login_time'] }}
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
                                <h3 class="panel-title">個人資料</h3>
                                <div class="panel-actions">
                                    <a class="panel-action icon wb-minus" aria-expanded="true" data-toggle="panel-collapse" aria-hidden="true"></a>
                                    <a class="panel-action icon wb-expand" data-toggle="panel-fullscreen" aria-hidden="true"></a>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="form-control-label">
                                        性別
                                    </label>
                                    <div class="form-group">
                                        <div class="radio-custom radio-primary float-left">
                                            <input type="radio" id="gender0" name="gender" value="0" checked {{ (!isset($rows1['gender']) or !$rows1['gender']) ? 'checked' : null }} />
                                            <label for="gender0">未設定</label>
                                        </div>
                                        <div class="radio-custom radio-primary float-left m-l-10">
                                            <input type="radio" id="gender1" name="gender" value="1" {{ (isset($rows1['gender']) && $rows1['gender']==1) ? 'checked' : null }} />
                                            <label for="gender1">男性</label>
                                        </div>
                                        <div class="radio-custom radio-primary float-left m-l-10">
                                            <input type="radio" id="gender2" name="gender" value="2" {{ (isset($rows1['gender']) && $rows1['gender']==2) ? 'checked' : null }}>
                                            <label for="gender2">女性</label>
                                        </div>

                                        <div class="clearfix"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">
                                        生日
                                    </label>
                                    <input type="text" class="form-control" id="birthday" name="birthday" placeholder="生日" value="{{ isset($rows1['birthday']) ? $rows1['birthday'] : null }}" />
                                    <div class="invalid-feedback"></div>
                                    <div class="help-feedback"></div>
                                </div>

								<div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label class="form-control-label" for="city_id">城市</label>
                                        <select class="form-control selectpicker" id="city_id" name="city_id" data-style="btn-secondary">
                                            @for($i=0; $i<sizeof($city); $i++)
                                            <option value="{{ $city[$i]['id'] }}" {{ (isset($rows1['city_id']) && $city[$i]['id']==$rows1['city_id']) ? 'selected' : null }} >{{ $city[$i]['name'] }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-control-label" for="area_id">地區</label>
                                        <select class="form-control selectpicker" id="area_id" name="area_id" data-style="btn-secondary">
                                            @for($i=0; $i<sizeof($area); $i++)
                                            <option value="{{ $area[$i]['id'] }}" {{ (isset($rows1['area_id']) && $area[$i]['id']==$rows1['area_id']) ? 'selected' : null }}>{{ $area[$i]['text'] }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label class="form-control-label" for="address">住址</label>
                                        <input type="text" class="form-control" id="address" name="address" placeholder="住址" value="{{ isset($rows1['address']) ? $rows1['address'] : null }}">
                                        <div class="invalid-feedback"></div>
                                        <div class="help-feedback"></div>
                                    </div>
                                </div>

								<div class="row">
                                    <div class="form-group col-lg-6">
                                        <label class="form-control-label">
                                            電話
                                        </label>
                                        <input type="text" class="form-control" id="phone" name="phone" placeholder="電話" value="{{ isset($rows1['phone']) ? $rows1['phone'] : null }}" />
                                        <div class="invalid-feedback"></div>
                                        <div class="help-feedback"></div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <label class="form-control-label">
                                            手機
                                        </label>
                                        <input type="text" class="form-control" id="mobile" name="mobile" placeholder="手機" value="{{ isset($rows1['mobile']) ? $rows1['mobile'] : null }}" />
                                        <div class="invalid-feedback"></div>
                                        <div class="help-feedback"></div>
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
            "name": {
                "required": true
            },
            "email": {
                "required": true,
                "minlength": 4,
                "remote": {
                    url: '{{ $config['path']['validate'] }}',
                    type: "post",
                    data: {
                        type: function(){
                            return 'email';
                        },
                        email: function() {
                            return $("#email").val();
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
                "required": "必填"
            },
            "email":{
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
        },
        submitHandler: function(form){
            Ark.submitHandler(form);
        }
    });

    Ark.datepicker.date('#birthday');
	Ark.zip('#city_id', '#area_id');
    Ark.submit();
});
</script>
@endsection