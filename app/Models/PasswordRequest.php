<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordRequest extends Model
{
    use HasFactory;
    
    // Sesuaikan dengan konfigurasi mass assignment Anda
    protected $guarded = ['id']; 
}