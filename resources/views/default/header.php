<!DOCTYPE html>
<html lang="<?= Translate::getLang(); ?>" prefix="og: http://ogp.me/ns# article: http://ogp.me/ns/article# profile: http://ogp.me/ns/profile#">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?= $meta; ?>
  <?php getRequestHead()->output(); ?>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="icon" sizes="16x16" href="/favicon.ico" type="image/x-icon">
  <link rel="icon" sizes="120x120" href="/favicon-120.ico" type="image/x-icon">
</head>

<body class="p0 m0 black bg-gray-100<?php if (Request::getCookie('dayNight') == 'dark') { ?> dark<?php } ?>">

  <header class="bg-white br-bottom mt0 mb15 sticky top0 z-30">
    <div class="col-span-12 mr-auto max-width w-100 pr10 pl10 h44 grid items-center flex justify-between">
      <div class="flex items-center">
        <div class="lateral no-pc mr10 flex items-center size-15">
          <i class="bi bi-list gray-light-2 size-18"></i>
          <nav class="ltr-menu box-shadow none min-w165 bg-white br-rd3 p5 absolute justify-between mt0 ml0 pl0 sticky">
            <?php foreach (Config::get('menu-header-user-mobile') as $menu) { ?>
              <a class="pt5 pr10 pb5 pl10 gray block bg-hover-light" href="<?= getUrlByName($menu['url']); ?>">
                <i class="<?= $menu['icon']; ?> middle"></i>
                <span class="ml5"><?= $menu['name']; ?></span>
              </a>
            <?php } ?>
          </nav>
        </div>
        <div class="mr20 flex items-center">
          <a title="<?= Translate::get('home'); ?>" class="size-21 mb-size-18 p5 black dark-white uppercase" href="/">
            <?= Config::get('meta.name'); ?>
          </a>
        </div>
      </div>
      <?php if (Request::getUri() != getUrlByName('search')) { ?>
        <div class="p5 ml30 mr20 relative no-mob w-100">
          <form class="form" method="post" action="<?= getUrlByName('search'); ?>">
            <input type="text" autocomplete="off" name="q" id="find" placeholder="<?= Translate::get('to find'); ?>" class="h30 bg-gray-100 size-15 p15 br-rd20 gray w-100">
            <input name="token" value="<?= csrf_token(); ?>" type="hidden">
          </form>
          <div class="absolute box-shadow bg-white pt10 pr15 pb5 pl15 mt5 max-w460 br-rd3 none" id="search_items"></div>
        </div>
      <?php } ?>
      <?php if ($uid['user_id'] == 0) { ?>
        <div class="flex right col-span-4 items-center">
          <div id="toggledark" class="header-menu-item no-mob only-icon p10 ml30 mb-ml-10">
            <i class="bi bi-brightness-high gray-light-2 size-18"></i>
          </div>
          <?php if (Config::get('general.invite') == 0) { ?>
            <a class="register gray size-15 ml30 mr15 block" title="<?= Translate::get('sign up'); ?>" href="<?= getUrlByName('register'); ?>">
              <?= Translate::get('sign up'); ?>
            </a>
          <?php } ?>
          <a class="btn btn-outline-primary ml20" title="<?= Translate::get('sign in'); ?>" href="<?= getUrlByName('login'); ?>">
            <?= Translate::get('sign in'); ?>
          </a>
        </div>
      <?php } else { ?>
        <div class="col-span-4">
          <div class="flex right ml30 items-center">

            <?= add_post($facet, $uid['user_id']); ?>

            <div id="toggledark" class="only-icon p10 ml20 mb-ml-10">
              <i class="bi bi-brightness-high gray-light-2 size-18"></i>
            </div>

            <a class="gray-light-2 p10 ml20 mb-ml-10" href="<?= getUrlByName('user.notifications', ['login' => $uid['user_login']]); ?>">
              <?php if ($uid['notif']) { ?>
                <?php if ($uid['notif']['notification_action_type'] == 1) { ?>
                  <i class="bi bi-envelope size-18 red"></i>
                <?php } else { ?>
                  <i class="bi bi-bell-fill size-18 red"></i>
                <?php } ?>
              <?php } else { ?>
                <i class="bi bi-bell mb-size-18 size-18"></i>
              <?php } ?>
            </a>

            <div class="dropbtn relative p10 ml20 mb-ml-10">
              <a class="relative w-auto">
                <?= user_avatar_img($uid['user_avatar'], 'small', $uid['user_login'], 'w34 br-rd-50'); ?>
              </a>
              <div class="dr-menu box-shadow none min-w165 right0 bg-white size-15 br-rd3 p5 absolute">
                <?php foreach (Config::get('menu-header-user') as $menu) { ?>
                  <?= $menu['hr'] ?? ''; ?>
                  <?php if ($uid['user_trust_level'] >= $menu['tl']) { ?>
                    <a class="pt5 pr10 pb5 pl10 block gray bg-hover-light" href="<?= getUrlByName($menu['url'], ['login' => $uid['user_login']]); ?>">
                      <i class="<?= $menu['icon']; ?> middle mr5"></i>
                      <span class="middle size-14"><?= $menu['name']; ?></span>
                    </a>
                  <?php } ?>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      <?php }  ?>
    </div>
  </header>
  <div class="max-width mr-auto w-100 grid grid-cols-12 gap-4 pr5 pl5 justify-between">