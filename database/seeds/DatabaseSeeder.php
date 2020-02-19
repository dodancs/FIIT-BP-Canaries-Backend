<?php

use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
	{
        $pw = uniqid();
        $uuid = (string) Uuid::uuid4();

        echo "Default login is: admin@demo-cert.sk $pw\n";

        DB::table('users')->insert([
            'login'=>'admin@demo-cert.sk',
			'uuid'=>$uuid,
			'password'=>password_hash($pw,PASSWORD_DEFAULT),
			'permissions'=>json_encode(["is_admin"=>true]),
        ]);
    }
}
