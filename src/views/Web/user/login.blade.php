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
                <form method="post" id="form_login" name="form_login" class="mx-auto w-80p" action="{{ route('login_process') }}">
                    <h1>Login</h1>
                    <div class="form-group required">
                        <label for="username">Email address</label>
                        <input type="email" class="form-control" id="username" name="username" placeholder="Enter email" required>
                        <div class="invalid-feedback"></div>
                        <div class="help-feedback">Required</div>
                    </div>
                    <div class="form-group required">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <div class="invalid-feedback"></div>
                        <div class="help-feedback">Required</div>
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
    .w-p-80{
        width: 80%;
    }
    .h-100vh{
        height: 100vh;
    }
    .bg-image{
        background-image: url('https://images.unsplash.com/photo-1587613752723-b7c4a7603f25?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1534&q=80');
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
    }
{!! $css !!}
</style>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha256-3TKcZElR88BBIA6CeePJAGOsW1yIYf4lP8pI333YuZw=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js" integrity="sha256-+BEKmIvQ6IsL8sHcvidtDrNOdZO3C9LtFPtF2H0dOHI=" crossorigin="anonymous"></script>
<script>
    $(function(){

        $("#form_login").validate({
            rules: {
                "username": {
                    "required": true
                },
                "password": {
                    "required": true
                }
            },
            messages: {
                "username":{
                    "required": "This field is required."
                },
                "password":{
                    "required": "This field is required."
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
    })
</script>
@endsection