<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exceptional extends Model
{
    use HasFactory;

    public const STATUS_NEW = 'NEW';

    protected $fillable = ['word','status'];

}
