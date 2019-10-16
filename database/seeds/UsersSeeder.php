<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::transaction(function () {
            DB::table('users')->truncate();

            $data = [
                [
                    'email' => 'admin@email.com',
                    'password' => Hash::make('123456'),
                ],
            ];

            foreach ($data as $row) {
                $user = User::create($row);
                $user->save();
            }
        });
    }
}
