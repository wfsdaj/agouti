<?php

namespace App\Controllers;

use Hleb\Constructor\Handlers\Request;
use App\Models\UserModel;
use App\Models\AnswerModel;
use App\Models\PostModel;
use App\Models\VotesModel;
use Lori\Content;
use Lori\Config;
use Lori\Base;

class AnswerController extends \MainController
{
    // Все ответы
    public function index()
    {
        $pg = \Request::getInt('page'); 
        $page = (!$pg) ? 1 : $pg;
        
        $uid        = Base::getUid();
         
        $pagesCount = AnswerModel::getAnswersAllCount();  
        $answ       = AnswerModel::getAnswersAll($page, $uid['trust_level']);
 
        $result = Array();
        foreach ($answ  as $ind => $row) {
            $row['answer_content']  = Content::text($row['answer_content'], 'text');
            $row['date']            = lang_date($row['answer_date']);
            // N+1 - перенести в запрос
            $row['answer_vote_status'] = VotesModel::voteStatus($row['answer_id'], $uid['id'], 'answer');
            $result[$ind]   = $row;
        }
        
        if ($page > 1) { 
            $num = ' — ' . lang('Page') . ' ' . $page;
        } else {
            $num = '';
        }
        
        $data = [
            'h1'            => lang('All answers'),
            'pagesCount'    => $pagesCount,
            'pNum'          => $page,
            'canonical'     => Config::get(Config::PARAM_URL) . '/answers',
            'sheet'         => 'answers', 
            'meta_title'    => lang('All answers') .' | '. Config::get(Config::PARAM_NAME),
            'meta_desc'     => lang('answers-desc') .' '. Config::get(Config::PARAM_HOME_TITLE),            
        ];

        return view(PR_VIEW_DIR . '/answer/answers', ['data' => $data, 'uid' => $uid, 'answers' => $result]);
    }
    
   // Покажем форму редактирования
	public function editAnswerPage()
	{
        $answer_id  = \Request::getInt('answer_id');
        $post_id    = \Request::getInt('post_id');
        $uid        = Base::getUid();
        
        $answer = AnswerModel::getAnswerOne($answer_id);

        // Проверка доступа 
        if (!accessСheck($answer, 'answer', $uid, 0, 0)) {
            redirect('/');
        }        

        $post = PostModel::postId($post_id);
        Base::PageError404($post);

        Request::getResources()->addBottomStyles('/assets/editor/editormd.css');
        Request::getResources()->addBottomScript('/assets/editor/editormd.js');
        Request::getResources()->addBottomScript('/assets/editor/lib/marked.min.js');
        Request::getResources()->addBottomScript('/assets/editor/lib/prettify.min.js');
        Request::getResources()->addBottomScript('/assets/editor/config.js');
        
        $data = [
            'h1'                => lang('Edit answer'),
            'answer_id'         => $answer_id,
            'post_id'           => $post_id,
            'user_id'           => $uid['id'],
            'answer_content'    => $answer['answer_content'],
            'sheet'             => 'edit-answers', 
            'meta_title'        => lang('All answers'),
            'meta_desc'         => lang('answers-desc'),  
        ]; 
        
        return view(PR_VIEW_DIR . '/answer/edit-form-answer', ['data' => $data, 'uid' => $uid, 'post' => $post]);
    }

    // Редактируем ответ
    public function editAnswer()
    {
       
        $answer_id      = \Request::getPostInt('answer_id');
        $post_id        = \Request::getPostInt('post_id');
        $answer_content = $_POST['answer']; // не фильтруем

        $post = PostModel::postId($post_id);

        // Получим относительный url поста для возрата
        $url = '/post/' . $post['post_id'] . '/' . $post['post_slug'];
        
        // id того, кто редактирует
        $uid        = Base::getUid();
        $user_id    = $uid['id'];
        
        $answer = AnswerModel::getAnswerOne($answer_id);
        
        // Проверка доступа
        if (!accessСheck($answer, 'answer', $uid, 0, 0)) {
            redirect('/');
        }        
        
        Base::Limits($answer_content, lang('Bodies'), '6', '5000', '/' . $url);

        // Редактируем комментарий
        AnswerModel::AnswerEdit($answer_id, $answer_content);
        
        redirect('/' . $url . '#answer_' . $answer_id); 
	}
    
    // Ответы участника
    public function userAnswers()
    {
        $login  = \Request::get('login');
        $user   = UserModel::getUser($login, 'slug');
        
        Base::PageError404($user);
        
        $answers  = AnswerModel::userAnswers($login); 
        
        $result = Array();
        foreach ($answers as $ind => $row) {
            $row['content'] = Content::text($row['answer_content'], 'text');
            $row['date']    = lang_date($row['answer_date']);
            $result[$ind]   = $row;
        }
        
        $uid  = Base::getUid();
        $data = [
            'h1'            =>  lang('Answers-n') .' '. $login,
            'canonical'     => Config::get(Config::PARAM_URL) . '/u/' . $login . '/answers',
            'sheet'         => 'user-answers', 
            'meta_title'    => lang('Answers') .' '. $login .' | '. Config::get(Config::PARAM_NAME),
            'meta_desc'     => 'Ответы  учасника сообщества ' . $login .' '. Config::get(Config::PARAM_HOME_TITLE),
        ];
        
        return view(PR_VIEW_DIR . '/answer/user-answer', ['data' => $data, 'uid' => $uid, 'answers' => $result]);
    }

    // Помещаем комментарий в закладки
    public function addAnswerFavorite()
    {
        $uid        = Base::getUid();
        $answer_id  = \Request::getPostInt('answer_id');
        $answer     = AnswerModel::getAnswerOne($answer_id); 
        
        if (!$answer) {
            redirect('/');
        }
        
        AnswerModel::setAnswerFavorite($answer_id, $uid['id']);
       
        return true;
    } 
}