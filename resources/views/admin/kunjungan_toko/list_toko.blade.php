@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">List Toko</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">Input Titik Awal</div>
        <div class="card-body">
            <form action="{{ route('admin.kunjungan_toko.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Nama Toko</label>
                        <input type="text" name="nama_toko" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Barcode</label>
                        <input type="text" name="barcode" class="form-control" placeholder="kosong = auto generate">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Latitude</label>
                        <input type="text" name="latitude" id="latitude" class="form-control" readonly required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Longitude</label>
                        <input type="text" name="longitude" id="longitude" class="form-control" readonly required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Accuracy</label>
                        <input type="text" name="accuracy" id="accuracy" class="form-control" readonly required>
                    </div>
                </div>

                <button type="button" class="btn btn-primary" onclick="ambilLokasiAkurat()">Geoloc</button>
                <button type="submit" class="btn btn-success">Submit</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Daftar Toko</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Barcode</th>
                        <th>Nama Toko</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Accuracy</th>
                        <th>Print Barcode</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stores as $store)
                        <tr>
                            <td>{{ $store->barcode }}</td>
                            <td>{{ $store->nama_toko }}</td>
                            <td>{{ $store->latitude }}</td>
                            <td>{{ $store->longitude }}</td>
                            <td>{{ $store->accuracy }}</td>
                            <td>
                                <a href="{{ route('admin.kunjungan_toko.print', $store->barcode) }}" target="_blank" class="btn btn-sm btn-info">
                                    Cetak Barcode
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data toko</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function getAccuratePosition(targetAccuracy = 50, maxWait = 20000) {
  return new Promise((resolve, reject) => {
    let bestResult = null;
    const startTime = Date.now();

    const watchId = navigator.geolocation.watchPosition(
      (position) => {
        const acc = position.coords.accuracy;

        if (!bestResult || acc < bestResult.coords.accuracy) {
          bestResult = position;
        }

        if (acc <= targetAccuracy) {
          navigator.geolocation.clearWatch(watchId);
          resolve(bestResult);
        }

        if (Date.now() - startTime >= maxWait) {
          navigator.geolocation.clearWatch(watchId);
          if (bestResult) resolve(bestResult);
          else reject(new Error("Timeout, tidak dapat posisi"));
        }
      },
      (error) => reject(error),
      { enableHighAccuracy: true, maximumAge: 0, timeout: maxWait }
    );
  });
}

async function ambilLokasiAkurat() {
    try {
        const pos = await getAccuratePosition(50);
        document.getElementById('latitude').value = pos.coords.latitude;
        document.getElementById('longitude').value = pos.coords.longitude;
        document.getElementById('accuracy').value = pos.coords.accuracy;
    } catch (e) {
        alert('Gagal ambil lokasi: ' + e.message);
    }
}
</script>
@endsection