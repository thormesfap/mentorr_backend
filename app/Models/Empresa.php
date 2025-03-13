<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    public const PER_PAGE = 15;
    protected $fillable = ['nome'];
}
