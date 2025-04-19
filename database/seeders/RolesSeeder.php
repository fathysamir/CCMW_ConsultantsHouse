<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       //create role "Employee" for users
        $roles = [
            'Super Admin',
            'User'
            
        // Add more roles as needed
        ];

        foreach ($roles as $role) {
            $existed_role=Role::where('name' , $role)->first();
            if(!$existed_role){
                Role::create(['name' => $role]);
            }
        }
        $admin_role = Role::where('name','Super Admin')->first();
        
        $permissions = Permission::pluck('id', 'id')->all();

        $admin_role->syncPermissions($permissions);
        do {
            $code = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
        } while (User::where('code', $code)->exists());
        $admin1 = User::create([
            'name' => 'Super Admin',
            'code'=> $code,
            'email' => 'superadmin@ccmw.app',
            'password' => Hash::make('CMWAdminCMW'),
           
        ]);
        do {
            $code2 = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
        } while (User::where('code', $code2)->exists());
        $admin2 = User::create([
            'name' => 'Super Admin 2',
            'code'=> $code2,
            'email' => 'superadmin2@ccmw.app',
            'password' => Hash::make('CMWAdminCMW'),
           
        ]);
        
        
        $admin1->assignRole([$admin_role->id]);
        $admin2->assignRole([$admin_role->id]);


        
    }
}