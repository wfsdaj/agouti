<div class="sticky col-span-2 justify-between no-mob">
  <?= includeTemplate('/_block/menu/left', ['sheet' => $data['sheet'], 'uid' => $uid]); ?>
</div>
<main class="col-span-7 mb-col-12">
  <div class="bg-white br-rd5 br-box-gray pt5 pr15 pb5 pl15">
    <?= breadcrumb(
      '/',
      Translate::get('home'),
      getUrlByName('user', ['login' => Request::get('login')]),
      Translate::get('profile'),
      Translate::get('posts') . ' ' . $data['user_login']
    ); ?>
  </div>
  <div class="mt15">
    <?= includeTemplate('/_block/post', ['data' => $data, 'uid' => $uid]); ?>
  </div>
</main>
<aside class="col-span-3 relative no-mob">
  <?= includeTemplate('/_block/menu/content', ['uid' => $uid, 'sheet' => $data['sheet']]); ?>
</aside>