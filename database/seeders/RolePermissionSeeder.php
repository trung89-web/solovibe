<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Xóa cache quyền cũ của Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Tạo các Permissions (Quyền hạn)
        $permissions = [
            'category.manage',
            'product.manage',
            'user.manage'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // 3. Tạo Role và gán Quyền
        // Role: Content Staff (Chỉ quản lý danh mục và sản phẩm)
        $staffRole = Role::create(['name' => 'Content Staff']);
        $staffRole->givePermissionTo(['category.manage', 'product.manage']);

        // Role: Super Admin (Nắm mọi quyền)
        $adminRole = Role::create(['name' => 'Super Admin']);
        $adminRole->givePermissionTo(Permission::all());

        // 4. Tạo tài khoản Super Admin mặc định
        $adminUser = User::create([
            'name' => 'Quản trị viên',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'), // Mật khẩu là: password
        ]);
        
        // Gán chức danh Super Admin cho tài khoản này
        $adminUser->assignRole('Super Admin');
    }
}