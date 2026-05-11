<?php
namespace App\Livewire\Settings;
use App\Models\Setting;
use Livewire\Component;
use Livewire\WithFileUploads;
class SettingIndex extends Component {
    use WithFileUploads;
    public $company_name = '', $company_address = '', $company_phone = '', $invoice_footer = '', $petugas = '';
    public $logo;
    protected $rules = [
        'company_name'   => 'required|string|max:255',
        'company_address'=> 'nullable|string',
        'company_phone'  => 'nullable|string|max:50',
        'invoice_footer' => 'nullable|string',
        'petugas'        => 'nullable|string|max:100',
        'logo'           => 'nullable|image|max:2048',
    ];
    public function mount() {
        $s = Setting::getSettings();
        $this->company_name    = $s->company_name;
        $this->company_address = $s->company_address;
        $this->company_phone   = $s->company_phone;
        $this->invoice_footer  = $s->invoice_footer;
        $this->petugas         = $s->petugas;
    }
    public function save() {
        $this->validate();
        $s = Setting::getSettings();
        $data = [
            'company_name'   => $this->company_name,
            'company_address'=> $this->company_address,
            'company_phone'  => $this->company_phone,
            'invoice_footer' => $this->invoice_footer,
            'petugas'        => $this->petugas,
        ];
        if ($this->logo) { $data['company_logo'] = $this->logo->store('logos','public'); }
        $s->update($data);
        session()->flash('message','Pengaturan berhasil disimpan!');
    }
    public function render() {
        $setting = Setting::getSettings();
        return view('livewire.settings.setting-index', compact('setting'));
    }
}
