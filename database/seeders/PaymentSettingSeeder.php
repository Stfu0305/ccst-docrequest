<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentSetting;

class PaymentSettingSeeder extends Seeder
{
    public function run(): void
    {
        // updateOrCreate checks if a row with that `method` already exists.
        // If yes → updates it. If no → creates it.
        // This is safer than truncate() here because an admin may have updated
        // their real account numbers — we don't want to wipe those.

        // GCash
        PaymentSetting::updateOrCreate(
            ['method' => 'gcash'],
            [
                'account_name'   => 'CCST Registrar',
                'account_number' => '09XX-XXX-XXXX',
                'bank_name'      => null,
                'branch'         => null,
                'extra_info'     => null,
                'is_active'      => true,
            ]
        );

        // BDO
        PaymentSetting::updateOrCreate(
            ['method' => 'bdo'],
            [
                'account_name'   => 'CCST Registrar',
                'account_number' => 'XXXX-XXXX-XXXX',
                'bank_name'      => 'BDO',
                'branch'         => null,
                'extra_info'     => null,
                'is_active'      => true,
            ]
        );

        // BPI — inactive by default (cashier can activate from settings page)
        PaymentSetting::updateOrCreate(
            ['method' => 'bpi'],
            [
                'account_name'   => 'CCST Registrar',
                'account_number' => 'XXXX-XXXX-XXXX',
                'bank_name'      => 'BPI',
                'branch'         => null,
                'extra_info'     => null,
                'is_active'      => false,
            ]
        );

        // Cash — always active, cannot be deactivated
        PaymentSetting::updateOrCreate(
            ['method' => 'cash'],
            [
                'account_name'   => '',
                'account_number' => '',
                'bank_name'      => null,
                'branch'         => null,
                'extra_info'     => 'SNS Building, Ground Floor — Mon to Fri, 8:00 AM to 5:00 PM',
                'is_active'      => true,
            ]
        );
    }
}
