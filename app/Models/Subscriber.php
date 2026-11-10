<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $guarded = [];
    protected $casts = ['verified_at' => 'datetime'];

    public function quartier() { return $this->belongsTo(Quartier::class); }
}
