<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        return $this->tampil($request, null);
    }

    public function show(Request $request, Feedback $feedback)
    {
        return $this->tampil($request, $feedback);
    }

    public function balas(Request $request, Feedback $feedback)
    {
        Gate::authorize('reply', $feedback);
        $data = $request->validate(['balasan_admin' => ['required', 'string', 'max:1000']]);

        $feedback->update($data + ['dibalas_at' => now(), 'dibaca_at' => $feedback->dibaca_at ?? now()]);

        return redirect()->route('admin.feedback.show', $feedback)->with('success', 'Tanggapan berhasil dikirim.');
    }

    private function tampil(Request $request, ?Feedback $dipilih)
    {
        $baru = $request->query('filter') === 'baru';
        $kata = trim((string) $request->query('q'));

        $daftar = Feedback::with(['mahasiswa', 'ruangan'])
            ->when($baru, fn ($q) => $q->whereNull('dibaca_at'))
            ->when($kata !== '', function ($q) use ($kata) {
                $like = '%'.addcslashes($kata, '%_\\').'%';
                $q->where(fn ($w) => $w->where('isi_feedback', 'like', $like)
                    ->orWhereHas('mahasiswa', fn ($m) => $m->where('nama', 'like', $like)));
            })
            ->latest('created_at')->paginate(10)->withQueryString();

        $dipilih ??= $daftar->first();

        if ($dipilih) {
            Gate::authorize('view', $dipilih);
            $dipilih->loadMissing(['mahasiswa', 'ruangan']);
            if (! $dipilih->dibaca_at) {
                $dipilih->update(['dibaca_at' => now()]);
            }
        }

        return view('admin.feedback.index', [
            'daftar' => $daftar,
            'dipilih' => $dipilih,
            'semua' => Feedback::count(),
            'jumlahBaru' => Feedback::whereNull('dibaca_at')->count(),
        ]);
    }
}
