@extends('ark::Web.welcome.app')

@section('header')
{!! $html['header'] !!}
@endsection

@section('content')
<section style="padding-top: 100px">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center">最新消息</h1>
            </div>
        </div>
        <div class="row pt-5">
            @for($i=0; $i<sizeof($data); $i++)
            <div class="col-md-4">
                <div class="card mb-4 shadow-sm">
                    <img class="img-fluid card-img-top" src="{{ $data[$i]['media']['square'] }}" />
                    <div class="card-body">
                        <h5 class="card-title">{{ $data[$i]['name'] }}</h5>
                        <p class="card-text">{{ $data[$i]['description'] }}</p>
                        <small class="text-muted">{{ date('Y-m-d', $data[$i]['news_time']) }}</small>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>
@endsection

@section('footer')
{!! $html['footer'] !!}
@endsection

@section('css')
<style>
{!! isset($html['css']) ? $html['css'] : '' !!}
</style>
@endsection