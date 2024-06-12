@extends('layouts.appDashboard')

@section('content')
    <div class="container-fluid mt-3">
        <div class="row justify-content-center">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between">
                            <h4 class="card-title mb-3">Hello, {{ $user->username }}</h4>
                        </div>
                        <div class="row">
                            <table class="table table-responsive">
                                <tr>
                                    <th>Name</th>
                                    <th>:</th>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <th>:</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Username</th>
                                    <th>:</th>
                                    <td>{{ $user->username }}</td>
                                </tr>
                                <tr>
                                    <th>Role</th>
                                    <th>:</th>
                                    <td>{{ $user->role }}</td>
                                </tr>
                                <tr>
                                    <th>Create At</th>
                                    <th>:</th>
                                    <td>{{ $user->created_at }}</td>
                                </tr>
                            </table>
                        </div>
                        <a class="nav-link" href="{{ url()->previous() }}" align="right">
                            <button type="button" class="btn btn-secondary">Back</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
