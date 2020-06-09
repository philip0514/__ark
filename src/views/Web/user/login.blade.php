@extends('ark::Web.welcome.app')

@section('header')
{!! $header !!}
@endsection

@section('content')
{{-- $html --}}
<section>
    <div class="container-fluid">
        <div class="row h-100vh">
            <div class="col-6 bg-image"></div>
            <div class="col-6 d-flex align-items-center">
                <form method="post" action="" id="form1" name="form1" class="mx-auto w-p-80">
                    <h1>Login</h1>
                    <div class="form-group">
                        <label for="username">Email address</label>
                        <input type="email" class="form-control" id="username" name="username" aria-describedby="emailHelp" placeholder="Enter email">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
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