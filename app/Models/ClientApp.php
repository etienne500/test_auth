<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientApp extends Model
{

    use HasFactory;
    
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['name', 'return_url', 'public_key', 'secret_key'];

    public function accessTokens()
    {
        return $this->hasMany('App\Models\AccessToken');
    }



}
