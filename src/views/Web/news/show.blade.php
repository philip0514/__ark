@extends('ark::Web.welcome.app')

@section('header')
{!! $html['header'] !!}
@endsection

@section('content')
<section style="padding-top: 100px">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center">{{ $data['name'] }}</h1>
            </div>
        </div>
        <div class="row pt-5">
            {!! $data['content'] !!}
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