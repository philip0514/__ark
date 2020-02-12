<!DOCTYPE html>
<html class="no-js css-menubar" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <meta name="robots" content="noindex">
        <meta name="googlebot" content="noindex">
        <meta name="description" content="bootstrap admin template">
        <meta name="author" content="Philip Chuang">

        <title>登入 | {{ config('ark.title') }} 管理系統</title>

        <!--
        <link rel="apple-touch-icon" href="../../assets/images/apple-touch-icon.png">
        <link rel="shortcut icon" href="../../assets/images/favicon.ico">
        -->

        <!-- Stylesheets -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.3.0/css/flag-icon.min.css">
        <link rel="stylesheet" href="{{ '/ark/css/login.css' }}">


        <!-- Fonts -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.css">
        <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic'>

        <!--[if lt IE 9]>
        <script src="../../../global/vendor/html5shiv/html5shiv.min.js"></script>
        <![endif]-->

        <!--[if lt IE 10]>
        <script src="../../../global/vendor/media-match/media.match.min.js"></script>
        <script src="../../../global/vendor/respond/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="animsition page-login layout-full page-dark">
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->


        <!-- Page -->
        <div class="page vertical-align text-center" data-animsition-in="fade-in" data-animsition-out="fade-out">
            <div class="page-content vertical-align-middle animation-slide-top animation-duration-1">
                <div class="brand">
                    <h1 class="text-white">{{ config('ark.title') }}</h1>
                    <h4 class="brand-text">管理系統</h4>
                </div>
                <form id="form1" name="form1" method="post" action="">
                    <div class="form-group">
                        <label class="sr-only" for="account">{{ config('ark.text.account') }}</label>
                        <input type="text" class="form-control form-control-lg" id="account" name="account" placeholder="{{ config('ark.text.account') }}">
                    </div>
                    <div class="form-group">
                        <label class="sr-only" for="password">{{ config('ark.text.password') }}</label>
                        <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="{{ config('ark.text.password') }}">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-sign-in-alt fa-2x"></i></button>

                    @if (session()->has('status'))
                    <div class="row mt-5">
                        <div class="control-group col-md-12">
                            <div class="alert alert-danger text-center">
                                {{ config('ark.error.login') }}
                            </div>
                        </div>
                    </div>
                    @endif
                </form>
            </div>
        </div>
        <!-- End Page -->


        <!-- Core  -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
        <script>
            $(function(){
                $('#account').focus();
            });
        </script>
    </body>
</html>
