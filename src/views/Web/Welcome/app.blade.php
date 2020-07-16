<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $html['meta']['title'] }}</title>
    <meta name="description" content="{{ $html['meta']['description'] }}">
    <meta name="keywords" content="{{ $html['meta']['keywords'] }}">

    <meta property="og:type" 		content="website" />
    <meta property="og:site_name" 	content="{{ $html['meta']['site_name'] }}" />
    <meta property="og:title" 		content="{{ $html['meta']['title'] }}" />
    <meta property="og:description" content="{{ $html['meta']['description'] }}" />
    <meta property="og:url" 		content="{{ url()->current() }}" />
@for($i=0; $i<sizeof($html['meta']['ogimage']); $i++)
    <meta property="og:image" 		content="{{ $html['meta']['ogimage'][$i] }}" />
@endfor


    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-social/5.1.1/bootstrap-social.min.css" integrity="sha256-rFMLRbqAytD9ic/37Rnzr2Ycy/RlpxE5QH52h7VoIZo=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/all.min.css" integrity="sha256-46r060N2LrChLLb5zowXQ72/iKKNiw/lAmygmHExk/o=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="/theme/bootstrap4/css/main.css?t=<?=time()?>">

    <style>
        .offcanvas, .offcanvas-full{
            visibility: hidden;
        }
    </style>
    @yield('css')

</head>
<body>
    @yield('header')

    @yield('content')

    @yield('footer')


    <b class="screen-overlay"></b>

    <aside class="offcanvas-full offcanvas-top" id="canvas-search">
        <div class="container">
            <div class="row">
                <div class="col-12 my-5">
                    <form method="post" id="nav-search" name="nav-search" action="{{ route('search') }}">
                        <a class="btn-close text-white display-4"><i class="fa fa-times"></i></a>
                        <h3 class="display-1 text-white">Search</h3>

                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="search" name="search" placeholder="Search Website...">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <aside class="offcanvas offcanvas-right" id="canvas-user">
        <div class="p-3 py-4 bg-light border-bottom">
            <a class="btn btn-link btn-close"> × Close </a>
            <h6 class="mb-0">Sign In</h6>
        </div>
        <div class="sidebar-user p-3 py-4">
            <div class="py-1">
                <a class="btn btn-block btn-social btn-facebook" href="javascript:;">
                    <i class="fab fa-facebook-f"></i> Sign in with Facebook
                </a>
            </div>
            <div class="py-1">
                <a class="btn btn-block btn-social btn-google" href="javascript:;">
                    <i class="fab fa-google"></i> Sign in with Google
                </a>
            </div>
            <div class="divider">Or login with</div>
            <form method="post" id="sidebar-login" name="sidebar-login" action="{{ route('login_process') }}">
                <div class="form-group">
                    <label class="text-muted" for="sidebar-login-username"><small>Email</small></label>
                    <input type="email" class="form-control form-control-sm" id="sidebar-login-username" name="username" placeholder="Email">
                    <small class="form-text text-muted"></small>
                </div>
                <div class="form-group">
                    <label class="text-muted" for="sidebar-login-password"><small>Password</small></label>
                    <input type="password" class="form-control form-control-sm" id="sidebar-login-password" name="password" placeholder="Password">
                    <small class="form-text text-muted"></small>
                </div>
                <button type="submit" class="btn btn-sm btn-primary btn-block"><i class="fa fa-sign-in-alt"></i> Login</button>
            </form>
            <div class="divider">Or Create Account</div>
            <a href="{{ route('register_process') }}" class="btn btn-dark btn-block">
                <i class="fa fa-registered"></i> Register
            </a>
            
        </div>
    </aside>

    <aside class="offcanvas offcanvas-right" id="canvas-cart">
        <header class="p-4 bg-light border-bottom">
            <button class="btn btn-link btn-close"> × Close </button>
            <h6 class="mb-0">Cart </h6>
        </header>
        <div class="sidebar-cart">
            <ul class="list-unstyled">
                <? for($i=0; $i<2; $i++){?>
                <li>
                    <div class="card my-3 border-top-0 border-right-0 border-left-0">
                        <a class="btn-cart-delete"><i class="fa fa-times"></i></a>
                        <div class="row no-gutters">
                            <div class="col-3">
                                <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgc3R5bGU9ImZpbGw6IHJnYmEoMCwwLDAsMC4xNSk7IHRyYW5zZm9ybTogc2NhbGUoMC43NSkiPgogICAgICAgIDxwYXRoIGQ9Ik04LjUgMTMuNWwyLjUgMyAzLjUtNC41IDQuNSA2SDVtMTYgMVY1YTIgMiAwIDAgMC0yLTJINWMtMS4xIDAtMiAuOS0yIDJ2MTRjMCAxLjEuOSAyIDIgMmgxNGMxLjEgMCAyLS45IDItMnoiPjwvcGF0aD4KICAgICAgPC9zdmc+" class="card-img">
                            </div>
                            <div class="col-6">
                                <div class="card-body p-3">
                                    <h5>Product 1</h5>
                                    <p class="card-text"><small class="text-muted">$ 1,000</small></p>
                                </div>
                            </div>
                            <div class="col-3">
                                <div style="margin-top: 30%; padding-right: 10px;">
                                    <input type="number" class="form-control" value="1">
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            <? }?>
            </ul>
            <div style="border-top: 5px solid black;" class="p-2">
                <div class="py-3 text-center">
                    <i class="fa fa-money-bill-wave"></i> $<span style="font-weight: bolder; font-size:16px;">2,000</span>
                </div>
                <div class="py-1 text-center">
                    <a href="javascript:;" class="btn btn-block btn-primary"><i class="fa fa-cash-register"></i> Checkout</a>
                </div>
            </div>
        </div>
    </aside>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/js/all.min.js" integrity="sha256-+Q/z/qVOexByW1Wpv81lTLvntnZQVYppIL1lBdhtIq0=" crossorigin="anonymous"></script>

    <script src="/theme/bootstrap4/js/main.js?t=<?=time()?>"></script>

    @yield('js')
</html>