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


var toolbar_top,		//item page submit toolbar
	toolbar_width;

$(document).ready(function(){
	Ark.init();
});

var Ark = function(){
	'use strict';
	
	var header_message = function($message, $type='success', $delay=3000)
	{
		$('.page-alert').html('<div class="rounded-0 alert alert-'+$type+'"><div class="text-center">'+$message+'</div></div>').delay(500).slideDown(500).delay($delay).slideUp(1000);
	};
	
	var submit = function()
	{
		$('.btn-submit').click(function(){
			$('#__method').val($(this).data('method'));
			//var $method = $(this).data('method');
		});
	};

	var submitHandler = function($form)
	{
		var method = parseInt($('#__method').val());
		switch(method){
			default:
			case 0:
				$form.submit();
				break;
			case 1:
				//ajax submit
				$('#form1').ajaxSubmit(function(response){
					Ark.header_message('<strong>儲存完畢</strong>，您可以繼續編輯了');
					$('#id').val(response.id);
				});
				break;
		}
	};

	var tag = function($element, $multiple=false, $sortable=false)
	{
		$($element).select2({
			theme: 'bootstrap4',
			placeholder: "請選擇標籤",
			tags: $multiple,
			tokenSeparators: [',', ' '],
			width: '100%',
			containerCssClass: ':all:',
			minimumInputLength: 1,
			createTag: function(params){
				var term = $.trim(params.term);
				if(term === ''){
					return null;
				}
	
				return {
					id: term,
					text: term,
					new: true
				}
			},
			ajax: {
				url: route(routes.tag.search),
				delay: 100,
				dataType: 'json'
			}
		}).on('select2:selecting', function(event){
			//選擇之前，先加入資料庫，之後再放入選擇中
			var data = event.params.args.data;
			if(data.new){
				if(confirm("確定新增此標籤？")){
					$.ajax({
						type: 'post',
						url: route(routes.tag.insert),
						data: {
							text: data.text
						},
						error: function() {
							console.log('AJAX Error');
						},
						success: function(response) {
							var newOption = new Option(response.text, response.id, true, true);
							$(event.currentTarget).append(newOption).trigger('change');
							$(event.currentTarget).select2('close');
						}
					});
				}
			}else{
				var newOption = new Option(data.text, data.id, true, true);
				$(event.currentTarget).append(newOption).trigger('change');
				$(event.currentTarget).select2('close');
			}
			return false;
		});

		if($sortable){
			$($element+" ul.select2-selection__rendered").sortable({
				containment: 'parent'
			});
		}
	}
	
	var ogimage = function($max){
		$('.btn-ogimage-manager').media({
			area: 					'.ogimage-area',
			input_field: 			'.ogimage_input',
			selectable_limit:		$max,
			selectable_multiple:	1,
			size:					'facebook',
		});
	};

	var datepicker = {
		date: function($e)
		{
			$($e)
			.daterangepicker({
				autoUpdateInput: false,
				singleDatePicker: true,
				locale: {
					format: 'YYYY/MM/DD'
				}
			})
			.inputmask({
				alias: "datetime",
				inputFormat: "yyyy/mm/dd",
				placeholder: "YYYY/MM/DD",
			});
			
			$($e).on('apply.daterangepicker', function(ev, picker){
				$(this).val(picker.startDate.format('YYYY/MM/DD'));
			});
			
			$($e).on('cancel.daterangepicker', function(){
				$(this).val('');
			});
		},
		datetime: function($e)
		{
			$($e)
			.daterangepicker({
				autoUpdateInput: false,
				singleDatePicker: true,
				timePicker: true,
				timePicker24Hour: true,
				locale: {
					format: 'YYYY/MM/DD H:mm'
				}
			})
			.inputmask({
				alias: "datetime",
				inputFormat: "yyyy/mm/dd HH:MM",
				placeholder: "YYYY/MM/DD HH:MM",
			});
			
			$($e).on('apply.daterangepicker', function(ev, picker){
				$(this).val(picker.startDate.format('YYYY/MM/DD HH:mm'));
			});
			
			$($e).on('cancel.daterangepicker', function(){
				$(this).val('');
			});
		},
		date_range: function($e)
		{
			$($e)
			.daterangepicker({
				autoUpdateInput: false,
				locale: {
					format: 'YYYY/MM/DD'
				}
			})
			.inputmask({
				alias: "datetime",
				inputFormat: "yyyy/mm/dd ~ yyyy/mm/dd",
				placeholder: "YYYY/MM/DD ~ YYYY/MM/DD",
			});
			
			$($e).on('apply.daterangepicker', function(ev, picker){
				$(this).val(picker.startDate.format('YYYY/MM/DD') + ' ~ ' + picker.endDate.format('YYYY/MM/DD'));
			});
			
			$($e).on('cancel.daterangepicker', function(){
				$(this).val('');
			});
		},
		datetime_range: function($e)
		{
			$($e)
			.daterangepicker({
				autoUpdateInput: false,
				locale: {
					format: 'YYYY/MM/DD H:mm'
				}
			})
			.inputmask({
				alias: "datetime",
				inputFormat: "yyyy/mm/dd HH:MM ~ yyyy/mm/dd HH:MM",
				placeholder: "YYYY/MM/DD HH:MM ~ YYYY/MM/DD HH:MM",
			});
			
			$($e).on('apply.daterangepicker', function(ev, picker){
				$(this).val(picker.startDate.format('YYYY/MM/DD HH:MM') + ' ~ ' + picker.endDate.format('YYYY/MM/DD HH:MM'));
			});
			
			$($e).on('cancel.daterangepicker', function(){
				$(this).val('');
			});
		}
	};

	var color = function($el, $input, $color)
	{
		const pickr = Pickr.create({
			el: $el,
			theme: 'classic', // or 'monolith', or 'nano'
			default: $color,
		
			swatches: [
				'rgba(244, 67, 54, 1)',
				'rgba(233, 30, 99, 0.95)',
				'rgba(156, 39, 176, 0.9)',
				'rgba(103, 58, 183, 0.85)',
				'rgba(63, 81, 181, 0.8)',
				'rgba(33, 150, 243, 0.75)',
				'rgba(3, 169, 244, 0.7)',
				'rgba(0, 188, 212, 0.7)',
				'rgba(0, 150, 136, 0.75)',
				'rgba(76, 175, 80, 0.8)',
				'rgba(139, 195, 74, 0.85)',
				'rgba(205, 220, 57, 0.9)',
				'rgba(255, 235, 59, 0.95)',
				'rgba(255, 193, 7, 1)'
			],
		
			components: {
		
				// Main components
				preview: true,
				opacity: true,
				hue: true,
		
				// Input / output Options
				interaction: {
					hex: true,
					rgba: true,
					hsla: true,
					hsva: true,
					cmyk: true,
					input: true,
					clear: true,
					save: true
				}
			}
		});
	
		pickr.on('save', (color, instance) => {
			console.log(color.toHEXA().toString());
			$($input).val(color.toHEXA().toString());
		});
	};

	var zip = function($city, $area, $empty)
	{
		$($city).selectpicker();
		$($area).selectpicker();

		$($city).change(function(){
			var id = $(this).val();
			$.ajax({
				url: route(routes.request.zip),	//檔案位置
				type: 'POST',	//or POST
				data: {
					id: id
				},
				error: function(xhr, textStatus, errorThrown){
					alert(xhr.status+' '+errorThrown);
				},
				success: function(response){
					var $res = response;
						//console.log(res);
					$($area).empty();

					if($empty){
						$($area)
							.append($("<option></option>")
							.attr("value",0)
							.text('--- 無 ---')); 
					}
					
					$.each($res, function(key, value){
						if(key==0){
							$($area)
							.append($("<option></option>")
							.attr('selected', 'selected')
							.attr("value",value.id)
							.text(value.text)); 
						}else{
							$($area)
							.append($("<option></option>")
							.attr("value",value.id)
							.text(value.text)); 
						}
					});

					$($area).selectpicker('refresh');
				}
			});
		});
	}
	
	var init = function(){

	};
	
	return {
		init: function()
		{
			init();
		},
		header_message: function($message, $type, $delay)
		{
			header_message($message, $type, $delay);
		},
		submit: function()
		{
			submit();
		},
		submit_mce: function()
		{
			submit_mce();
		},
		submitHandler: function($form)
		{
			submitHandler($form);
		},
		tag: function($element, $multiple=false, $sortable=false)
		{
			tag($element, $multiple, $sortable);
		},
		product: function($element, $multiple=false, $sortable=false)
		{
			product($element, $multiple, $sortable);
		},
		ogimage: function($max=3)
		{
			ogimage($max);
		},
		datepicker: {
			date: function($e)
			{
				datepicker.date($e);
			},
			datetime: function($e)
			{
				datepicker.datetime($e);
			},
			date_range: function($e)
			{
				datepicker.date_range($e);
			},
			datetime_range: function($e)
			{
				datepicker.datetime_range($e);

			}
		},
		color: function($el, $input, $color){
			color($el, $input, $color);
		},
		zip: function($city, $area, $empty=0){
			zip($city, $area, $empty);
		}
	};
}();
(function($){
	'use strict';
	
	var retrieving = false;
	
	var option = {};
	
	var $media_id = [];

	var $container;
	
	var mce = false;

	var default_option = {
		is_image:				1,
		selectable_limit:		0,
		selectable_multiple:	1,
		max_position:			0,
		url:	{
			manager:			route(routes.media.manager),
			data:				route(routes.media.data),
			upload:				route(routes.media.upload),
			//update:				route(routes.media.update),
			editor:				route(routes.media.editor),
		},
		form:					'.form-media',
		modal_btn:				'.btn-media-manager',
		modal_container:		'.modal-media',
		modal_content:			'#modal-media',
		modal_body:				'.modal-body',
		input_field: 			'.media_input',
		area: 					'.media-area',
		element_selected:		'#selected',
		element_selectable:		'#media-row',
		element_single:			'.media-single',
		element_id:				'.media_id',
		delete_btn:				'.btn-media-delete',
		editor_btn:				'.btn-media-editor',
		save_btn:				'.btn-media-save',
		template_upload_id:		'template_upload',
		template_download_id:	'template_download',
		full_column:			false,
		size:					'square',
	};
 	
	function mediaMultiple(e, config){
		var $prototype = this;
		
		if(typeof config == "undefined" || config.area){
			$(e).on('click', function(){
				mce = false;
				option = $.extend({}, default_option, config );
				$prototype.manager();
			});

			option = $.extend({}, default_option, config);
			$prototype.sortable(option);
			$prototype.image.delete(option);
			$prototype.image.editor(option);
		}else{
			mce = true;
			option = $.extend({}, default_option, config );
			$prototype.manager();
		}
	}
	
	mediaMultiple.prototype = {
		constructor: mediaMultiple,

		manager: function()
		{
      		var $prototype = this;
			var data = {};
			
			$.ajax({
				type: 'POST',
				url: option.url.manager,
				data: data,
				error: function(xhr, textStatus) {
					console.log(xhr+' '+textStatus);
				},
				success: function(data, textStatus, jqXHR){
					$(option.modal_container).html(data);
					$(option.modal_content).modal();
					$prototype.modal();
				}
			});
		},

		modal: function()
		{
      		var $prototype = this;
			retrieving = false;
			$media_id = [];

			//上傳
			$prototype.upload();

			//搜尋
			$prototype.search();

			//無限捲軸
			$prototype.infinityScroll();

			//存檔
			$(option.save_btn).click(function(){
				//console.log($media_id);
				var $data = [];
				$.each($media_id, function($index, $value){
					var $e = $('.media-single a[data-id='+$value+']')
					var $title = $e.data('title');
					var $path = $e.data('path');

					$data.push({
						'id': 		$value,
						'title': 	$title,
						'path': 	$path,
					});
				});

				if(option.selectable_multiple){
					$(option.area).append(tmpl('tmpl-preview', $data));
					var $val = $(option.input_field).val();
					if(!$val){
						$(option.input_field).val($media_id.join());
					}else{
						$(option.input_field).val($val+','+$media_id.join());
					}
				}else{
					$(option.area).html(tmpl('tmpl-preview', $data));
					$(option.input_field).val($media_id.join());
				}

				$prototype.image.delete(option);
				$prototype.image.editor(option);
				$(option.modal_content).modal('hide');

			});
		},
		
		/*
			無限捲軸
		*/
		infinityScroll: function(type)
		{
			var $prototype = this;
			$container = $('#media-row');

			$container.infiniteScroll({
				path: function() {
					var $url = route(routes.media.data)+'?page='+ (this.pageIndex) + '&request_time='+$('.form-media #request_time').val();
					if(option.selectable_multiple){
						$url += '&skip='+$(option.input_field).val();
					}
					var $search = $('.form-media #input-search').val();
					if($search){
						$url += '&search='+$search;
					}
					return $url;
				},
				append: false,
				history: false,
				responseType: 'text',
				loadOnScroll: true,
				elementScroll: "#media-container",
				button: '.pagination__next',
				checkLastPage: ".pagination__next",
				status: ".scroller-status",
			}).infiniteScroll('loadNextPage');

			$container.on('load.infiniteScroll', function(event, response){
				var $result = response;
				var $items = $( tmpl('tmpl-image', $result) );

				$container.infiniteScroll( 'appendItems', $items );
				$prototype.selectable();
			});
		},
		
		/*
			上傳檔案
		*/
		upload: function()
		{
			$('#fileupload').fileupload({
				url: route(routes.media.upload),
				autoUpload: true,
				progressall: function (e, data) {
					var progress = parseInt(data.loaded / data.total * 100, 10);
					$('.progress-bar').css(
						'width',
						progress + '%'
					);
					$('.progress-bar span').html(progress + '%');
				},
				done: function(e, data){
					var $result = []
					$result.push(data.result);

					$('#media-row').prepend(tmpl('tmpl-image', $result));
				},
				fail: function(e, data){
					console.log('failed');
					console.log(e);
					console.log(data);
				}
			});
		},
		
		search: function()
		{
			var $prototype = this;
			
			$('#btn-search').click(function(){
				$prototype.search_ajax();
			});
			$('#input-search').keypress(function(e) {
				if(e.which == 13) {
					$prototype.search_ajax();
				}
			});
		},
		
		search_ajax: function()
		{
			var $prototype = this;
			var infScroll = $container.data('infiniteScroll');
			infScroll.pageIndex = 1;
			$container.html('');
			$container.infiniteScroll('loadNextPage');
		},
		
		selectable: function()
		{
			var $prototype = this;
			
			$(option.element_selectable).selectable()
			.on("selectablestart", function(event, ui){
				if(option.selectable_multiple){
					event.originalEvent.ctrlKey = true;
				}
			})
			.on("selectableselected", function(event, ui){
				var $id = parseInt($(ui.selected).children('a').data('value'));

				if(!$id){
					return 0;
				}

				if($.inArray($id, $media_id)==-1){
					$media_id.push($id);
				}
				//console.log($media_id);
			}).on("selectableunselected", function(event, ui){
				var $id = parseInt($(ui.unselected).children('a').data('value'));

				if(!$id){
					return 0;
				}

				$($media_id).each(function(index, element) {
					if($media_id[index]==$id){
						$media_id.splice(index, 1);
					}
				});
				//console.log($media_id);
			});
			/*
			if(option.selectable_limit>1){
				//數量限制
				$(option.element_selectable).on("selectableselecting", function(event, ui){
					if(ids.length >= option.selectable_limit){
						$(ui.selecting).removeClass("ui-selecting");
					}
				});
			}
			*/
		},
	
		/*
			排序
		*/
		sortable: function(option)
		{
			$(option.area).sortable({
				start: function(e, ui){
					var height = ui.item.outerHeight();
					ui.placeholder.css({
						'background':'#ebf6fb',
						'height': height+'px'
					});
				},
				stop: function( event, ui ) {
					var id_array = [];
					$(option.area+' '+option.element_single).each(function(index, element){
						id_array.push($(this).data('value'));
					});
					$(option.input_field).val(id_array.join());
				}
			});
		},
		
		/*
			已經選擇的圖片
		*/
		image: {
			delete: function(option){
				$(option.area+' '+option.delete_btn).unbind('click');
				$(option.area+' '+option.delete_btn).click(function(){
					var $this = $(this);
					var media_id = $this.parents(option.element_single).data('value');
					var input_field = $(option.input_field).val().split(',');
					var id_array = [];
					
					Swal.fire({
						type: 'question',
						title: '刪除？',
						text: '確定不要這張圖片了嗎？',
						showCancelButton: true,
						confirmButtonColor: '',
						cancelButtonColor: '',
						confirmButtonClass: 'btn btn-danger',
						cancelButtonClass: 'btn btn-dark',
						confirmButtonText: '是！',
						cancelButtonText: '取消',
						reverseButtons: true,
					}).then(function () {
						$.each(input_field, function(index, value){
							if(value==media_id){
								$this.parents(option.element_single).remove();
							}else{
								id_array.push(value);
							}
							$(option.input_field).val(id_array)
						});
					});
				});
			},
			editor: function(option){
				$(option.area+' '+option.editor_btn).unbind('click');
				$(option.area+' '+option.editor_btn).click(function(){
					var $this = $(this);
					var $media_id = $this.parents(option.element_single).data('value');
					var $size = option.size;

					$.ajax({
						type: 'GET',
						url: option.url.editor,
						data:{
							id: $media_id,
							size: $size,
						},
						error: function(xhr, textStatus) {
							console.log(xhr+' '+textStatus);
						},
						success: function(data, textStatus, jqXHR){
							$(option.modal_container).html(data);
							$(option.modal_content).modal();
						}
					});
				});
			}
		},
			
	};
	
    $.fn.media = function(config){
		var results = [];
		
		return this.each(function(){
			new mediaMultiple(this, config);
		});
    };
	
	$.fn.media.Constructor = mediaMultiple;
 
}(jQuery));
var Cropper = function(){
	'use strict';

	var $custom_crop,
		$index,
		$cropper = [],
		$crop_data;

	var init = function()
	{
		$custom_crop = $('#custom_crop');
		$crop_data = $('#crop_data').val();
		if($crop_data){
			$crop_data = $.parseJSON($crop_data);
		}

		tab();
		custom();
		$custom_crop.click(function(){
			custom();
		});

		setTimeout(function(){
			$(".nav-tabs-vertical li:first-child a").trigger('click');
		}, 500);
	};

	var custom = function(){
		var status = $custom_crop.is(":checked");

		$('.cropper li').each(function(){
			if($(this).children('a').data('custom-crop')=="1"){
				if(status==true){
					$(this).children('a').removeClass('disabled');
				}else{
					$(this).children('a').addClass('disabled');
				}
			}
		});
	};

	var tab = function()
	{
		$(".nav-tabs-vertical a").click(function(e) {
			e.preventDefault();

			if ($(this).hasClass("disabled")){
				return false;
			}

			$index = $(this).parent().index();

			var $minWidth = $(this).data('width');
			var $minHeight = $(this).data('height');
			var $config = $(this).data('config');
			var $cropper_data = $(this).find('.cropper_data');
			var $value = $cropper_data.val() ? $.parseJSON($cropper_data.val()) : {};
			//var $data = {};

			var $default = {
				autoCropArea: 1,
				zoomable: false,
				viewMode: 1,
				responsive: true,
				cropmove: function(e){
					var data = $(e.target).cropper('getData');

					if(data.width < $minWidth || data.height < $minHeight){
						console.log("Minimum size reached!");
						e.preventDefault();
					}
				},
				cropend: function(e){
					var data = $(e.target).cropper('getData');

					if (data.width < $minWidth || data.height < $minHeight) {
						data.width = $minWidth;
						data.height = $minHeight;
						
						$(e.target).cropper('setData', data);
					}

					var $data = $(e.target).cropper('getData');
					$data.x = Math.floor($data.x);
					$data.y = Math.floor($data.y);
					$data.width = Math.floor($data.width);
					$data.height = Math.floor($data.height);
					$cropper_data.val(JSON.stringify($data));
				},
				data: $value
			};
			var $configs = $.extend($default, $config);
			$cropper[$index] = $('.image').eq($index).cropper($configs);
			//$cropper[$index].cropper('setCanvasData', {width:'100%'})
		});
	};

	return {
		init: function(){
			init();
		}
	};
}();