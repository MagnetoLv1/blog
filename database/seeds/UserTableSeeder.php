<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        App\User::truncate();
        App\User::create([
            'name'=>'Admin',
            'email'=>'admin@example.com',
            'password'=>bcrypt('password')
        ]);

        factory(App\User::class,5)->create();
    }
}
