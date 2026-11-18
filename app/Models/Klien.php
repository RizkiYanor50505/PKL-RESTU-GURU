<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Klien extends Model
{
    use HasFactory;

    protected $table = 'adv_klien';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'nama_klien',
        'kategori_klien',
        'nama_pic',
        'no_wa',
        'email',
        'alamat',
        'npwp',
    ];

    /**
     * @return HasMany<KontrakSewa, $this>
     */
    public function kontrakSewas(): HasMany
    {
        return $this->hasMany(KontrakSewa::class, 'klien_id');
    }
}
