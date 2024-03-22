<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\ArticleLikesTable;
use App\Model\Table\ArticlesTable;
use Authentication\Controller\Component\AuthenticationComponent;
use Authorization\Controller\Component\AuthorizationComponent;
use Authorization\Exception\ForbiddenException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\TableRegistry;
use Cake\View\JsonView;
use Exception;

/**
 * Articles Controller
 *
 * @property ArticlesTable $Articles
 * @property ArticleLikesTable $ArticlesLikesTable
 * @property AuthorizationComponent $Authorization
 * @property AuthenticationComponent $Authentication
 */
class ArticlesController extends ApiController
{
    /**
     * @throws Exception
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Authentication.Authentication');
        $this->loadComponent('Auth',
            [
                'authenticate' => ['Basic' => ['fields' => ['username' => 'email', 'password' => 'password']]
                ]
            ]
        );
        $this->loadComponent('Authorization.Authorization',
            [
                'skipAuthorization' => [
                    'index',
                    'view',
                    'add'
                ]
            ]
        );
    }

    public function viewClasses(): array
    {
        return [JsonView::class];
    }

    /**
     * Index method
     *
     * @return void Renders view
     */
    public function index()
    {
        $articles = [];
        $result = $this->Authentication->getResult();
        if ($result && $result->isValid()) {
            $articles = $this->Articles->find()->all()->toArray();
        }
        $this->set(compact('articles'));
        $this->_viewBuilder->setOption('serialize', ['articles']);
    }

    /**
     * View method
     *
     * @param string|null $id Article id.
     *
     * @return void Renders view
     * @throws RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $article = [];
        $result = $this->Authentication->getResult();
        if ($result && $result->isValid()) {
            $article = $this->Articles->get($id)->toArray();
        }
        $this->set(compact('article'));
        $this->_viewBuilder->setOption('serialize', ['article']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $errorCode = false;
        $message = __('You are not allowed.');
        $result = $this->Authentication->getResult();
        if ($result && $result->isValid()) {
            try {
                $user = $this->Auth->identify();
                $article = $this->Articles->newEmptyEntity();
                $article = $this->Articles->patchEntity($article, $this->request->getData());
                $article->set('user_id', $user['id']);
                if ($this->Articles->save($article)) {
                    $message = __('The article has been saved.');
                } else {
                    $errorCode = true;
                    $message = __('The article could not be saved. Please, try again.');
                }
            } catch (ForbiddenException $exception) {
                $message = __('You are not allowed.');
                $errorCode = true;
            }
        }
        $this->set(compact('errorCode', 'message'));
        $this->_viewBuilder->setOption('serialize', ['message', 'errorCode']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Article id.
     *
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $errorCode = false;
        $message = '';
        $result = $this->Authentication->getResult();
        if ($result && $result->isValid()) {
            $article = $this->Articles->get($id, [
                'contain' => []
            ]);
            try {
                $this->Authorization->authorize($article);
                $article = $this->Articles->patchEntity($article, $this->request->getData());
                if ($this->Articles->save($article)) {
                    $message = __('The article has been saved.');
                } else {
                    $errorCode = true;
                    $message = __('The article could not be saved. Please, try again.');
                }
            } catch (ForbiddenException $exception) {
                $message = __('You are not allowed.');
                $errorCode = true;
            }
        }
        $this->set(compact('errorCode', 'message'));
        $this->_viewBuilder->setOption('serialize', ['message', 'errorCode']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Article id.
     *
     * @return void Redirects to index.
     * @throws RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $errorCode = false;
        $message = '';
        $this->request->allowMethod(['post', 'delete']);
        $result = $this->Authentication->getResult();
        if ($result && $result->isValid()) {
            try {
                $article = $this->Articles->get($id);
                $this->Authorization->authorize($article);
                if ($this->Articles->delete($article)) {
                    $message = __('The article has been deleted.');
                } else {
                    $errorCode = true;
                    $message = __('The article could not be deleted. Please, try again.');
                }
            } catch (ForbiddenException $exception) {
                $message = __('You are not allowed.');
                $errorCode = true;
            }
        }
        $this->set(compact('errorCode', 'message'));
        $this->_viewBuilder->setOption('serialize', ['message', 'errorCode']);
    }

    /**
     * @param $id
     */
    public function like($id)
    {
        $errorCode = false;
        $message = '';
        $result = $this->Authentication->getResult();
        if ($result && $result->isValid()) {
            $this->Articles->getConnection()->begin();
            try {
                $user = $this->Auth->identify();
                $article = $this->Articles->get($id);
                $this->Authorization->authorize($article);
                /**
                 * @var ArticleLikesTable $articleLikes
                 */
                $articleLikes = TableRegistry::getTableLocator()->get('ArticleLikes');
                $articleLikesModel = $articleLikes->patchEntity($articleLikes->newEmptyEntity(), [
                    'article_id' => $article->id,
                    'user_id' => $user['id']
                ]);

                $this->Articles->updateAll(
                    ['count_like' => $this->Articles->query()->newExpr('count_like + 1')],
                    ['Articles.id' => $id]
                );
                $articleLikes->save($articleLikesModel);
                $this->Articles->getConnection()->commit();
                $message = __('The article has been liked.');
            } catch (ForbiddenException $exception) {
                $message = __('Cannot like or cancel like. This article is liked by you.');
                $errorCode = true;
            } catch (Exception $exception) {
                $this->Articles->getConnection()->rollback();
                $message = __('Can not like. DB error.');
                $errorCode = true;
            }
        }
        $this->set(compact('errorCode', 'message'));
        $this->_viewBuilder->setOption('serialize', ['message', 'errorCode']);
    }
}
