<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        //$this->call(AdminSeeder::class);
        // $this->call(PermissionSeeder::class);
        //$this->call(GatewaySeeder::class);

        $user = \App\Models\User::create([
            'first_name' => 'Andrew',
            'last_name' => 'Foley',
            'email' => 'andrew@so-creative.co.uk',
            'password' => Hash::make('Admin@78@@%'),
            'country' => 'UK',
            'phone' => '+123456789',
            'username' => 'AndrewFoley',
        ]);
    }
}
