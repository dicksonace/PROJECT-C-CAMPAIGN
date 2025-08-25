<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class messages extends Model
{
    use HasFactory;
    protected $fillable = ['audience_id', 'campaign_id', 'status', 'content', 'delivered_at'];
}
