<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Audience extends Model
{
    use HasFactory;

    protected $fillable = ['email', 'segment', 'idempotency_key', 'last_campaign_id', 'last_touch_at'];
}
