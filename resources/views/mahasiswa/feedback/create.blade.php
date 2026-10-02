<x-layouts.app title="Tulis Feedback">
    <h1 class="text-3xl font-extrabold text-slate-900">Tulis Feedback</h1>
    <p class="mt-1 text-sm text-slate-600">Bantu kami meningkatkan kualitas fasilitas ruangan kampus.</p>

    <form method="POST" action="{{ route('feedback.store') }}" x-data="{ loading: false }" @submit="loading = true" class="card mt-6 max-w-2xl space-y-5 p-6">
        @csrf
        <x-select name="id_ruangan" label="Ruangan" :options="$ruangan->pluck('nama_ruangan', 'id_ruangan')->all()" placeholder="Umum (tidak terkait ruangan tertentu)" />
        <x-textarea name="isi_feedback" label="Isi Feedback" rows="6" max="500" placeholder="Ceritakan pengalaman Anda..." required />
        <div class="flex justify-end gap-3">
            <a href="{{ route('feedback.index') }}" class="btn-ghost">Batal</a>
            <button class="btn-primary" :disabled="loading"><x-icon name="send" class="h-4 w-4" /> Kirim Feedback</button>
        </div>
    </form>
</x-layouts.app>
