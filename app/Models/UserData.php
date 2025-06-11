<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserData extends Model
{
    protected $table = 'user_data';  
    use HasFactory;
    
    protected $fillable = [
        'cpf',
        'cep',
        'email',
        'address_data',
        'name_origin_data',
        'cpf_status',
        'risk_level',
    ];
    
    protected $casts = [
        'address_data' => 'array',
        'name_origin_data' => 'array',
    ];
}