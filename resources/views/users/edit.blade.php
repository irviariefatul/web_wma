@extends('layouts.appDashboard')

@section('content')
    <div class="container-fluid mt-3">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">EDIT PROFIL USER</h4>
                        <form class="forms-sample" method="POST" action={{ route('users.update', $user->id) }}>
                            <p class="card-description">
                                Edit Profil User
                            </p>
                            @csrf
                            @method('PUT')
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="name">Name <span
                                        class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" value="{{ $user->name }}" id="name"
                                        placeholder="Enter a name.." required>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="email">Email address <span
                                        class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        name="email" value="{{ $user->email }}" id="email"
                                        placeholder="Your valid email.." required>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="username">Username <span
                                        class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                                        name="username" value="{{ $user->username }}" id="username"
                                        placeholder="Enter a username.." required>
                                    @error('username')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="password">Password <span
                                        class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        name="password" id="password" placeholder="Choose a safe one.." required>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="role" class="col-lg-4 col-form-label" style="display: none;">Role</label>
                                <div class="col-lg-6">
                                    <select class="form-control" name="role" id="role" style="display: none;"
                                        required>
                                        <option value="admin" @if ($user->role === 'admin') selected @endif>Admin
                                        </option>
                                        <option value="user" @if ($user->role === 'user') selected @endif>User
                                        </option>
                                    </select>

                                    <input type="hidden" name="role" value="admin">

                                    @error('role')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-8 ml-auto">
                                    <button type="submit" name="edit"
                                        class="btn btn-primary align-self-center">Update</button>
                                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
