var Product = function(){
	'use strict';

	var init = function(){
		modal();
	};

	var datatable_price = function(column){
		var dom = 
		"<'row toolbar'>"+
		"<'row'"+
			"<'col-md-3'"+
				"l"+
			">"+
			"<'col-md-9'"+
				"p"+
			">"+
		">"+
		"<'row'"+
			"<'col-md-12'"+
				"t"+
			">"+
		">"+
		"<'row'<'col-md-4'i><'col-md-8'p>>";

		datatablePrice = $('.price-table table').DataTable({
			"dom":dom,
			"pageLength": 10,
			"displayStart": 0,

			"columnDefs": column.columnDefs,
			"order": column.order,
			"columns": column.columns,

			"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "全部"]],

			"language": {
				"lengthMenu": "_MENU_",
				"zeroRecords": "無資料",
				"info": '_START_ ~ _END_ / 共 _TOTAL_ 筆',
				"infoEmpty": "",
				"infoFiltered": '(全部 _MAX_ 筆)',
				"paginate": {
					"previous": '<i class="fa fa-chevron-left"></i>',
					"next":     '<i class="fa fa-chevron-right"></i>',
				},
				"processing": "<div class='processing_alert alert alert-warning'>Loading. Please wait...</div>"
			},
			responsive: true,

			//ajax
			"processing": true,
			"serverSide": true,
			"ajax": function (data, callback){
				var product_id = $('#id').val();
				product_id = product_id ? product_id : 0;

				$.ajax({
					"url": route(routes.product.datatablePrice, {id: product_id}),
					"data": data,
					"dataType": 'json',
					"type": "GET",
					"error": function(response){
					},
					"success": function(response){
						callback(response);
					}
				});
			},

			"initComplete": function(){
				$('.dataTables_length select').removeClass('form-control-sm').data({
					'style': 'btn-outline-secondary'
				}).selectpicker();
			},
			"drawCallback": function(setting){
				modal();
			},
			"rowCallback": function(row, data, displayIndex, displayIndexFull){
				var displayClass = "";
				if(data.display == "0"){
					displayClass = "bg-light text-secondary font-italic";
				}
				$(row).addClass(displayClass);

				return row;
			},

			//save
			stateSave: true,
			iCookieDuration: 60*60*1
		});	
	};

	var datatable_inventory = function(column){
		var dom = 
		"<'row toolbar'>"+
		"<'row'"+
			"<'col-md-3'"+
				"l"+
			">"+
			"<'col-md-9'"+
				"p"+
			">"+
		">"+
		"<'row'"+
			"<'col-md-12'"+
				"t"+
			">"+
		">"+
		"<'row'<'col-md-4'i><'col-md-8'p>>";

		datatableInventory = $('.inventory-table table').DataTable({
			"dom":dom,
			"pageLength": 10,
			"displayStart": 0,
			"columnDefs": column.columnDefs,
			"order": column.order,
			"columns": column.columns,
			"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "全部"]],
			"language": {
				"lengthMenu": "_MENU_",
				"zeroRecords": "無資料",
				"info": '_START_ ~ _END_ / 共 _TOTAL_ 筆',
				"infoEmpty": "",
				"infoFiltered": '(全部 _MAX_ 筆)',
				"paginate": {
					"previous": '<i class="fa fa-chevron-left"></i>',
					"next":     '<i class="fa fa-chevron-right"></i>',
				},
				"processing": "<div class='processing_alert alert alert-warning'>Loading. Please wait...</div>"
			},
			responsive: true,

			//ajax
			"processing": true,
			"serverSide": true,
			"ajax": function (data, callback){
				var product_id = $('#id').val();
				product_id = product_id ? product_id : 0;

				$.ajax({
					"url": route(routes.product.datatableInventory, {id: product_id}),
					"data": data,
					"dataType": 'json',
					"type": "GET",
					"error": function(response){
					},
					"success": function(response){
						callback(response);
					}
				});
			},
			"initComplete": function(){
				$('.dataTables_length select').removeClass('form-control-sm').data({
					'style': 'btn-outline-secondary'
				}).selectpicker();
			},
			"drawCallback": function(setting){
				modal();
			},
			"rowCallback": function(row, data, displayIndex, displayIndexFull){
				var displayClass = "";
				if(data.display == "0"){
					displayClass = "bg-light text-secondary font-italic";
				}
				$(row).addClass(displayClass);

				return row;
			},

			//save
			stateSave: true,
			iCookieDuration: 60*60*1
		});	
	};

	var datatable_plus = function(column){
		var dom = 
		"<'row toolbar'>"+
		"<'row'"+
			"<'col-md-3'"+
				"l"+
			">"+
			"<'col-md-9'"+
				"p"+
			">"+
		">"+
		"<'row'"+
			"<'col-md-12'"+
				"t"+
			">"+
		">"+
		"<'row'<'col-md-4'i><'col-md-8'p>>";

		datatablePlus = $('.plus-table table').DataTable({
			"dom": dom,
			"pageLength": 10,
			"displayStart": 0,
			"columnDefs": column.columnDefs,
			"order": column.order,
			"columns": column.columns,
			"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "全部"]],
			"language": {
				"lengthMenu": "_MENU_",
				"zeroRecords": "無資料",
				"info": '_START_ ~ _END_ / 共 _TOTAL_ 筆',
				"infoEmpty": "",
				"infoFiltered": '(全部 _MAX_ 筆)',
				"paginate": {
					"previous": '<i class="fa fa-chevron-left"></i>',
					"next":     '<i class="fa fa-chevron-right"></i>',
				},
				"processing": "<div class='processing_alert alert alert-warning'>Loading. Please wait...</div>"
			},
			responsive: true,

			//ajax
			"processing": true,
			"serverSide": true,
			"ajax": function (data, callback){
				var product_id = $('#id').val();
				product_id = product_id ? product_id : 0;

				$.ajax({
					"url": route(routes.product.datatablePlus, {id: product_id}),
					"data": data,
					"dataType": 'json',
					"type": "GET",
					"error": function(response){
					},
					"success": function(response){
						callback(response);
					}
				});
			},
			"initComplete": function(){
				$('.dataTables_length select').removeClass('form-control-sm').data({
					'style': 'btn-outline-secondary'
				}).selectpicker();
			},
			"drawCallback": function(setting){
				modal();
			},
			"rowCallback": function(row, data, displayIndex, displayIndexFull){
				var displayClass = "";
				if(data.display == "0"){
					displayClass = "bg-light text-secondary font-italic";
				}
				$(row).addClass(displayClass);

				return row;
			},

			//save
			stateSave: true,
			iCookieDuration: 60*60*1
		});	
	};
	
	var modal = function(){
		
		$('.btn-modal').unbind('click');
		$('.btn-modal').click(function(){
			var type = $(this).data('type'),
				url = '',
				product_id = parseInt($('#id').val()),
				price_id = 0,
				inventory_id = 0,
				plus_id = 0
			;
			
			switch(type){
				case 'category':
					url = route(routes.product.createCategory);
					break;
				case 'color':
					url = route(routes.product.createColor);
					break;
				case 'spec':
					url = route(routes.product.createSpec);
					break;
				case 'style':
					url = route(routes.product.createStyle);
					break;
				case 'price':
					if(!product_id){
						Swal.fire({
							type: 'error',
							title: '抱歉',
							text: '請先存檔，才能增加價格',
							confirmButtonColor: '',
							confirmButtonClass: 'btn btn-primary',
							confirmButtonText: '關閉',
						});
						return 0;
					}else{
						price_id = parseInt($(this).data('id'));
						url = route(routes.product.price, {product_id:product_id, id: (price_id?price_id:'')});
					}
					break;
				case 'inventory':
					if(!product_id){
						Swal.fire({
							type: 'error',
							title: '抱歉',
							text: '請先存檔，才能增加庫存',
							confirmButtonColor: '',
							confirmButtonClass: 'btn btn-primary',
							confirmButtonText: '關閉',
						});
						return 0;
					}else{
						inventory_id = parseInt($(this).data('id'));
						url = route(routes.product.inventory, {product_id:product_id, id: (inventory_id?inventory_id:'')});
					}
					break;
				case 'plus':
					if(!product_id){
						Swal.fire({
							type: 'error',
							title: '抱歉',
							text: '請先存檔，才能增加贈品/加購',
							confirmButtonColor: '',
							confirmButtonClass: 'btn btn-primary',
							confirmButtonText: '關閉',
						});
						return 0;
					}else{
						plus_id = parseInt($(this).data('id'));
						url = route(routes.product.plus, {product_id:product_id, id: (plus_id?plus_id:'')});
					}
					break;
				default:
					return 0;
					break;
			}
			
			$.ajax({
				url: url,
				type: 'GET',
				data: {},
				error: function(xhr, textStatus, errorThrown){
					console.log(xhr.status+' '+errorThrown);
				},
				success: function(response) {
					$('.modal-main').html(response);
					$('#modal-main').modal('show');
				}
			});
			
		});
	};

/*
	var submit = function(){
		$('.btn-submit').click(function(){
			tinyMCE.triggerSave();
			$('#__method').val($(this).data('method'));
		});
	};
	
	var handle = function($form){
	
		var method = parseInt($('#__method').val());
		switch(method){
			default:
			case 0:
				$form.submit();
				break;
			case 1:
				//ajax submit
				$('#form1').ajaxSubmit(function(response){
					Main.header_message('<strong>儲存完畢</strong>，您可以繼續編輯了');
					var res = $.parseJSON(response);
					$('#id').val(res.id);
				});
				break;
		}
		
    };
*/
	
	return {
		init: function(){
			init();
		},
		handle: function($form){
			handle($form);
		},
		datatable_price: function(column){
			datatable_price(column);	
		},
		datatable_inventory: function(column){
			datatable_inventory(column);	
		},
		datatable_plus: function(column){
			datatable_plus(column);	
		},
	}
}();