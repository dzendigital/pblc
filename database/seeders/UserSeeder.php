<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Account;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     *
     * создание root-пользователя, amin
     *
     */
    public function run()
    {
    	# по умолчанию назначаем доступ для админа

        $admin = Role::where('slug', 'admin')->first();
        $manager = Role::where('slug', 'manager')->first();
        $account = Role::where('slug', 'account')->first();
 
        $users = array(
            array(
                "name" => "admin",
                "email" => "admin@gmail.com",
                "password" => null,
                "email_verified_at" => "",
                "role" => "admin",
            ),
            array(
                "name" => "manager",
                "email" => "manager@gmail.com",
                "password" => null,
                "email_verified_at" => "",
                "role" => "manager",
            ),
            array(
                "name" => "Пользователь",
                "email" => "account@gmail.com",
                "password" => null,
                "email_verified_at" => "",
                "role" => "account",
            ),
            array(
                "name" => "Александр",
                "email" => "cocos_@mail.ru",
                "password" => null,
                "email_verified_at" => "",
                "role" => "account",
            ),
        );

        foreach ( $users as $key => $value) {
            $item = new User();
            $item->name = $value["name"];
            # используйте указанную пару ключей для входа || use login parametsr below to access to your project 
            $item->email = $value["email"];
            $item->password = bcrypt($value["email"]);
            $item->email_verified_at = now();
            $item->save();

            # назначение роли
            switch ( $value["role"] ) {
                case "admin":
                    $role = $admin;
                    $item->roles()->attach($role);
                break;
                case "manager":
                    $role = $manager;
                    $item->roles()->attach($role);
                break;
                case "account":

                    # назначаем роль
                    $role = $account;
                    $item->roles()->attach($role);

                    # создаем профиль Account
                    $account_model = array(
                        "user_id" => $item->id,
                        "title" => $item->name,
                        "is_visible" => 1,
                        "sort" => null,
                    );
                    $account = new Account($account_model);
                    $account->save();

                break;
            }


        }
    }
}
