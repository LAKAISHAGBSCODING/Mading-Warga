<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lapak extends Model
{
    protected $table = 'lapaks';
    protected $fillable = ['user_id', 'nama', 'harga', 'deskripsi', 'foto'];

    public function user() { return $this->belongsTo(User::class); }
}