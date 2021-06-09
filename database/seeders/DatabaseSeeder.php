<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();

        $user = new User();
        $user->email = 'b.fijneman@designone.nl';
        $user->master_password = hash("sha512", 'b.fijneman');
        $user->name = 'Bart Fijneman';
        $user->save();

        $user = new User();
        $user->email = 'w.molhoek@designone.nl';
        $user->master_password = hash("sha512", 'w.molhoek');
        $user->name = 'Wouter molhoek';
        $user->save();
    }
}
