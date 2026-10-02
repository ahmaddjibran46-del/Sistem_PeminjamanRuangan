<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeedbackRequest;
use App\Models\Feedback;
use App\Models\Ruangan;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function index()
    {
        return view('mahasiswa.feedback.index', [
            'daftar' => Feedback::with('ruangan')->where('id_mahasiswa', Auth::id())->latest('created_at')->paginate(8),
        ]);
    }

    public function create()
    {
        // Ruangan yang pernah dipinjam mahasiswa (disetujui / selesai).
        $ruangan = Ruangan::whereIn('id_ruangan', Auth::user()->peminjaman()
            ->whereIn('status', ['disetujui', 'selesai'])->select('id_ruangan'))
            ->orderBy('nama_ruangan')->get();

        return view('mahasiswa.feedback.create', ['ruangan' => $ruangan]);
    }

    public function store(StoreFeedbackRequest $request)
    {
        Feedback::create([
            'id_mahasiswa' => Auth::id(), // selalu dari user login
            'id_ruangan' => $request->id_ruangan,
            'isi_feedback' => $request->isi_feedback,
        ]);

        return redirect()->route('feedback.index')->with('success', 'Terima kasih, feedback Anda berhasil dikirim.');
    }
}
