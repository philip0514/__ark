@extends('ark::Web.welcome.app')

@section('header')
{!! $html['header'] !!}
@endsection

@section('content')
{!! $html['body'] !!}
@endsection

@section('footer')
{!! $html['footer'] !!}
@endsection

@section('css')
<style>
{!! isset($html['css']) ? $html['css'] : '' !!}
</style>
@endsection