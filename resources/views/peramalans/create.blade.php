<div class="basic-form">
    {{-- <form method="POST" action={{ route('peramalans.store') }}>
        @csrf
        <div class="form-row">
            <div class="form-group col-md-5">
                <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="tanggal" id="mdate" placeholder="Tanggal" required>
            </div>
            <div class="form-group col-md-5">
                <label for="data_aktual">Data Aktual <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="nilai_aktual" id="nilai_aktual"
                    placeholder="Nilai IHSG hari ini" required>
            </div>
            <div class="form-inline">
                <button type="submit" class="btn btn-warning btn-lg mt-2">Peramalan</button>
            </div>
        </div>
    </form> --}}
    <form method="POST" action={{ route('test-peramalan') }}>
        @csrf
        <div class="form-row">
            <div class="form-group col-md-10">
                <label for="data_aktual">Rentang Hari</label>
                <input type="number" class="form-control" name="bobot" id="bobot" placeholder="Rentang Hari"
                    required>
            </div>
            <div class="form-inline">
                <button type="submit" class="btn btn-warning btn-lg mt-2">Peramalan</button>
            </div>
        </div>
    </form>
</div>
