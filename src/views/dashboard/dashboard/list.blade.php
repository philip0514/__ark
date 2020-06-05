@extends('ark::Dashboard.dashboard.app')

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
                                    <div class="col-xxl-6 col-md-8 xs-p-b-10 filter">
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
                                            @include(sprintf('ark::Dashboard.%s.search', $config['controller']))
                                            @endif
                                        </div>  
                                    </div>
                                    <div class="col-xxl-6 col-md-4">
                                        <div class="btn-group float-right">
                                            {{--
                                            @if($setting['action']['import'])
                                            <a href="javascript:;" class="btn btn-info">
                                                <i class="fa fa-plus"></i> 匯入
                                            </a>
                                            @endif
                                            --}}
                                            @if($config['action']['create'])
                                            <a href="{{ $config['path']['create'] }}" class="btn btn-primary btn-lg">
                                                <i class="fa fa-plus"></i> 新增
                                            </a>
                                           @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row p-t-10 p-b-10">
                                    <div class="col-md-12 filter-detail">
                                        <ul class="list-inline">
                                            <li><a href="javascript:;" class="btn btn-dark btn-filter-clear"><i class="fas fa-times"></i> 清除全部條件</a></li>
                                            <!--
                                            <li><a href="javascript:;" class="btn btn-secondary btn-filter-delete">搜尋: test <i class="fa fa-times-circle"></i></a></li>
                                            -->
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
});
</script>
@endsection
