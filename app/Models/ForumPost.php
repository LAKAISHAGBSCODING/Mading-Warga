<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    protected $table = 'forum_posts';
    protected $fillable = ['user_id', 'label', 'judul', 'isi'];

    public function user() { return $this->belongsTo(User::class); }
    public function comments() { return $this->hasMany(ForumComment::class, 'forum_post_id'); }
}