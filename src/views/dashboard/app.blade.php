
<!DOCTYPE html>
<html class="no-js css-menubar" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <meta name="robots" content="noindex">
        <meta name="googlebot" content="noindex">
        <meta name="description" content="bootstrap admin template">
        <meta name="author" content="Philip Chuang">

        <title>{{ $config['name'] }} | {{ config('ark.title') }} 管理系統</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.css">
        <link rel="stylesheet" href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic'>
        <link rel="stylesheet" href="{{ '/ark/css/blueimp.css' }}">
        <link rel="stylesheet" href="{{ '/ark/css/cropper.css' }}">
        <link rel="stylesheet" href="{{ '/ark/css/app.css' }}">
        @yield('css')

        <!-- Scripts -->
        <script src="{{ '/ark/js/breakpoints.js' }}"></script>
        <script>
            Breakpoints();
        </script>
        <style>
        </style>
    </head>
    <body class="animsition dashboard {{ session()->get('ark.config.sidebar') ? 'site-menubar-unfold' : 'site-menubar-fold' }}">
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <nav class="site-navbar navbar navbar-default navbar-fixed-top navbar-mega navbar-inverse" role="navigation">
        
            <div class="navbar-header">
                <button type="button" class="navbar-toggler hamburger hamburger-close navbar-toggler-left hided" data-toggle="menubar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="hamburger-bar"></span>
                </button>
                <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-collapse" data-toggle="collapse">
                    <i class="icon wb-more-horizontal" aria-hidden="true"></i>
                </button>
                <div class="navbar-brand navbar-brand-center site-gridmenu-toggle" data-toggle="gridmenu">
                    <a href="/">
                        <img class="navbar-brand-logo" src="/ark/images/logo/logo.png" title="{{ config('ark.title') }}">
                    </a>
                    <span class="navbar-brand-text"> {{ config('ark.title') }}</span>
                </div>
                <!--
                <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-search" data-toggle="collapse">
                    <span class="sr-only">Toggle Search</span>
                    <i class="icon wb-search" aria-hidden="true"></i>
                </button>
                -->
            </div>
    
            <div class="navbar-container container-fluid pl-0">

		        <div class="page-alert"></div>
                <!-- Navbar Collapse -->
                <div class="collapse navbar-collapse navbar-collapse-toolbar" id="site-navbar-collapse">
                    <!-- Navbar Toolbar -->
                    <ul class="nav navbar-toolbar">
                        <li class="nav-item hidden-float" id="toggleMenubar">
                            <a class="nav-link toggle-sidebar" data-toggle="menubar" href="javascript:;" role="button">
                                <i class="icon hamburger hamburger-arrow-left">
                                    <span class="sr-only">Toggle menubar</span>
                                    <span class="hamburger-bar"></span>
                                </i>
                            </a>
                        </li>
                        <li class="nav-item hidden-sm-down" id="toggleFullscreen">
                            <a class="nav-link icon icon-fullscreen" data-toggle="fullscreen" href="javascript:;" role="button">
                                <span class="sr-only">Toggle fullscreen</span>
                            </a>
                        </li>
                        <!--
                        <li class="nav-item hidden-float">
                            <a class="nav-link icon wb-search" data-toggle="collapse" href="#" data-target="#site-navbar-search"
                                role="button">
                                <span class="sr-only">Toggle Search</span>
                            </a>
                        </li>
                        -->
                    </ul>
                    <!-- End Navbar Toolbar -->
    
                    <!-- Navbar Toolbar Right -->
                    <ul class="nav navbar-toolbar navbar-right navbar-toolbar-right">
                        <li class="nav-item dropdown">
                            <a class="nav-link navbar-avatar p-t-25" data-toggle="dropdown" href="#" aria-expanded="false" data-animation="scale-up" role="button">
                                <i class="fas fa-cog fa-lg"></i>
                            </a>
                            <div class="dropdown-menu" role="menu">
                                <a class="dropdown-item" href="{{ route('administrator.profile') }}" role="menuitem"><i class="icon wb-user" aria-hidden="true"></i> 個人資料</a>
                                <div class="dropdown-divider" role="presentation"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}" role="menuitem"><i class="icon wb-power" aria-hidden="true"></i> 登出</a>
                            </div>
                        </li>
                    </ul>
                    <!-- End Navbar Toolbar Right -->
                </div>
                <!-- End Navbar Collapse -->
    
                <!-- Site Navbar Seach -->
                <div class="collapse navbar-search-overlap" id="site-navbar-search">
                    <form role="search">
                        <div class="form-group">
                            <div class="input-search">
                                <i class="input-search-icon wb-search" aria-hidden="true"></i>
                                <input type="text" class="form-control" name="site-search" placeholder="Search...">
                                <button type="button" class="input-search-close icon wb-close" data-target="#site-navbar-search" data-toggle="collapse" aria-label="Close"></button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- End Site Navbar Seach -->
            </div>
        </nav>

        <div class="site-menubar site-menubar-light">
            <div class="site-menubar-body">
                <div>
                    <div>
                        @yield('sidebar')
                    </div>
                </div>
            </div>
        </div>

        <!-- Page -->
        @yield('content')

		<div class="modal-media"></div>
        <div class="modal-main"></div>
        
        <script id="template-upload" type="text/x-tmpl"> </script>
        <script id="template-download" type="text/x-tmpl"> </script>
        <script type="text/x-tmpl" id="tmpl-image">
        {% for (var i=0; i<o.length; i++) { %}
            <div class="col-xxl-1 col-xl-2 col-lg-2 col-md-3 col-sm-6 col-xs-6 col-6 mb-3 media-single">
                <a href="javascript:;" title="" data-value="{%=o[i].id%}" data-id="{%=o[i].id%}" data-title="{%=o[i].title%}" data-path="{%=o[i].path%}">
                    <div class="card">
                        <div class="mask"></div>
                        <div class="mask-selected">
                            <i class="fas fa-3x fa-check-circle"></i>
                        </div>
                        <img src="{%=o[i].path%}" class="card-img-top" alt="{%=o[i].title%}">
                        <div class="card-body p-h-5">
                            <h5 class="card-title card-text">{%=o[i].title%}</h5>
                            <p class="card-text"><small class="text-muted">{%=o[i].created_at%}</small></p>
                        </div>
                    </div>
                </a>
            </div>
        {% } %}
        </script>
        <script type="text/x-tmpl" id="tmpl-preview">
            {% for (var i=0; i<o.length; i++) { %}
            <div class="col-xxl-3 col-xl-4 col-md-4 col-sm-4 col-xs-6 col-6 p-t-10 p-b-10 media-single" data-value="{%=o[i].id%}">
                <figure class="m-b-0">
                    <img class="img-fluid rounded-top" alt="{%=o[i].title%}" src="{%=o[i].path%}" />
                </figure>
                <div class="btn-group btn-group-justified" role="group">
                    <button type="button" class="btn btn-primary btn-media-editor">
                        <i class="fa fa-pencil"></i>
                        <span>編輯</span>
                    </button>
                    <button type="button" class="btn btn-danger btn-media-delete">
                        <i class="fa fa-remove"></i>
                        <span>刪除</span>
                    </button>
                </div>
            </div>
            {% } %}
        </script>
        <!-- End Page -->


        <!-- Footer -->
        {{--
        <footer class="site-footer">
            <div class="site-footer-legal">© 2018 <a href="http://themeforest.net/item/remark-responsive-bootstrap-admin-template/11989202">Remark</a></div>
            <div class="site-footer-right">
                Crafted with <i class="red-600 wb wb-heart"></i> by <a href="https://themeforest.net/user/creation-studio">Creation Studio</a>
            </div>
        </footer>
        --}}

        <!-- Core  -->
        <script src="{{ prefixUri('/ark/js/route') }}"></script>
        <script src="{{ '/ark/js/app.js' }}"></script>
        <script src="{{ '/ark/js/blueimp.js' }}"></script>
        <script src="{{ '/ark/js/blueimp-file-upload.js' }}"></script>
        <script src="{{ '/ark/js/cropper.js' }}"></script>
        <script src="{{ '/ark/js/Ark.js' }}"></script>

        @yield('js')

        <script>
        (function(document, window, $){
            'use strict';

            var Site = window.Site;
            $(document).ready(function(){
                Site.run();

                $('.toggle-sidebar').click(function(){
                    $.ajax({
                        url: route(routes.request.toggle_sidebar),
                        type: 'POST',
                        data: {
                            status: status
                        },
                        success: function(response) {
                            console.log(response);
                        }
                    });
                })
            });
        })(document, window, jQuery);
        </script>
  </body>
</html>
