<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\PriceListType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Superadmin',
                'password' => Hash::make('password'),
                'role' => 'SUPERADMIN',
            ]
        );

        // Create default price list types
        PriceListType::firstOrCreate(
            ['type' => 'RETAIL'],
            [
                'name' => 'Retail Price',
                'description' => 'Standard retail pricing for walk-in customers',
                'created_by' => $superadmin->id,
            ]
        );

        PriceListType::firstOrCreate(
            ['type' => 'GROSIR'],
            [
                'name' => 'Wholesale Price',
                'description' => 'Wholesale pricing for bulk purchases',
                'created_by' => $superadmin->id,
            ]
        );

        PriceListType::firstOrCreate(
            ['type' => 'MEMBER'],
            [
                'name' => 'Member Price',
                'description' => 'Special pricing for registered members',
                'created_by' => $superadmin->id,
            ]
        );

        PriceListType::firstOrCreate(
            ['type' => 'RESELLER'],
            [
                'name' => 'Reseller Price',
                'description' => 'Pricing for authorized resellers',
                'created_by' => $superadmin->id,
            ]
        );
    }
}
