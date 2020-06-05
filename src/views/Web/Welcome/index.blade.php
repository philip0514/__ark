@extends('ark::Web.welcome.app')

@section('header')
{!! $header !!}
@endsection

@section('content')
{!! $html !!}
@endsection

@section('footer')
{!! $footer !!}
@endsection

@section('css')
<style>
{!! $css !!}
</style>
@endsection