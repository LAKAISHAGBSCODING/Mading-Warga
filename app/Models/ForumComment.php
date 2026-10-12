<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumComment extends Model
{
    protected $table = 'forum_comments';
    protected $fillable = ['forum_post_id', 'user_id', 'isi'];

    public function user() { return $this->belongsTo(User::class); }
}