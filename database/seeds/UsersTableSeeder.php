<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pw = uniqid();
        echo "Default login is: admin@demo-cert.sk $pw\n";
        DB::table('users')->insert([
            'name'=>'admin',
            'email'=>'admin@demo-cert.sk',
            'password'=>password_hash($pw, PASSWORD_DEFAULT),
            'permissions'=>'["is_admin":1]',
        ]);
    }
}
