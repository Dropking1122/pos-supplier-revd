<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Debt extends Model {
    protected $fillable = ['customer_id','sale_id','total_hutang','total_bayar','sisa_hutang','jatuh_tempo','status'];
    protected $casts = ['jatuh_tempo'=>'date','total_hutang'=>'decimal:2','total_bayar'=>'decimal:2','sisa_hutang'=>'decimal:2'];
    public function customer() { return $this->belongsTo(Customer::class); }
    public function sale() { return $this->belongsTo(Sale::class); }
    public function payments() { return $this->hasMany(DebtPayment::class); }
}
