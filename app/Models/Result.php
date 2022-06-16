<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $fillable = ['phone', 'code'];

    public function client(){
        return $this->belongsTo(Client::class, 'phone', 'phone');
    }
}