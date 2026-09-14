<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class BusinessSettingIndex extends Component
{
    public string $activeTab = 'business';
    public string $previewPaperSize = '58';

    // 1. Identitas Bisnis
    public string $company_name = '';
    public string $company_tagline = '';
    public string $company_phone = '';
    public string $company_email = '';
    public string $company_address = '';
    public string $company_city = '';
    public string $company_hours = '';

    // 2. Format Struk Thermal
    public string $receipt_header_title = '';
    public bool $receipt_show_logo = true;
    public bool $receipt_show_address = true;
    public bool $receipt_show_phone = true;
    public string $receipt_default_paper = '58';
    public bool $receipt_show_cashier = true;
    public bool $receipt_show_customer = true;
    public string $receipt_footer_thanks = '';
    public string $receipt_footer_notice = '';
    public bool $receipt_show_timestamp = true;

    // 3. Invoice & Rekening
    public string $invoice_bank_name = '';
    public string $invoice_bank_account = '';
    public string $invoice_bank_holder = '';
    public string $invoice_terms = '';
    public string $invoice_footer_note = '';

    // 4. Aturan Order & Margin (Dipindahkan dari promo)
    public int|string|null $min_order_total = 0;
    public int|string|null $min_order_qty = 0;
    public int|string|null $min_margin_rp = 500;
    public int|string|null $min_margin_pct = 2;

    public function mount(): void
    {
        $this->ensureOwner();

        // Identitas Bisnis
        $this->company_name = (string) Setting::get('company_name', 'CV Ari Gita Grosir');
        $this->company_tagline = (string) Setting::get('company_tagline', 'Distributor Minuman Grosir');
        $this->company_phone = (string) Setting::get('company_phone', '081234567890');
        $this->company_email = (string) Setting::get('company_email', '');
        $this->company_address = (string) Setting::get('company_address', 'Jl. Cargo Permai No. 88, Denpasar');
        $this->company_city = (string) Setting::get('company_city', 'Denpasar, Bali');
        $this->company_hours = (string) Setting::get('company_hours', 'Senin - Sabtu: 08:00 - 17:00');

        // Struk Thermal
        $this->receipt_header_title = (string) Setting::get('receipt_header_title', 'STRUK TRANSAKSI PENJUALAN');
        $this->receipt_show_logo = (bool) Setting::get('receipt_show_logo', true);
        $this->receipt_show_address = (bool) Setting::get('receipt_show_address', true);
        $this->receipt_show_phone = (bool) Setting::get('receipt_show_phone', true);
        $this->receipt_default_paper = (string) Setting::get('receipt_default_paper', '58');
        $this->receipt_show_cashier = (bool) Setting::get('receipt_show_cashier', true);
        $this->receipt_show_customer = (bool) Setting::get('receipt_show_customer', true);
        $this->receipt_footer_thanks = (string) Setting::get('receipt_footer_thanks', 'Terima kasih atas pembelian Anda.');
        $this->receipt_footer_notice = (string) Setting::get('receipt_footer_notice', 'Barang yang sudah dibeli harap diperiksa kembali.');
        $this->receipt_show_timestamp = (bool) Setting::get('receipt_show_timestamp', true);
        $this->previewPaperSize = $this->receipt_default_paper;

        // Invoice & Rekening
        $this->invoice_bank_name = (string) Setting::get('invoice_bank_name', 'BCA');
        $this->invoice_bank_account = (string) Setting::get('invoice_bank_account', '');
        $this->invoice_bank_holder = (string) Setting::get('invoice_bank_holder', 'CV Ari Gita Grosir');
        $this->invoice_terms = (string) Setting::get('invoice_terms', 'Pembayaran transfer wajib menyertakan nomor invoice pada berita transfer.');
        $this->invoice_footer_note = (string) Setting::get('invoice_footer_note', 'Dokumen invoice ini sah dan diproses secara terkomputerisasi.');

        // Aturan Order & Margin
        $this->min_order_total = (int) Setting::get('min_order_total', 0);
        $this->min_order_qty = (int) Setting::get('min_order_qty', 0);
        $this->min_margin_rp = (int) Setting::get('min_margin_rp', 500);
        $this->min_margin_pct = (float) Setting::get('min_margin_pct', 2);
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function setPreviewPaper(string $size): void
    {
        $this->previewPaperSize = in_array($size, ['58', '80'], true) ? $size : '58';
    }

    protected function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:150'],
            'company_tagline' => ['nullable', 'string', 'max:255'],
            'company_phone' => ['nullable', 'string', 'max:50'],
            'company_email' => ['nullable', 'email', 'max:100'],
            'company_address' => ['nullable', 'string', 'max:500'],
            'company_city' => ['nullable', 'string', 'max:100'],
            'company_hours' => ['nullable', 'string', 'max:150'],

            'receipt_header_title' => ['required', 'string', 'max:100'],
            'receipt_show_logo' => ['boolean'],
            'receipt_show_address' => ['boolean'],
            'receipt_show_phone' => ['boolean'],
            'receipt_default_paper' => ['required', 'in:58,80'],
            'receipt_show_cashier' => ['boolean'],
            'receipt_show_customer' => ['boolean'],
            'receipt_footer_thanks' => ['nullable', 'string', 'max:255'],
            'receipt_footer_notice' => ['nullable', 'string', 'max:500'],
            'receipt_show_timestamp' => ['boolean'],

            'invoice_bank_name' => ['nullable', 'string', 'max:50'],
            'invoice_bank_account' => ['nullable', 'string', 'max:50'],
            'invoice_bank_holder' => ['nullable', 'string', 'max:150'],
            'invoice_terms' => ['nullable', 'string', 'max:500'],
            'invoice_footer_note' => ['nullable', 'string', 'max:500'],

            'min_order_total' => ['required', 'integer', 'min:0'],
            'min_order_qty' => ['required', 'integer', 'min:0'],
            'min_margin_rp' => ['required', 'integer', 'min:0'],
            'min_margin_pct' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function save(): void
    {
        $this->ensureOwner();

        $validated = $this->validate();

        // 1. Identitas Bisnis
        Setting::set('company_name', $validated['company_name']);
        Setting::set('company_tagline', $validated['company_tagline'] ?? '');
        Setting::set('company_phone', $validated['company_phone'] ?? '');
        Setting::set('company_email', $validated['company_email'] ?? '');
        Setting::set('company_address', $validated['company_address'] ?? '');
        Setting::set('company_city', $validated['company_city'] ?? '');
        Setting::set('company_hours', $validated['company_hours'] ?? '');

        // 2. Format Struk Thermal
        Setting::set('receipt_header_title', $validated['receipt_header_title']);
        Setting::set('receipt_show_logo', $validated['receipt_show_logo'] ? '1' : '0');
        Setting::set('receipt_show_address', $validated['receipt_show_address'] ? '1' : '0');
        Setting::set('receipt_show_phone', $validated['receipt_show_phone'] ? '1' : '0');
        Setting::set('receipt_default_paper', $validated['receipt_default_paper']);
        Setting::set('receipt_show_cashier', $validated['receipt_show_cashier'] ? '1' : '0');
        Setting::set('receipt_show_customer', $validated['receipt_show_customer'] ? '1' : '0');
        Setting::set('receipt_footer_thanks', $validated['receipt_footer_thanks'] ?? '');
        Setting::set('receipt_footer_notice', $validated['receipt_footer_notice'] ?? '');
        Setting::set('receipt_show_timestamp', $validated['receipt_show_timestamp'] ? '1' : '0');

        // 3. Invoice & Rekening
        Setting::set('invoice_bank_name', $validated['invoice_bank_name'] ?? '');
        Setting::set('invoice_bank_account', $validated['invoice_bank_account'] ?? '');
        Setting::set('invoice_bank_holder', $validated['invoice_bank_holder'] ?? '');
        Setting::set('invoice_terms', $validated['invoice_terms'] ?? '');
        Setting::set('invoice_footer_note', $validated['invoice_footer_note'] ?? '');

        // 4. Aturan Order & Margin
        Setting::set('min_order_total', (int) $validated['min_order_total']);
        Setting::set('min_order_qty', (int) $validated['min_order_qty']);
        Setting::set('min_margin_rp', (int) $validated['min_margin_rp']);
        Setting::set('min_margin_pct', (float) $validated['min_margin_pct']);

        ActivityLogger::log(
            'business_settings.updated',
            'Pengaturan bisnis dan layout struk berhasil diperbarui oleh Owner.',
            null,
            [
                'updated_by' => Auth::id(),
                'company_name' => $validated['company_name'],
                'receipt_paper' => $validated['receipt_default_paper'],
            ]
        );

        Session::flash('success', 'Pengaturan bisnis & layout struk berhasil disimpan.');
    }

    private function ensureOwner(): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isOwner()) {
            abort(403, 'Akses khusus untuk Owner.');
        }
    }

    public function render()
    {
        return view('livewire.settings.business-setting-index');
    }
}
