<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Sale extends Model {
    protected $fillable = ['invoice_number','customer_id','user_id','total_amount','amount_paid','payment_type','status','due_date','notes'];
    protected $casts = ['due_date'=>'date','total_amount'=>'decimal:2','amount_paid'=>'decimal:2'];
    public function customer() { return $this->belongsTo(Customer::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function details() { return $this->hasMany(SaleDetail::class); }
    public function debt() { return $this->hasOne(Debt::class); }
}
