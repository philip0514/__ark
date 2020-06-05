<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">


    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/all.min.css" integrity="sha256-46r060N2LrChLLb5zowXQ72/iKKNiw/lAmygmHExk/o=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://builder.philip.place/theme/bootstrap4/css/main.css?t=<?=time()?>">

    @yield('css')

</head>
<body>
    @yield('header')

    @yield('content')

    @yield('footer')

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/js/all.min.js" integrity="sha256-+Q/z/qVOexByW1Wpv81lTLvntnZQVYppIL1lBdhtIq0=" crossorigin="anonymous"></script>

    @yield('js')


    <script type="text/javascript">
    $(function () {
        'use strict'
    
        $("[data-trigger]").on("click", function(){
            var trigger_id =  $(this).attr('data-trigger');
            $(trigger_id).toggleClass("show");
            $('body').toggleClass("offcanvas-active");
        });
    
        // close if press ESC button 
        $(document).on('keydown', function(event) {
            if(event.keyCode === 27) {
               $(".navbar-collapse").removeClass("show");
               $(".offcanvas, .offcanvas-full").removeClass("show");
               $("body").removeClass("overlay-active");
            }
        });
    
        // close button 
        $(".btn-close, .screen-overlay, .btn-close-search").click(function(e){
            $(".navbar-collapse").removeClass("show");
            $(".screen-overlay").removeClass("show");
            $(".offcanvas, .offcanvas-full").removeClass("show");
            $("body").removeClass("offcanvas-active");
        });
    
        $('.btn-search').click(function(e){
            e.preventDefault();
            e.stopPropagation();
            $('#canvas-search').toggleClass("show");
            $('body').toggleClass("offcanvas-active");
            //$(".screen-overlay").toggleClass("show");
        });
    
        $('.btn-user').click(function(e){
            e.preventDefault();
            e.stopPropagation();
            $('#canvas-user').toggleClass("show");
            $('body').toggleClass("offcanvas-active");
            $(".screen-overlay").toggleClass("show");
        });
    
        $('.btn-cart').click(function(e){
            e.preventDefault();
            e.stopPropagation();
            $('#canvas-cart').toggleClass("show");
            $('body').toggleClass("offcanvas-active");
            $(".screen-overlay").toggleClass("show");
        });
    })
    </script>
</body>
</html>