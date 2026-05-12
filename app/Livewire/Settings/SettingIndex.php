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
    public $showResetModal = false, $resetConfirmText = '', $resetKeepProducts = true;

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
            // Parse the base64 data URL: data:image/png;base64,XXXX
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
                    // Delete old logo
                    if ($s->company_logo) {
                        $oldPath = public_path($s->company_logo);
                        if (file_exists($oldPath)) {
                            @unlink($oldPath);
                        }
                    }

                    $dir = public_path('logos');
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }

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
            if (file_exists($path)) {
                @unlink($path);
            }
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

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('debt_payments')->truncate();
        DB::table('debts')->truncate();
        DB::table('sale_details')->truncate();
        DB::table('sales')->truncate();
        DB::table('customers')->truncate();
        if (!$this->resetKeepProducts) {
            DB::table('products')->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->showResetModal  = false;
        $this->resetConfirmText = '';

        $this->dispatch('toast',
            type: 'success',
            title: 'Database Direset',
            message: 'Semua data transaksi & customer telah dihapus. Akun & pengaturan tetap aman.',
            duration: 6000
        );
    }

    public function render()
    {
        $setting = Setting::getSettings();
        return view('livewire.settings.setting-index', compact('setting'));
    }
}
