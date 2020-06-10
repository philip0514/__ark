@extends('ark::Web.welcome.app')

@section('header')
{!! $header !!}
@endsection

@section('content')
<section>
    <div class="container-fluid">
        <div class="row h-100vh">
            <div class="col-6 bg-image"></div>
            <div class="col-6 d-flex align-items-center">
                <form method="post" action="/process/register" id="form1" name="form1" class="mx-auto w-80p">
                    <h1>Register</h1>

                    <div class="form-group">
                        <label class="form-control-label required" for="name">
                            Your Name
                        </label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Name" required />
                        <div class="invalid-feedback"></div>
                        <div class="help-feedback">Required</div>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label required" for="username">Email address</label>
                        <input type="email" class="form-control" id="username" name="username" placeholder="Enter email" required>
                        <div class="invalid-feedback"></div>
                        <div class="help-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label required" for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <div class="invalid-feedback"></div>
                        <div class="help-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label" for="password">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Confirm Password">
                        <div class="invalid-feedback"></div>
                        <div class="help-feedback"></div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Submit</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@section('footer')
{!! $footer !!}
@endsection

@section('css')
<style>
    .bg-image{
        background-image: url('https://images.unsplash.com/photo-1587613752723-b7c4a7603f25?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1534&q=80');
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
    }
{!! $css !!}
</style>
@endsection
<? 

                    /*
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
                    */
                    ?>
@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha256-3TKcZElR88BBIA6CeePJAGOsW1yIYf4lP8pI333YuZw=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js" integrity="sha256-+BEKmIvQ6IsL8sHcvidtDrNOdZO3C9LtFPtF2H0dOHI=" crossorigin="anonymous"></script>
<script>
    $(function(){

        $("#form1").validate({
            rules: {
                "name": {
                    "required": true
                },
                "username": {
                    "required": true,
                    "minlength": 4,
                },
                "password": {
                    "required": true
                },
                "password_confirm": {
                    equalTo: "#password"
                }
            },
            messages: {
                "name":{
                    "required": "Required"
                },
                "username":{
                    "required": "Required"
                },
                "password":{
                    "required": "Required"
                },
                "password_confirm":{
                    "equalTo": "Two passwords are not the same."
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
    })
</script>
@endsection