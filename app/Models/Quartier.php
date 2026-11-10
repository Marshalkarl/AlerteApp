<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Quartier extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    public function subscribers() { return $this->hasMany(Subscriber::class); }
}
