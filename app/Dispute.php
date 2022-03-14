<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class Dispute extends Model
{
    use HasApiTokens;
    protected $guarded = [];
    protected $table = 'dispute';
    protected $fillable = [
         'description', 'order_number',
    ];
}
