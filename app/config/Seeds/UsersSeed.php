<?php
declare(strict_types=1);

use Cake\Auth\AbstractPasswordHasher;
use Cake\Auth\DefaultPasswordHasher;
use Faker\Factory as Faker;
use Faker\Provider\Internet;
use Migrations\AbstractSeed;

/**
 * Users seed.
 */
class UsersSeed extends AbstractSeed
{
    /**
     * Run Method.
     * Write your database seeder using this method.
     * More information on writing seeds is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     *
     * @return void
     */
    public function run(): void
    {
        $faker = Faker::create();
        $faker->addProvider(new Internet($faker));
        $table = $this->table('users');
        $hasher = new DefaultPasswordHasher();
        $data[] = [
            'email' => 'author@author.com',
            'password' => $hasher->hash('author'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        for ($i = 0; $i < 10; $i++) {
            $data[] = [
                'email' => $faker->email,
                'password' => $hasher->hash('dummy123'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        $table->insert($data)->save();
    }
}
