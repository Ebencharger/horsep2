<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'userId',
    'session',
    'ipaddress',
    'city',
    'device',
    'browser',
    'country',
    'created_at'
])]
class Loginsession extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $primaryKey = 'id';
    public $timestamps = false;
}
