<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiSetting extends Model
{
    protected $fillable = [
        'provider_name', 
        'api_url', 
        'api_key', 
        'header_name'
    ];
}
