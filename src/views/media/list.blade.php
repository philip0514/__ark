@extends('ark::dashboard.app')

@section('sidebar')
{!! $sidebar !!}
@endsection

@section('content')
<div class="page">
        <div class="page-header">
            <h1 class="page-title">{{ $config['name'] }}</h1>
            <p class="page-description"></p>
        </div>
        <div class="page-content">

            <div class="container-fluid px-0">
                <div class="row">

                    <div class="col-12">
                        <div class="panel">
                            <div class="panel-body">

                                <div class="row">
                                    <div class="col-xxl-6 col-lg-8 xs-p-b-10 filter">
                                        <div class="input-group input-group-lg">
                                            <input type="text" class="form-control" id="search" name="search" placeholder="搜尋..." title="搜尋" value="{{ isset($config['search']['value']) ? $config['search']['value'] : null }}">
                                            <div class="input-group-append">
                                                <a href="javascript:;" class="btn btn-primary btn-search"><i class="fa fa-search"></i></a>
                                                @if($config['action']['search'])
                                                <a href="javascript:;" class="btn btn-primary btn-filter"><i class="fa fa-filter"></i></a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="filter-content">
                                            @if($config['action']['search'])
                                            @include(sprintf('ark.%s.search', $config['controller']))
                                            @endif
                                        </div>  
                                    </div>
                                    <div class="col-xxl-6 col-lg-4 md-p-t-10">
                                        @if($config['action']['create'])
                                            <form id="fileupload" action="{{ route('media.upload') }}" method="POST" enctype="multipart/form-data" class="fileupload-processing">
                                                <div class="row fileupload-buttonbar">
                                                    <div class="col-md-8 fileupload-progress fade">
                                                        <div class="progress progress-lg">
                                                            <div class="progress-bar progress-bar-striped active" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" role="progressbar">
                                                                <span></span>
                                                            </div>
                                                        </div>
                                                    </div>
                
                                                    <div class="col-md-4 text-right">
                                                        <a class="btn btn-primary btn-lg fileinput-button" href="javascript:;">
                                                            <i class="fas fa-plus"></i>
                                                            <span>上傳</span>
                                                            <input type="file" id="file" name="file" style="height:50px;" multiple>
                                                        </a>
                                                    </div>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                <div class="row p-t-10 p-b-10">
                                    <div class="col-md-12 filter-detail">
                                        <ul class="list-inline">
                                            <li><a href="javascript:;" class="btn btn-dark btn-filter-clear"><i class="fas fa-times"></i> 清除全部條件</a></li>
                                        </ul>
                                    </div>
                                </div>

                                <table id="datatable" class="table table-hover table-striped" width="100%">
                                    <thead>
                                        <tr>
                                            <th>{!! implode('</th><th>', $config['table_view']['thead']) !!}</th>
                                        </tr>
                                    </thead>
                                </table>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="{{ '/ark/css/datatables.css' }}">
@endsection

@section('js')
<script src="{{ '/ark/js/datatables.js' }}"></script>
<script>
var config = @json($config['datatable'], JSON_PRETTY_PRINT);
$(function() {
    Datatables.init();

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
            datatable.columns.adjust().draw(false);
        },
        fail: function(e, data){
            console.log('failed');
            console.log(e);
            console.log(data);
        }
    });
});
</script>
@endsection
