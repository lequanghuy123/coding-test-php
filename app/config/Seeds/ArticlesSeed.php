<?php
declare(strict_types=1);

use Faker\Factory as Faker;
use Faker\Provider\Lorem;
use Migrations\AbstractSeed;

/**
 * Articles seed.
 */
class ArticlesSeed extends AbstractSeed
{
    /**
     * Run Method.
     * Write your database seeder using this method.
     * More information on writing seeds is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     *
     * @return void
     * @throws Exception
     */
    public function run(): void
    {
        $faker = Faker::create();
        $faker->addProvider(new Lorem($faker));
        $table = $this->table('articles');
        $data = [];
        for ($i = 0; $i < 1000; $i++) {
            $data[] = [
                'user_id' => random_int(1, 10),
                'title' => $faker->word(),
                'body' => $faker->word(),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        $table->insert($data)->save();
    }
}
