<?php

use Illuminate\Database\Seeder;
use App\Entities\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $this->createUser();
    }

    private function createUser()
    {
      // User::create([
      //   'name' => 'Administrador',
      //   'email' => 'admin@mult3telecom.com.br',
      //   'password' => '@admin123',
      // ]);

      User::create([
        'name' => 'Administrador',
        'email' => 'admin@jnet.com.br',
        'password' => '@admin123',
      ]);
      // Exibe uma informação no console durante o processo de seed
      $this->command->info('usuario Administrador criado');

    }
}
