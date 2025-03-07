<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       //create role "Employee" for users
       

      
        $admin_role = Role::where('name','Super Admin')->first();
        
       

       
        $admin1 = User::create([
            'name' => 'Super Admin 2',
            'email' => 'superadmin2@ccmw.app',
            'password' => Hash::make('CMWAdminCMW'),
           
        ]);
        
        

        $admin1->assignRole([$admin_role->id]);


        
    }
}