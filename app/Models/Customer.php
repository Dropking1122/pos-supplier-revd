<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Customer extends Model {
    protected $fillable = ['name', 'phone', 'address'];
    public function sales() { return $this->hasMany(Sale::class); }
    public function debts() { return $this->hasMany(Debt::class); }
}
