@extends('ark::Dashboard.dashboard.app')

@section('sidebar')
{!! $sidebar !!}
@endsection

@section('content')
<div class="page">
    <div class="page-header">
        <h1 class="page-title">Hi, {{ $admin['name'] }}</h1>
    </div>
</div>
@endsection

@section('js')
<script>
$(function(){
})
</script>
@endsection