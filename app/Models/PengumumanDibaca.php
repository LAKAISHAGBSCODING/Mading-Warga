<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PengumumanDibaca extends Model
{
    use HasUuids;

    protected $table = 'pengumuman_dibacas';

    protected $fillable = [
        'pengumuman_id',
        'user_id',
    ];

    public function pengumuman()
    {
        return $this->belongsTo(Pengumuman::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
