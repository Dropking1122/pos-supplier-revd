<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder {
    public function run(): void {
        Setting::firstOrCreate([],['company_name'=>'Toko Makmur Jaya','company_address'=>'Jl. Raya No. 1, Jakarta','company_phone'=>'0812-3456-7890','invoice_footer'=>'Terima kasih telah berbelanja di Toko Makmur Jaya!']);
        $admin = User::firstOrCreate(['email'=>'admin@pos.com'],['name'=>'Admin','password'=>Hash::make('password'),'email_verified_at'=>now()]);
        $admin->update(['is_admin' => true]);
    }
}
