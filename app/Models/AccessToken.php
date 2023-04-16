<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['client_id', 'user_id', 'expires_at'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function clientApp()
    {
        return $this->belongsTo('App\Models\ClientApp');
    }

}
