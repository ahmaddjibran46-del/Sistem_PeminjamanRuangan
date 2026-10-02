<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RuanganRequest;
use App\Models\Ruangan;
use App\Services\JadwalRuanganService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RuanganController extends Controller
{
    public function index(Request $request, JadwalRuanganService $jadwal)
    {
        $status = in_array($request->query('status'), ['tersedia', 'tidak_tersedia'], true) ? $request->query('status') : null;

        $ruangan = Ruangan::query()
            ->cari($request->query('q'))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderBy('nama_ruangan')->paginate(12)->withQueryString();

        $total = Ruangan::count();
        $tersedia = Ruangan::tersedia()->count();

        return view('admin.ruangan.index', [
            'ruangan' => $ruangan,
            'total' => $total,
            'tersedia' => $tersedia,
            'tidakTersedia' => $total - $tersedia,
            'dipakai' => $jadwal->pemakaianSekarang(),
        ]);
    }

    public function create()
    {
        Gate::authorize('manage', Ruangan::class);

        return view('admin.ruangan.form', ['ruangan' => new Ruangan(['status' => 'tersedia'])]);
    }

    public function store(RuanganRequest $request)
    {
        Gate::authorize('manage', Ruangan::class);

        $data = $request->safe()->except(['foto', 'hapus_foto']);
        $data['fasilitas'] = $data['fasilitas'] ?? '';
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('ruangan', 'public');
        }

        $ruangan = Ruangan::create($data);
        Log::info('Ruangan ditambahkan', ['ruangan' => $ruangan->id_ruangan, 'admin' => auth('admin')->id()]);

        return redirect()->route('admin.ruangan.index')->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function edit(Ruangan $ruangan)
    {
        Gate::authorize('manage', $ruangan);

        return view('admin.ruangan.form', ['ruangan' => $ruangan]);
    }

    public function update(RuanganRequest $request, Ruangan $ruangan)
    {
        Gate::authorize('manage', $ruangan);

        $data = $request->safe()->except(['foto', 'hapus_foto']);
        $data['fasilitas'] = $data['fasilitas'] ?? '';

        if ($request->hasFile('foto')) {
            $this->hapusFoto($ruangan);
            $data['foto'] = $request->file('foto')->store('ruangan', 'public');
        } elseif ($request->boolean('hapus_foto')) {
            $this->hapusFoto($ruangan);
            $data['foto'] = '';
        }

        $ruangan->update($data);
        Log::info('Ruangan diperbarui', ['ruangan' => $ruangan->id_ruangan, 'admin' => auth('admin')->id()]);

        return redirect()->route('admin.ruangan.index')->with('success', 'Perubahan ruangan berhasil disimpan.');
    }

    /** Menonaktifkan = status tidak_tersedia. Ruangan tidak dihapus agar riwayat tetap utuh (Rule 11). */
    public function status(Request $request, Ruangan $ruangan)
    {
        Gate::authorize('manage', $ruangan);

        $ruangan->update(['status' => $ruangan->is_tersedia ? Ruangan::STATUS_TIDAK_TERSEDIA : Ruangan::STATUS_TERSEDIA]);

        return back()->with('success', 'Status ruangan diperbarui.');
    }

    private function hapusFoto(Ruangan $ruangan): void
    {
        if ($ruangan->foto) {
            Storage::disk('public')->delete($ruangan->foto);
        }
    }
}
