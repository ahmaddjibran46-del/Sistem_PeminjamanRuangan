<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Peminjaman Ruangan</title>
<style>
body{font-family:Arial;max-width:1000px;margin:30px auto;padding:0 15px}
table{width:100%;border-collapse:collapse;margin:15px 0}
th,td{border:1px solid #ccc;padding:8px}
form{border:1px solid #ccc;padding:15px;margin-bottom:25px}
input,select,textarea{padding:8px;margin:5px 0 10px;width:100%;box-sizing:border-box}
button{padding:9px 15px}
.success{padding:10px;background:#e7f7e7}
.error{padding:10px;background:#fde8e8}
</style>
</head>
<body>

<h1>Peminjaman Ruangan</h1>

@if (session('pesan'))
<div class="{{ str_starts_with(session('pesan'), 'Berhasil') ? 'success' : 'error' }}">
    {{ session('pesan') }}
</div>
@endif

<h2>Jadwal yang sudah terpakai</h2>
<table>
<tr><th>Ruangan</th><th>Tanggal</th><th>Mulai</th><th>Selesai</th></tr>
@foreach ($jadwal as $row)
<tr>
    <td>{{ $row->ruangan->nama_ruangan ?? '-' }}</td>
    <td>{{ $row->tanggal }}</td>
    <td>{{ $row->jam_mulai }}</td>
    <td>{{ $row->jam_selesai }}</td>
</tr>
@endforeach
</table>

<h2>Ajukan Peminjaman</h2>
<form action="{{ route('peminjaman.store') }}" method="POST">
    @csrf
    <input type="hidden" name="id_mahasiswa" value="{{ $id_mahasiswa }}">

    <label>Ruangan</label>
    <select name="id_ruangan" required>
        <option value="">-- pilih ruangan --</option>
        @foreach ($ruangan as $r)
            <option value="{{ $r->id_ruangan }}">
                {{ $r->nama_ruangan }} (kapasitas {{ $r->kapasitas }})
            </option>
        @endforeach
    </select>

    <label>Tanggal</label>
    <input type="date" name="tanggal" required>

    <label>Jam mulai</label>
    <input type="time" name="jam_mulai" required>

    <label>Jam selesai</label>
    <input type="time" name="jam_selesai" required>

    <label>Alasan</label>
    <textarea name="alasan" required></textarea>

    <label>Jumlah peserta</label>
    <input type="number" name="jumlah_peserta" min="1" required>

    <button type="submit">Ajukan Peminjaman</button>
</form>

<p><a href="{{ route('admin.peminjaman') }}">Lihat halaman admin →</a></p>
</body>
</html>