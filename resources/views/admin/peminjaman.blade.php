<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Admin - Pengajuan Peminjaman</title>
<style>
body{font-family:Arial;max-width:1200px;margin:30px auto;padding:0 15px}
table{width:100%;border-collapse:collapse}
th,td{border:1px solid #ccc;padding:8px;vertical-align:top}
.success{padding:10px;background:#e7f7e7}
.error{padding:10px;background:#fde8e8}
button{padding:6px 10px}
input[type=text]{padding:5px;width:150px}
</style>
</head>
<body>

<h1>Pengajuan Peminjaman</h1>

@if (session('pesan'))
<div class="{{ str_starts_with(session('pesan'), 'Berhasil') ? 'success' : 'error' }}">
    {{ session('pesan') }}
</div>
@endif

@if ($errors->any())
<div class="error">{{ $errors->first() }}</div>
@endif

<table>
<tr>
    <th>Mahasiswa</th><th>Ruangan</th><th>Waktu</th><th>Peserta</th>
    <th>Alasan</th><th>Status</th><th>Catatan admin</th><th>Aksi</th>
</tr>

@foreach ($pengajuan as $p)
<tr>
    <td>{{ $p->nama_pengaju }}<br>NIM: {{ $p->mahasiswa->nim ?? '-' }}</td>
    <td>{{ $p->ruangan->nama_ruangan ?? '-' }}</td>
    <td>{{ $p->tanggal_mulai }}<br>sampai<br>{{ $p->tanggal_selesai }}</td>
    <td>{{ (int) $p->jumlah_peserta }}</td>
    <td>{!! nl2br(e($p->alasan)) !!}</td>
    <td>{{ $p->status }}</td>
    <td>{{ $p->catatan_admin }}</td>
    <td>
        @if ($p->status === 'menunggu')
            <form action="{{ route('admin.peminjaman.approve', $p->id_peminjaman) }}" method="POST" style="display:inline">
                @csrf
                <button type="submit">Setujui</button>
            </form>

            <form action="{{ route('admin.peminjaman.reject', $p->id_peminjaman) }}" method="POST" style="display:block;margin-top:6px">
                @csrf
                <input type="text" name="catatan_admin" placeholder="Catatan (opsional)">
                <button type="submit">Tolak</button>
            </form>
        @else
            -
        @endif
    </td>
</tr>
@endforeach
</table>

<p><a href="{{ route('jadwal') }}">← Kembali ke peminjaman</a></p>
</body>
</html>