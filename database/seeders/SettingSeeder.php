<?php 
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Setting;
 
class SettingSeeder extends Seeder {
    public function run(): void {
        $settings = [
            ['key'=>'company_name',    'value'=>'My Store',          'group'=>'general'],
            ['key'=>'company_phone',   'value'=>'+966 50 000 0000',  'group'=>'general'],
            ['key'=>'company_email',   'value'=>'store@example.com', 'group'=>'general'],
            ['key'=>'company_address', 'value'=>'Riyadh, Saudi Arabia','group'=>'general'],
            ['key'=>'company_vat',     'value'=>'300000000000003',   'group'=>'general'],
            ['key'=>'currency',        'value'=>'SAR',               'group'=>'general'],
            ['key'=>'currency_symbol', 'value'=>'ريال',              'group'=>'general'],
            ['key'=>'tax_rate',        'value'=>'15',                'group'=>'invoice'],
            ['key'=>'invoice_prefix',  'value'=>'INV-',              'group'=>'invoice'],
            ['key'=>'quotation_prefix','value'=>'QT-',               'group'=>'invoice'],
            ['key'=>'invoice_note',    'value'=>'Thank you for your business!','group'=>'invoice'],
            ['key'=>'low_stock_alert', 'value'=>'5',                 'group'=>'stock'],
            ['key'=>'timezone',        'value'=>'Asia/Riyadh',       'group'=>'system'],
        ];
        foreach ($settings as $s) {
            Setting::updateOrCreate(['key' => $s['key']], $s);
        }
    }
}