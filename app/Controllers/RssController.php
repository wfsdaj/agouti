<?php

namespace App\Controllers;

use Hleb\Scheme\App\Controllers\MainController;
use Hleb\Constructor\Handlers\Request;
use App\Models\RssModel;
use Content, Config;

class RssController extends MainController
{
    // Route::get('/sitemap.xml')
    public function index()
    {
        $data = [
            'url'       => Config::get('meta.url'),
            'topics'    => RssModel::getTopicsSitemap(),
            'posts'     => RssModel::getPostsSitemap(),
        ];

        includeCachedTemplate('/rss/sitemap', ['data' => $data]);
    }

    // Route::get('/turbo-feed/topic/{slug}')
    public function turboFeed()
    {
        $topic_slug = Request::get('slug');
        $topic      = RssModel::getTopicSlug($topic_slug);
        pageError404($topic);

        $posts  = RssModel::getPostsFeed($topic_slug);
        $result = [];
        foreach ($posts as $ind => $row) {
            $text = explode("\n", $row['post_content']);
            $row['post_content']  = Content::text($text[0], 'line');
            // $row['post_content']  = Content::text($row['post_content'], 'text');
            $result[$ind]         = $row;
        }

        $data = [
            'url'       => Config::get('meta.url'),
            'posts'     => $result,
        ];

        includeCachedTemplate('/rss/turbo-feed', ['data' => $data, 'topic' => $topic]);
    }

    // Route::get('/rss-feed/topic/{slug}')
    public function rssFeed()
    {
        $topic_slug = Request::get('slug');
        $topic      = RssModel::getTopicSlug($topic_slug);
        pageError404($topic);

        $posts  = RssModel::getPostsFeed($topic_slug);
        $result = [];
        foreach ($posts as $ind => $row) {
            $text = explode("\n", $row['post_content']);
            $row['post_content']  = Content::text($text[0], 'line');
            $result[$ind]         = $row;
        }

        includeCachedTemplate(
            '/rss/rss-feed',
            [
                'data'  => [
                    'url'       => Config::get('meta.url'),
                    'posts'     => $result,
                ],
                'topic' => $topic
            ]
        );
    }
}
