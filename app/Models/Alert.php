<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $guarded = [];

    public function quartier() { return $this->belongsTo(Quartier::class); }
}
