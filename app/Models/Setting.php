<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Setting extends Model {
    protected $fillable = ['company_name','company_logo','company_address','company_phone','invoice_footer'];
    public static function getSettings(): self {
        return self::firstOrCreate([], ['company_name'=>'Toko Saya','company_address'=>'Alamat Toko','company_phone'=>'08xxxxxxxxx','invoice_footer'=>'Terima kasih telah berbelanja!']);
    }
}
