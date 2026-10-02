<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Ruangan extends Model
{
    // created_at pada tabel ini bertipe INT (existing). Diisi manual dengan time(),
    // sedangkan updated_at tetap datetime dan dikelola Eloquent.
    const CREATED_AT = null;

    const STATUS_TERSEDIA = 'tersedia';
    const STATUS_TIDAK_TERSEDIA = 'tidak_tersedia';

    protected $table = 'ruangan';
    protected $primaryKey = 'id_ruangan';

    protected $fillable = ['nama_ruangan', 'deskripsi', 'kapasitas', 'fasilitas', 'foto', 'status', 'lokasi'];

    // Kolom NOT NULL tanpa default di database.
    protected $attributes = ['fasilitas' => '', 'foto' => ''];

    protected function casts(): array
    {
        return ['kapasitas' => 'integer', 'created_at' => 'integer'];
    }

    protected static function booted(): void
    {
        static::creating(fn (self $r) => $r->setAttribute('created_at', time()));
    }

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'id_ruangan', 'id_ruangan');
    }

    public function jadwal(): HasMany
    {
        return $this->hasMany(JadwalRuangan::class, 'id_ruangan', 'id_ruangan');
    }

    public function scopeTersedia(Builder $q): Builder
    {
        return $q->where('status', self::STATUS_TERSEDIA);
    }

    public function scopeCari(Builder $q, ?string $kata): Builder
    {
        if (! $kata) {
            return $q;
        }
        $like = '%'.addcslashes($kata, '%_\\').'%';

        return $q->where(fn ($w) => $w->where('nama_ruangan', 'like', $like)
            ->orWhere('fasilitas', 'like', $like)
            ->orWhere('lokasi', 'like', $like));
    }

    public function getIsTersediaAttribute(): bool
    {
        return $this->status === self::STATUS_TERSEDIA;
    }

    public function getKodeAttribute(): string
    {
        return 'RNG-'.str_pad((string) $this->id_ruangan, 4, '0', STR_PAD_LEFT);
    }

    /** @return string[] */
    public function getDaftarFasilitasAttribute(): array
    {
        return collect(preg_split('/[,\n]+/', (string) $this->fasilitas))
            ->map(fn ($f) => trim($f))->filter()->values()->all();
    }

    public function getFotoUrlAttribute(): ?string
{
    return $this->foto ? asset('storage/'.$this->foto) : null;
}

    public function getDiperbaruiAttribute(): ?string
    {
        // Data lama memiliki updated_at '0000-00-00'.
        return $this->updated_at && $this->updated_at->year > 1
            ? $this->updated_at->translatedFormat('d M Y, H:i') : null;
    }
}
