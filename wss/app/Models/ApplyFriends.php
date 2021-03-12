<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplyFriends extends Model
{
    protected $table = "friends";
    public $timestamps = false;
    public $primaryKey = "id";
    public $fillable = ["user_id", "friend_id","state"];

    public function friendInfo()
    {
        return $this->hasOne("\App\Models\User", "id", "user_id");
    }
}
