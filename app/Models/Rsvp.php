<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rsvp extends Model
{
    protected $table = 'rsvps';
    protected $fillable = ['acara_id', 'user_id', 'status'];

    public function user() { return $this->belongsTo(User::class); }
}