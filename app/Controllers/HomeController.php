<?php

namespace App\Controllers;

use Hleb\Scheme\App\Controllers\MainController;
use Hleb\Constructor\Handlers\Request;
use App\Models\{HomeModel, FacetModel};
use Content, Base, Config, Translate;

class HomeController extends MainController
{
    // Главная страница
    public function index($sheet)
    {
        $uid    = Base::getUid();
        $page   = Request::getInt('page');
        $page   = $page == 0 ? 1 : $page;

        $limit  = 25;
        $latest_answers = HomeModel::latestAnswers($uid);
        $topics_user    = HomeModel::subscription($uid['user_id']);
        $pagesCount     = HomeModel::feedCount($topics_user, $uid, $sheet);
        $posts          = HomeModel::feed($page, $limit, $topics_user, $uid, $sheet);

        $result_post = [];
        foreach ($posts as $ind => $row) {
            $text = fragment($row['post_content']);
            $row['post_content_preview']    = Content::text($text, 'line');
            $row['post_date']               = lang_date($row['post_date']);
            $result_post[$ind]              = $row;
        }

        $result_answers = [];
        foreach ($latest_answers as $ind => $row) {
            $row['answer_content']      = cutWords($row['answer_content'], 8);
            $row['answer_date']         = lang_date($row['answer_date']);
            $result_answers[$ind]       = $row;
        }

        $num        = $page > 1 ? sprintf(Translate::get('page-number'), $page) : '';
        $meta_title = Config::get('meta.title') . $num;
        $meta_desc  = Config::get('meta.desc') . $num;

        if ($sheet == 'top' || $sheet == 'all') {
            $meta_title = Translate::get($sheet . '-title') . $num . Config::get('meta.title');
            $meta_desc  = Translate::get($sheet . '-desc') . $num . Config::get('meta.desc');
        }

        $topics = [];
        if (count($topics_user) == 0) {
            $topics = FacetModel::advice($uid['user_id']);
        }

        $m = [
            'og'         => true,
            'twitter'    => true,
            'imgurl'     => '/assets/images/agouti-max.png',
            'url'        => $sheet == 'top' ? '/top' : '/',
        ];

        return view(
            '/home',
            [
                'meta'  => meta($m, $meta_title, $meta_desc),
                'uid'   => $uid,
                'data'  => [
                    'pagesCount'        => ceil($pagesCount / $limit),
                    'pNum'              => $page,
                    'sheet'             => $sheet,
                    'latest_answers'    => $result_answers,
                    'topics_user'       => $topics_user,
                    'posts'             => $result_post,
                    'topics'            => $topics,
                ],
            ],
        );
    }
}
