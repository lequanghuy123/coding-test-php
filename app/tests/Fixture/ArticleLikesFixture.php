<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ArticleLikesFixture
 */
class ArticleLikesFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'ID' => 1,
                'article_id' => 1,
                'user_id' => 1,
                'liked_at' => '2024-03-22 10:02:12',
            ],
        ];
        parent::init();
    }
}
