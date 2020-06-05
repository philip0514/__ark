<div class="row">
    <div class="col-md-6 xs-p-b-10">
        <div class="input-group select-group">
            <select class="select-action show-tick selectpicker" data-style="btn-primary">
                <optgroup label="功能操作">
                    @if($action['display'])
                        <option data-icon="fa fa-eye" value="1"> 啟用</option>
                        <option data-icon="fa fa-eye-slash" value="2"> 停用</option>
                    @endif
                    @if($action['recommend'])
                    <option data-icon="fa fa-star" value="6"> 推薦</option>
                    <option data-icon="fa fa-star-half-alt" value="7"> 取消推薦</option>
                    @endif
                    @if($action['softDelete'])
                    <option class="text-danger" data-icon="fa fa-trash" value="4"> 刪除</option>
                    <option class="text-danger" data-icon="fa fa-trash-restore" value="5"> 取消刪除</option>
                    @endif
                    @if($action['delete'])
                    <option data-icon="fa fa-times" value="3" class="bg-danger text-white"> 永遠刪除</option>
                    @endif
                    @if($action['export'])
                    <option data-icon="fa fa-cloud-download" value="8"> 匯出</option>
                    @endif
                </optgroup>
                @if($action['export'])
                <option data-icon="fa fa-cloud-download" value="8"> 匯出</option>
                @endif
            </select>
            <div class="input-group-append">
                <button class="btn btn-primary btn-action" type="button"><i class="fa fa-play"></i></button>
            </div>
        </div>
    </div>

    <div class="col-md-6 xs-p-b-10">
        <select class="column-visible" data-style="btn-dark" data-width="100%" multiple data-selected-text-format="count > 3">
            <optgroup label="欄位顯示">
                @for($i=0; $i<sizeof($visible); $i++)
                <option 
                    value="{{ $visible[$i]['value'] }}" 
                    @if($visible[$i]['selected']=="true") 
                    selected="selected"
                    @endif
                >
                    {{ $visible[$i]['name'] }}
                </option>
                @endfor
            </optgroup>
        </select>
    </div>'
</div>
<script>
$(function(){
    //search
    $('.btn-filter').click(function(){
        var width = $(this).parents('.row').width();
        $(this).parents('.row').find('.filter-content').toggle().width(width);
    });

    $('.btn-filter-submit').click(function(){
        $(this).parents('.row').find('.filter-content').toggle();
        Datatables.filter.field();
        $('.btn-search').trigger('click');
    });

    $('.btn-filter-cancel').click(function(){
        $(this).parents('.row').find('.filter-content').toggle();
    });

    $('#search').keypress(function(e){
        if(e.keyCode === 13){
            Datatables.filter.field();
            $('.btn-search').trigger('click');
        }
    });

    $('.btn-search').click(function(){
        var data = {};
        datatable.search($('#search').val()).draw();

    });
    
    //功能操作
    $('.btn-action').click(function(){
        var type = $('select.select-action').val();
        var id = [];

        switch(parseInt(type)){
            default:
                $('.row_select').each(function(){
                    if($(this).prop('checked')){
                        id.push($(this).parents('td').find('.input-id').val());
                    }
                });
                if(id.length){
                    $.ajax({
                        url: config.path.action,
                        type: 'POST',
                        data: {
                            type: type,
                            id: id
                        },
                        error: function(xhr, textStatus, errorThrown){
                            console.log(xhr.status+' '+errorThrown);
                        },
                        success: function(response){
                            datatable.columns.adjust().draw(false);
                            $('#select_all').prop('checked', false);
                        }
                    });
                }
                break;
            case 3:
                //刪除
                $('.row_select').each(function(){
                    if($(this).prop('checked')){
                        id.push($(this).parents('td').find('.input-id').val());
                    }
                });
                if(id.length){
                    Swal.fire({
                        title: '您確定要永遠刪除項目？',
                        type: 'warning',
                        showCancelButton: true,
                        buttonsStyling: false,
                        confirmButtonText: '是！',
                        cancelButtonText: '取消',
                        customClass: {
                            confirmButton: 'btn btn-success btn-lg m-l-10',
                            cancelButton: 'btn btn-danger btn-lg'
                        },
                        reverseButtons: true,
                    }).then(function (data) {
                        if(data.value){
                            $.ajax({
                                url: config.path.action,
                                type: 'POST',
                                data: {
                                    type: type,
                                    id: id
                                },
                                error: function(xhr, textStatus, errorThrown){
                                    console.log(xhr.status+' '+errorThrown);
                                },
                                success: function(response){
                                    datatable.columns.adjust().draw(false);
                                    $('#select_all').prop('checked', false);
                                }
                            });
                        }
                    });
                }
                break;
        }
    });

    //全選checkbox
    $('#select_all').unbind('click');
    $('#select_all').click(function(){
        if($(this).prop('checked')){
            $('.row_select').prop('checked', true);
        }else{
            $('.row_select').prop('checked', false);
        }
    });

    //visible
    var visible_none = [], visible_all = [];
    for(var i=0; i<config.visible.length; i++){
        var column = datatable.column(config.visible[i].value);

        if(config.visible[i].selected==false){
            column.visible( false );
        }else{
            column.visible( true );
        }
    }

    $('.column-visible').change(function(){
        var val = $(this).val();
        var visible = [];
        for(var i=0; i<config.visible.length; i++){
            var value = config.visible[i].value;
            var column = datatable.column(value);

            if($.inArray(value.toString(), val)==-1){
                //not found
                column.visible(false);
            }else{
                column.visible(true);
                visible.push(value);
            }
        }

        //紀錄顯示與否
        $.ajax({
            url: config.path.columnVisible,
            type: 'POST',
            data: {
                data: visible,
            },
            error: function(xhr, textStatus, errorThrown){
                console.log(xhr.status+' '+errorThrown);
            },
            success: function(response){
                console.log(response);
            }
        });
    });

    $('.dataTables_length select').attr('data-style', 'btn-primary');
    $('select.column-visible, select.select-action, .dataTables_length select.form-control').selectpicker();
    $('.dataTables_length .bootstrap-select').removeClass('form-control-sm');
})
</script>