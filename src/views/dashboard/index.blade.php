@extends('ark::dashboard.app')

@section('sidebar')
{!! $sidebar !!}
@endsection

@section('content')
<div class="page">
    <div class="page-header">
        <h1 class="page-title">Hi, {{ $admin['name'] }}</h1>
    </div>

    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-shadow card-md">
                    <div class="card-header card-header-transparent py-20">
                        <p class="font-size-14 blue-grey-700 mb-0">title</p>
                    </div>
                    <div class="card-block p-20">
                        <p>content.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(function(){
})
</script>
@endsection