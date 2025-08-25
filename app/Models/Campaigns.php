<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campaigns extends Model
{

    use HasFactory;
    protected $fillable = ['name', 'content', 'segment', 'budget', 'status', 'idempotency_key'];
}
