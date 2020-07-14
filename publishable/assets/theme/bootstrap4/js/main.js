$(function () {
    'use strict'

    $("[data-trigger]").on("click", function(){
        var trigger_id =  $(this).attr('data-trigger');
        $(trigger_id).toggleClass("show");
        $('body').toggleClass("offcanvas-active");
        $(".screen-overlay").toggleClass("show");
    });

    $('.dropdown-item.dropdown-toggle').on("click", function(e){
        $(this).toggleClass('show');
        $(this).next('ul').toggleClass('show');
        e.stopPropagation();
        e.preventDefault();
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
        $('#search').focus();
        //$(".screen-overlay").toggleClass("show");
    });

    $('.btn-user').click(function(e){
        e.preventDefault();
        e.stopPropagation();
        var $next = e.currentTarget.nextSibling;
        if(!$next){
            $('#canvas-user').toggleClass("show");
            $('body').toggleClass("offcanvas-active");
            $(".screen-overlay").toggleClass("show");
        }
    });

    $('.btn-cart').click(function(e){
        e.preventDefault();
        e.stopPropagation();
        $('#canvas-cart').toggleClass("show");
        $('body').toggleClass("offcanvas-active");
        $(".screen-overlay").toggleClass("show");
    });


    var navbarCollapse = function() {
        if ($("#mainHeader").offset().top > 100) {
        $("#mainHeader").addClass("navbar-scrolled");
        } else {
        $("#mainHeader").removeClass("navbar-scrolled");
        }
    };
    // Collapse now if page is not at top
    navbarCollapse();
    // Collapse the navbar when page is scrolled
    $(window).scroll(navbarCollapse);
});

function form_highlight(element, errorClass, validClass){
	$(element).removeClass('is-valid').addClass('is-invalid');

	if($(element).hasClass('selectpicker')){
		$(element).next('.btn').addClass('btn-danger');
		$(element).parents('.input-group').addClass('is-invalid');
	}
}

function form_unhighlight(element, errorClass, validClass){
	$(element).removeClass('is-invalid').addClass('is-valid');

	if($(element).hasClass('selectpicker')){
		$(element).next('.btn').removeClass('btn-danger');
		$(element).parents('.input-group').removeClass('is-invalid');
	}
}
	
function form_error_text(error, element){
	var text = error[0].innerText;
	if(text){
		element.parents('.form-group').find('.invalid-tooltip, .invalid-feedback').text(text);
	}
}

