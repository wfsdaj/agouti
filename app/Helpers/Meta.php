<?php

function meta($m, $title = '', $desc = '', $date_article = '')
{
    $output = '';
    if ($title == '') {
        $title = Config::get('meta.title');
    }
    if ($desc == '') {
        $desc = Config::get('meta.name');
    }

    $output .= '<title>' . $title . ' | ' . Config::get('meta.name') . '</title>'
        . '<meta name="description" content="' . $desc . '">';

    if ($date_article != '') {
        $output .= '<meta property="og:type" content="article" />'
            . '<meta property="article:published_time" content="' . $date_article . '" />';
    }

    if (!empty($m)) {
        if ($m['url']) {
            $output .= '<link rel="canonical" href="' . Config::get('meta.url') . $m['url'] . '">';
        }

        if ($m['og']) {
            $output .= '<meta property="og:title" content="' . $title . '"/>'
                . '<meta property="og:description" content="' . $desc . '"/>'
                . '<meta property="og:url" content="' . Config::get('meta.url') . $m['url'] . '"/>';

            if ($m['imgurl']) {
                $output .= '<meta property="og:image" content="' . Config::get('meta.url') . $m['imgurl'] . '"/>'
                    . '<meta property="og:image:width" content="820" />'
                    . '<meta property="og:image:height" content="320" />';
            }
        }

        if ($m['twitter']) {
            $output .= '<meta name="twitter:card" content="summary"/>'
                . '<meta name="twitter:title" content="' . $title . '"/>'
                . '<meta name="twitter:description" content="' . $desc . '"/>'
                . '<meta name="twitter:url" content="' . Config::get('meta.url') . $m['url'] . '"/>';

            if ($m['imgurl']) {
                $output .= '<meta name="twitter:image" content="' . Config::get('meta.url') . $m['imgurl'] . '"/>';
            }
        }
    }

    return $output;
}
