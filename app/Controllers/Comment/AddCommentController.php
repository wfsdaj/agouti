<?php

namespace App\Controllers\Comment;

use Hleb\Scheme\App\Controllers\MainController;
use Hleb\Constructor\Handlers\Request;
use App\Models\User\UserModel;
use App\Models\{NotificationsModel, ActionModel, AnswerModel, CommentModel, PostModel};
use Content, Base, Validation, SendEmail, Translate;

class AddCommentController extends MainController
{
    // Покажем форму
    public function index()
    {
        includeTemplate(
            '/_block/form/add-form-answer-and-comment',
            [
                'data'  => [
                    'answer_id'     => Request::getPostInt('answer_id'),
                    'post_id'       => Request::getPostInt('post_id'),
                    'comment_id'    => Request::getPostInt('comment_id'),
                ],
                'uid'   => Base::getUid()
            ]
        );
    }

    // Добавление комментария
    public function create()
    {
        $comment_content    = Request::getPost('comment');
        $post_id            = Request::getPostInt('post_id');   // в каком посту ответ
        $answer_id          = Request::getPostInt('answer_id');   // на какой ответ
        $comment_id         = Request::getPostInt('comment_id');   // на какой комментарий

        $uid        = Base::getUid();
        $ip         = Request::getRemoteAddress();
        $post       = PostModel::getPostId($post_id);
        pageError404($post);

        // Если пользователь забанен / заморожен
        $user = UserModel::getUser($uid['user_id'], 'id');
        (new \App\Controllers\Auth\BanController())->getBan($user);
        Content::stopContentQuietМode($user);

        $redirect = getUrlByName('post', ['id' => $post['post_id'], 'slug' => $post['post_slug']]);

        // Проверяем длину тела
        Validation::Limits($comment_content, Translate::get('comments'), '6', '2024', $redirect);

        // Ограничим добавления комментариев (в день)
        Validation::speedAdd($uid, 'comment');

        // Если контента меньше N и он содержит ссылку 
        // Оповещение админу
        $comment_published = 1;
        if (!Validation::stopSpam($comment_content, $uid['user_id'])) {
            addMsg(Translate::get('content-audit'), 'error');
            $comment_published = 0;
        }

        $comment_content = Content::change($comment_content);

        $data = [
            'comment_post_id'       => $post_id,
            'comment_answer_id'     => $answer_id,
            'comment_comment_id'    => $comment_id,
            'comment_content'       => $comment_content,
            'comment_published'     => $comment_published,
            'comment_ip'            => $ip,
            'comment_user_id'       => $uid['user_id'],
        ];

        $last_comment_id    = CommentModel::addComment($data);
        $url_comment        = $redirect . '#comment_' . $last_comment_id;

        if ($comment_published == 0) {
            ActionModel::addAudit('comment', $uid['user_id'], $last_comment_id);
            // Оповещение админу
            $type       = 15; // Упоминания в посте  
            $user_id    = 1;  // админу
            NotificationsModel::send($uid['user_id'], $user_id, $type, $last_comment_id, $url_comment, 1);
        }

        // Пересчитываем количество комментариев для поста + 1
        PostModel::updateCount($post_id, 'comments');

        // Оповещение автору ответа, что есть комментарий
        if ($answer_id) {
            // Себе не записываем
            $answ = AnswerModel::getAnswerId($answer_id);
            if ($uid['user_id'] != $answ['answer_user_id']) {
                $type = 4; // Ответ на пост        
                NotificationsModel::send($uid['user_id'], $answ['answer_user_id'], $type, $last_comment_id, $url_comment, 1);
            }
        }

        // Уведомление (@login)
        if ($message = Content::parseUser($comment_content, true, true)) {
            foreach ($message as $user_id) {
                // Запретим отправку себе и автору ответа (оповщение ему выше)
                if ($user_id == $uid['user_id'] || $user_id == $answ['answer_user_id']) {
                    continue;
                }
                $type = 12; // Упоминания в комментарии      
                NotificationsModel::send($uid['user_id'], $user_id, $type, $last_comment_id, $url_comment, 1);
                SendEmail::mailText($user_id, 'appealed');
            }
        }

        redirect($url_comment);
    }
}
