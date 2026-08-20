<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo 2 tài khoản khách hàng mặc định (không có Role Admin)
        User::create([
            'name' => 'Khách Hàng VIP',
            'email' => 'khachhang1@gmail.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Khách Hàng Mới',
            'email' => 'khachhang2@gmail.com',
            'password' => Hash::make('password'),
        ]);
    }
}