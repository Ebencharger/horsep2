<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'userId',
    'reason',
    'created_at'
])]
class Loginattempt extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $primaryKey = 'id';
    public $timestamps = false;
}
