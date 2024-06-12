@extends('layouts.appDashboard')

@section('content')
    <div class="container-fluid mt-3">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">EDIT DATA PERAMALAN</h4>
                        <div class="basic-form">
                            <form method="POST" action="{{ route('peramalans.update', $peramalan->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="tanggal" id="mdate"
                                            placeholder="Tanggal" value="{{ $peramalan->tanggal }}" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="data_aktual">Data Aktual <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nilai_aktual" id="nilai_aktual"
                                            placeholder="Nilai IHSG hari ini" value="{{ $peramalan->nilai_aktual }}"
                                            required>
                                    </div>
                                </div>
                                <div class="form-group" align="right">
                                    <button type="submit" name="edit" class="btn btn-primary"
                                        style="margin-right: 5px;">Update</button>
                                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
