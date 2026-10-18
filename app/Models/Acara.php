<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Acara extends Model
{
    protected $table = 'acaras';
    protected $fillable = ['user_id', 'judul', 'deskripsi', 'tanggal', 'jam', 'lokasi', 'status', 'alasan_penolakan'];
    protected $casts = ['tanggal' => 'date'];

    public function pengaju() { return $this->belongsTo(User::class, 'user_id'); }
    public function rsvps() { return $this->hasMany(Rsvp::class); }
}