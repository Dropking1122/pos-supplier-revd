<?php
namespace App\Livewire\Settings;

use App\Models\Setting;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SettingIndex extends Component
{
    public $company_name = '', $company_address = '', $company_phone = '', $invoice_footer = '', $petugas = '';
    public $logoBase64 = null;
    public $logoFileName = null;

    public $showResetModal = false;
    public $resetConfirmText = '';
    public $resetDeleteSales     = true;
    public $resetDeleteDebts     = true;
    public $resetDeleteCustomers = true;
    public $resetDeleteProducts  = false;
    public $resetDeleteUsers     = false;
    public $resetDeleteSettings  = false;

    protected function rules()
    {
        return [
            'company_name'    => 'required|string|max:255',
            'company_address' => 'nullable|string',
            'company_phone'   => 'nullable|string|max:50',
            'invoice_footer'  => 'nullable|string',
            'petugas'         => 'nullable|string|max:100',
        ];
    }

    public function mount()
    {
        $s = Setting::getSettings();
        $this->company_name    = $s->company_name;
        $this->company_address = $s->company_address;
        $this->company_phone   = $s->company_phone;
        $this->invoice_footer  = $s->invoice_footer;
        $this->petugas         = $s->petugas;
    }

    public function setLogoBase64($dataUrl, $fileName)
    {
        $this->logoBase64   = $dataUrl;
        $this->logoFileName = $fileName;
    }

    public function clearLogo()
    {
        $this->logoBase64   = null;
        $this->logoFileName = null;
    }

    public function save()
    {
        $this->validate();

        $s    = Setting::getSettings();
        $data = [
            'company_name'    => $this->company_name,
            'company_address' => $this->company_address,
            'company_phone'   => $this->company_phone,
            'invoice_footer'  => $this->invoice_footer,
            'petugas'         => $this->petugas,
        ];

        if ($this->logoBase64) {
            if (preg_match('/^data:(image\/(\w+));base64,(.+)$/', $this->logoBase64, $matches)) {
                $mime    = $matches[1];
                $ext     = match($mime) {
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                    'image/gif'  => 'gif',
                    'image/webp' => 'webp',
                    default      => 'jpg',
                };
                $decoded = base64_decode($matches[3]);

                if ($decoded !== false && strlen($decoded) > 0) {
                    if ($s->company_logo) {
                        $oldPath = public_path($s->company_logo);
                        if (file_exists($oldPath)) @unlink($oldPath);
                    }

                    $dir = public_path('logos');
                    if (!is_dir($dir)) mkdir($dir, 0755, true);

                    $filename = Str::random(40) . '.' . $ext;
                    file_put_contents($dir . '/' . $filename, $decoded);
                    $data['company_logo'] = 'logos/' . $filename;
                }
            }

            $this->logoBase64   = null;
            $this->logoFileName = null;
        }

        $s->update($data);
        $this->dispatch('toast', type: 'success', message: 'Pengaturan toko berhasil disimpan.');
    }

    public function deleteLogo()
    {
        $s = Setting::getSettings();
        if ($s->company_logo) {
            $path = public_path($s->company_logo);
            if (file_exists($path)) @unlink($path);
            $s->update(['company_logo' => null]);
        }
        $this->logoBase64   = null;
        $this->logoFileName = null;
        $this->dispatch('toast', type: 'success', message: 'Logo berhasil dihapus.');
    }

    public function resetDatabase()
    {
        if (strtoupper(trim($this->resetConfirmText)) !== 'RESET') {
            $this->addError('resetConfirmText', 'Ketik RESET untuk konfirmasi.');
            return;
        }

        if (!$this->resetDeleteSales && !$this->resetDeleteDebts && !$this->resetDeleteCustomers
            && !$this->resetDeleteProducts && !$this->resetDeleteUsers && !$this->resetDeleteSettings) {
            $this->addError('resetConfirmText', 'Pilih minimal satu data yang ingin direset.');
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        if ($this->resetDeleteSales) {
            DB::table('sale_details')->truncate();
            DB::table('sales')->truncate();
        }
        if ($this->resetDeleteDebts) {
            DB::table('debt_payments')->truncate();
            DB::table('debts')->truncate();
        }
        if ($this->resetDeleteCustomers) {
            DB::table('customers')->truncate();
        }
        if ($this->resetDeleteProducts) {
            DB::table('products')->truncate();
        }
        if ($this->resetDeleteUsers) {
            DB::table('users')->where('id', '!=', auth()->id())->delete();
        }
        if ($this->resetDeleteSettings) {
            Setting::first()?->update([
                'company_name'    => 'Toko Saya',
                'company_address' => null,
                'company_phone'   => null,
                'company_logo'    => null,
                'invoice_footer'  => null,
                'petugas'         => null,
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->showResetModal   = false;
        $this->resetConfirmText = '';
        $this->resetDeleteSales = $this->resetDeleteDebts = $this->resetDeleteCustomers = true;
        $this->resetDeleteProducts = $this->resetDeleteUsers = $this->resetDeleteSettings = false;

        $this->dispatch('toast',
            type: 'success',
            title: 'Database Direset',
            message: 'Data yang dipilih telah dihapus.',
            duration: 6000
        );
    }

    public function render()
    {
        $setting = Setting::getSettings();
        return view('livewire.settings.setting-index', compact('setting'));
    }
}
