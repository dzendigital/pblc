<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     *
     * название Ролей
     *
     */
    public function run()
    {
        $account = new Role();
        $account->name = 'Пользователь';
        $account->slug = 'account';
        $account->save();

        $manager = new Role();
        $manager->name = 'Администратор';
        $manager->slug = 'manager';
        $manager->save();

        $admin = new Role();
        $admin->name = 'Главный администратор';
        $admin->slug = 'admin';
        $admin->save();


    }
}
