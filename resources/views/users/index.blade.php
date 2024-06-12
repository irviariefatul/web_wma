@extends('layouts.appDashboard')

@section('content')
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <h4 class="card-title">Users</h4>
                        <div class="table-responsive">
                            <a href="/users/create" class="btn mb-1 btn-primary">Add User <span class="btn-icon-right"><i
                                        class="fa fa-user-plus"></i></span>
                            </a>
                            <table class="table table-striped table-bordered zero-configuration">
                                <thead>
                                    <tr style="text-align: center;">
                                        <th>
                                            No
                                        </th>
                                        <th>
                                            Name
                                        </th>
                                        <th>
                                            Email
                                        </th>
                                        <th>
                                            Username
                                        </th>
                                        <th>
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 0; @endphp
                                    @foreach ($user as $s)
                                        {{-- @foreach (\App\Models\User::orderBy('created_at', 'desc')->get() as $s) --}}
                                        <tr>
                                            <td style="text-align: center;">{{ ++$no }}</td>
                                            <td>{{ $s->name }}</td>
                                            <td>{{ $s->email }}</td>
                                            <td>{{ $s->username }}</td>
                                            <td>
                                                <form action="/users/{{ $s->id }}" method="POST">
                                                    @method('GET')
                                                    <a href="/users/{{ $s->id }}" class="btn mb-1 btn-warning">
                                                        View <span class="btn-icon-right"><i
                                                                class="fa fa-search"></i></span></a>
                                                    <a href="/users/{{ $s->id }}/edit"
                                                        class="btn mb-1 btn-info">Edit<span class="btn-icon-right"><i
                                                                class="fa fa-edit"></i></span></a>
                                                    @method('DELETE')
                                                    @csrf
                                                    <button type="submit" name="delete" class="btn mb-1 btn-danger">
                                                        Delete<span class="btn-icon-right"><i
                                                                class="fa fa-trash"></i></span></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
