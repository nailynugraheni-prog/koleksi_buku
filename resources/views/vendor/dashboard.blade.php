@extends('layouts.app1')

@section('title', 'Dashboard Vendor')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Dashboard Vendor</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
</div>
@endsection