@extends('ark::Dashboard.dashboard.app')

@section('sidebar')
{!! $sidebar !!}
@endsection

@section('content')
<div class="page">
        <div class="page-header">
            <h1 class="page-title">
                @if (isset($rows1['id']) && isset($rows1['name']))
                    {{ $rows1['name'] }}
                @elseif (isset($rows1['id']) && !isset($rows1['name']))
                    {{ $config['name'] }} - 編輯
                @else
                    {{ $config['name'] }} - 新增
                @endif
            </h1>
            <p class="page-description"></p>
        </div>
        <div class="page-content">

            <div class="container-fluid px-0">
                <div class="row">
                    <div class="col-xl-6 col-12">
                        <div class="panel panel-bordered">
                            <div class="panel-heading">
                                <h3 class="panel-title">一般</h3>
                                <div class="panel-actions">
                                    <a class="panel-action icon wb-minus" aria-expanded="true" data-toggle="panel-collapse" aria-hidden="true"></a>
                                    <a class="panel-action icon wb-expand" data-toggle="panel-fullscreen" aria-hidden="true"></a>
                                </div>
                            </div>
                            <div class="panel-body">

                                <div class="form-group">
                                    <label class="form-control-label">
                                        收件人
                                    </label>
                                    <div class="h5">
                                        <a href="{{route('user.update', $rows1['user_id'])}}">{{ isset($rows1['user_name']) ? $rows1['user_name'] : null }} {{ isset($rows1['user_email']) ? '<'.$rows1['user_email'].'>' : null }}</a>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">
                                        標題
                                    </label>
                                    <div class="h5">
                                        {{ isset($rows1['name']) ? $rows1['name'] : null }} 
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">
                                        內容
                                    </label>
                                    <div class="h5">
                                        {!! isset($rows1['content']) ? $rows1['content'] : null !!}
                                    </div>
                                </div>

                                <div class="row">
                                    @if(isset($rows1['created_at']))
                                    <div class="form-group col-md-6">
                                        <label class="form-control-label">
                                            新增時間
                                        </label>
                                        <div>
                                            {{ $rows1['created_at'] }}
                                        </div>
                                    </div>
                                    @endif

                                    @if(isset($rows1['updated_at']))
                                    <div class="form-group col-md-6">
                                        <label class="form-control-label">
                                            更新時間
                                        </label>
                                        <div>
                                            {{ $rows1['updated_at'] }}
                                        </div>
                                    </div>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
</div>
@endsection