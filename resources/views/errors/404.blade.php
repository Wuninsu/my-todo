@extends('layouts.auth')

@section('content')

<div class="text-center">

    <div class="mb-4">

        <i class="bi bi-shield-lock fs-1 text-danger"></i>

    </div>

    <h1 class="fw-bold mb-3">

        403

    </h1>

    <p class="text-muted mb-4">

        You do not have permission
        to access this page.

    </p>

    <a
        href="{{ url('/') }}"
        class="btn app-btn-primary rounded-4">

        Go Back

    </a>

</div>

@endsection