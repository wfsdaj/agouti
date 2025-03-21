<div class="sticky col-span-2 justify-between no-mob">
  <?= includeTemplate('/_block/menu/left', ['sheet' => $data['sheet'], 'uid' => $uid]); ?>
</div>
<main class="col-span-7 mb-col-12">

  <?= breadcrumb(
    '/',
    Translate::get('home'),
    getUrlByName('user', ['login' => $uid['user_login']]),
    Translate::get('profile'),
    Translate::get('drafts')
  ); ?>

  <div class="bg-white br-rd5 br-box-gray br-rd5 p15 mb15">
    <?php if (!empty($data['drafts'])) { ?>
      <?php foreach ($data['drafts'] as $draft) { ?>

        <a href="<?= getUrlByName('post', ['id' => $draft['post_id'], 'slug' => $draft['post_slug']]); ?>">
          <h3 class="m0 size-21"><?= $draft['post_title']; ?></h3>
        </a>
        <div class="mr5 size-14 gray-light lowercase">
          <?= $draft['post_date']; ?> |
          <a href="<?= getUrlByName('post.edit', ['id' => $draft['post_id']]); ?>"><?= Translate::get('edit'); ?></a>
        </div>

      <?php } ?>
    <?php } else { ?>
      <?= no_content(Translate::get('there no drafts'), 'bi bi-info-lg'); ?>
    <?php } ?>
  </div>
</main>
<?= includeTemplate('/_block/sidebar/lang', ['lang' => Translate::get('under development')]); ?>