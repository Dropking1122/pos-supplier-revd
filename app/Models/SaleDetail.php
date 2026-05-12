<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SaleDetail extends Model {
    protected $fillable = ['sale_id','product_id','price_type','unit_price','quantity','stock_before','subtotal'];
    protected $casts = ['unit_price'=>'decimal:2','subtotal'=>'decimal:2'];
    public function sale() { return $this->belongsTo(Sale::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
