<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'id' => Uuid::uuid4()->toString(),
                'first_name' => 'Warehouse',
                'email' => 'warehouse@gmail.com',
                'telephone' => '62895617545308',
                'password' => bcrypt('123456789'),
                'role' => "warehouse",
                'status' => 0,
            ],

            [
                'id' => Uuid::uuid4()->toString(),
                'first_name' => 'Supplier',
                'email' => 'supplier@gmail.com',
                'telephone' => '62895617545307',
                'password' => bcrypt('123456789'),
                'role' => "supplier",
                'status' => 0,
            ],

            [
                'id' => Uuid::uuid4()->toString(),
                'first_name' => 'Owner',
                'email' => 'owner@gmail.com',
                'telephone' => '62895617545300',
                'password' => bcrypt('123456789'),
                'role' => "owner",
                'status' => 0,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
