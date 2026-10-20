<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    protected $table = 'pengumumans';

    protected $fillable = ['user_id', 'judul', 'isi', 'lampiran', 'is_pinned', 'pinned_at'];

    protected $casts = [
        'is_pinned' => 'boolean',
        'pinned_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dibacas()
    {
        return $this->hasMany(PengumumanDibaca::class);
    }

    public function pembacas()
    {
        return $this->belongsToMany(User::class, 'pengumuman_dibacas', 'pengumuman_id', 'user_id')
            ->withTimestamps();
    }
}
