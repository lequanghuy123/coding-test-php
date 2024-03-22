<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Article;
use App\Model\Table\ArticleLikesTable;
use Authorization\IdentityInterface;
use Cake\ORM\TableRegistry;

/**
 * Article policy
 *
 * @property ArticleLikesTable ArticleLike
 */
class ArticlePolicy
{
    /**
     * Check if $user can add Article
     *
     * @param IdentityInterface $user The user.
     *
     * @return bool
     */
    public function canAdd(IdentityInterface $user)
    {
        return true;
    }

    /**
     * Check if $user can edit Article
     *
     * @param IdentityInterface $user The user.
     * @param Article $article
     *
     * @return bool
     */
    public function canEdit(IdentityInterface $user, Article $article)
    {
        return $this->isAuthor($user, $article);
    }

    protected function isAuthor(IdentityInterface $user, Article $article)
    {
        return $article->user_id === $user->getIdentifier();
    }

    /**
     * Check if $user can delete Article
     *
     * @param IdentityInterface $user The user.
     * @param Article $article
     *
     * @return bool
     */
    public function canDelete(IdentityInterface $user, Article $article)
    {
        return $this->isAuthor($user, $article);
    }

    /**
     * Check if $user can like Article
     *
     * @param IdentityInterface $user The user.
     * @param Article $article
     *
     * @return bool
     */
    public function canLike(IdentityInterface $user, Article $article)
    {
        return !$this->isLiked($user, $article);
    }

    /**
     * @param IdentityInterface $user
     * @param Article $article
     *
     * @return bool
     */
    protected function isLiked(IdentityInterface $user, Article $article)
    {
        return TableRegistry::getTableLocator()->get('ArticleLikes')
            ->exists(['user_id' => $user->getIdentifier(), 'article_id' => $article->id]);
    }
}
