@extends('layouts.app')


@section('title', 'List Barang')


@section('content')
  <div class="page-header d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="mb-0">List Barang</h1>
    </div>
    <div>
      <a href="{{ route('admin.barang.create') }}" class="btn btn-primary">+ Tambah Barang</a>
    </div>
  </div>


  {{-- Flash messages --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif


  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif


  <div class="card">
    <div class="card-body p-3">
      <div class="mb-2 d-flex align-items-center justify-content-between">
        <div>
          <label class="me-3"><input type="checkbox" id="check_all"> Pilih semua (halaman)</label>
          <button id="clearSelection" type="button" class="btn btn-sm btn-secondary">Bersihkan pilihan</button>
        </div>


        <div class="d-flex align-items-center">
          <label class="me-2 mb-0">Start X</label>
          <input id="input_start_x" form="printForm" type="number" name="start_x" value="1" min="1" max="5" class="form-control form-control-sm me-3" style="width:90px;">


          <label class="me-2 mb-0">Start Y</label>
          <input id="input_start_y" form="printForm" type="number" name="start_y" value="1" min="1" max="8" class="form-control form-control-sm me-3" style="width:90px;">


          <form id="printForm" action="{{ route('admin.barang.printLabels') }}" method="POST" target="_blank" style="display:inline-block;">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm me-2">Cetak PDF</button>
          </form>
        </div>
      </div>


      <div class="table-responsive">
        <table id="table-barang" class="table table-striped table-hover mb-0">
          <thead>
            <tr>
              <th style="width:60px"></th> {{-- kolom checkbox --}}
              <th style="width:120px">ID</th>
              <th>Nama</th>
              <th style="width:140px">Harga</th>
              <th style="width:160px">Dibuat</th>
              <th class="text-center" style="width:160px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($barangs as $b)
              <tr>
                <td class="align-middle text-center">
                  <input type="checkbox" class="chk" value="{{ $b->id_barang }}" data-id="{{ $b->id_barang }}">
                </td>
                <td class="align-middle">{{ $b->id_barang }}</td>
                <td class="align-middle">{{ $b->nama }}</td>
                <td class="align-middle">{{ number_format($b->harga, 0, ',', '.') }}</td>
                <td class="align-middle">{{ $b->timestamp ? \Carbon\Carbon::parse($b->timestamp)->format('Y-m-d H:i') : '-' }}</td>
                <td class="text-center align-middle">
                  <a href="{{ route('admin.barang.edit', $b->id_barang) }}" class="btn btn-sm btn-outline-warning">
                    Edit
                  </a>


                  <form action="{{ route('admin.barang.delete', $b->id_barang) }}" method="POST" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                      class="btn btn-sm btn-outline-danger"
                      onclick="return confirm('Hapus barang {{ $b->nama }} ?')">
                      Hapus
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-4">Belum ada data</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>


      {{-- Jika kamu masih memakai server-side paginate (controller->paginate),
           pertahankan link paginasi. Jika pakai DataTables client-side, links bisa disembunyikan. --}}
      <div class="mt-3">
        {{ $barangs->links() }}
      </div>
    </div>
  </div>
@endsection




@push('script-page')
<script>


const STORAGE_KEY = 'selected_barangs';


// util: load/save array of ids
function loadSelected() {
    try {
        return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
    } catch(e) {
        return [];
    }
}
function saveSelected(arr) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(arr));
}


// update selection array (add/remove)
function updateSelection(id, checked) {
    let arr = loadSelected();
    const idx = arr.indexOf(id);
    if (checked && idx === -1) {
        arr.push(id);
    } else if (!checked && idx !== -1) {
        arr.splice(idx, 1);
    }
    saveSelected(arr);
}


// sync checkboxes in current DOM page
function syncCheckboxesInDOM() {
    const selected = loadSelected();
    document.querySelectorAll('.chk').forEach(ch => {
        ch.checked = selected.indexOf(ch.value) !== -1;
    });


    // update state of page-level check_all (checked if all visible checked)
    const visible = Array.from(document.querySelectorAll('.chk'));
    if (visible.length > 0) {
        const allChecked = visible.every(c => c.checked);
        document.getElementById('check_all').checked = allChecked;
    } else {
        document.getElementById('check_all').checked = false;
    }
}


document.addEventListener('DOMContentLoaded', function() {
    // init DataTable
    const table = $('#table-barang').DataTable({
        pageLength: 10,
        ordering: true,
        searching: true,
        lengthChange: true,
        // jaga supaya DataTable tidak menghancurkan event; kita pakai draw event untuk binding
        drawCallback: function(settings) {
            // setelah DataTable render, susun state checkbox pada halaman saat ini
            syncCheckboxesInDOM();


            // bind change listeners (remove duplicates first)
            document.querySelectorAll('.chk').forEach(ch => {
                ch.onchange = function() {
                    updateSelection(this.value, this.checked);
                    // update page-level check_all checkbox
                    const visible = Array.from(document.querySelectorAll('.chk'));
                    const allChecked = visible.length > 0 && visible.every(c => c.checked);
                    document.getElementById('check_all').checked = allChecked;
                };
            });
        }
    });


    // initial sync for first draw
    syncCheckboxesInDOM();


    // check_all (halaman)
    document.getElementById('check_all').addEventListener('change', function(e){
        const checked = e.target.checked;
        document.querySelectorAll('.chk').forEach(ch => {
            ch.checked = checked;
            updateSelection(ch.value, checked);
        });
    });


    // bersihkan pilihan
    document.getElementById('clearSelection').addEventListener('click', function(){
        localStorage.removeItem(STORAGE_KEY);
        // uncheck all checkboxes in DOM
        document.querySelectorAll('.chk').forEach(ch => ch.checked = false);
        document.getElementById('check_all').checked = false;
        alert('Pilihan barang telah dibersihkan.');
    });


    // submit form: inject selected[] hidden inputs
    document.getElementById('printForm').addEventListener('submit', function(e){
        const form = this;


        // remove previously injected inputs (safety)
        document.querySelectorAll('input[name="selected[]"].injected').forEach(i => i.remove());


        const selectedIds = loadSelected();
        if (!selectedIds || selectedIds.length === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 barang untuk dicetak.');
            return false;
        }


        // inject each id as hidden input
        selectedIds.forEach(id => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'selected[]';
            inp.value = id;
            inp.classList.add('injected');
            form.appendChild(inp);
        });


        // also ensure start_x/start_y are included: inputs already have form="printForm"
        // (they are outside form tag but bound via form attribute). If you prefer, include inside form.


        // allow submit to proceed (PDF opens in new tab due to target="_blank")
    });
});
</script>
@endpush
