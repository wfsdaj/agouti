<div class="sticky col-span-2 justify-between no-mob">
  <?= includeTemplate('/_block/menu/left', ['sheet' => $data['sheet'], 'uid' => $uid]); ?>
</div>
<main class="col-span-7 mb-col-12">
  <?= breadcrumb(
    '/',
    Translate::get('home'),
    getUrlByName('user', ['login' => $uid['user_login']]),
    Translate::get('profile'),
    Translate::get('favorites')
  ); ?>
  <div class="bg-white flex flex-row items-center justify-between br-box-gray br-rd5 p15 mb15">
    <p class="m0"><?= Translate::get($data['sheet']); ?></p>
    <ul class="flex flex-row list-none m0 p0 center size-15">

      <?= tabs_nav(
        $uid['user_id'],
        $data['sheet'],
        $pages = [
          [
            'id' => 'favorites',
            'url' => getUrlByName('user.favorites', ['login' => $uid['user_login']]),
            'content' => Translate::get('favorites'),
            'icon' => 'bi bi-bookmark'
          ],
          [
            'id' => 'subscribed',
            'url' => getUrlByName('user.subscribed', ['login' => $uid['user_login']]),
            'content' => Translate::get('subscribed'),
            'icon' => 'bi bi-bookmark-plus'
          ],
        ]
      ); ?>

    </ul>
  </div>
  <div class="mt10">
    <?= includeTemplate('/_block/post', ['data' => $data, 'uid' => $uid]); ?>
  </div>
</main>
<?= includeTemplate('/_block/sidebar/lang', ['lang' => Translate::get('info-preferences')]); ?>