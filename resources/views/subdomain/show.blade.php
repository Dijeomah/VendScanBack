@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Welcome to {{ $vendor->name }}'s page!</h1>
        <p>This is the subdomain page for {{ $vendor->name }}.</p>
    </div>
@endsection
