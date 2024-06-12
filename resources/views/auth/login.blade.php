@extends('layouts.appLogin')

@section('content')
    <div class="d-flex">
        <div class="w-100">
            <h3 class="mb-4">Login</h3>
        </div>
    </div>
    <form method="POST" action="{{ route('login') }}" class="signin-form">
        @csrf

        <div class="form-group mb-3">
            <label for="username" class="label">Username</label>
            <input id="username" type="username" class="form-control @error('username') is-invalid @enderror"
                name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>

            @error('username')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="password" class="label">Password</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                name="password" required autocomplete="current-password">

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <button type="submit" class="form-control btn btn-primary rounded submit px-3">
                Login
            </button>
        </div>
    </form>
    <p class="text-center">Want to Go <a href="{{ url()->previous() }}">Back</a>
    </p>
@endsection
