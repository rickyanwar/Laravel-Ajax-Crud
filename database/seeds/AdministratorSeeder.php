<?php

use Illuminate\Database\Seeder;

class AdministratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $administrator = new \App\Model\User;
        $administrator->username = "administrator";
        $administrator->name = "Site Administrator";
        $administrator->email = "ricky@bidanku.test";
        $administrator->phone = "123456789";
        $administrator->roles = json_encode(["ADMIN"]);
        $administrator->password = \Hash::make("ricky");
        $administrator->avatar = "saat-ini-tidak-ada-file.png";
        $administrator->address = "Sarmili, Bintaro, Tangerang Selatan";
        $administrator->save();
        $this->command->info("User Admin berhasil diinsert");
    }
}
