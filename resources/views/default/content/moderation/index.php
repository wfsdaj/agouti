<div class="sticky col-span-2 justify-between no-mob">
  <?= includeTemplate('/_block/menu/left', ['sheet' => $data['sheet'], 'uid' => $uid]); ?>
</div>
<main class="col-span-7 mb-col-12 bg-white br-rd5 br-box-gray pt5 mt15 pr15 pb5 pl15">
  <h1 class="mt0 mb10 size-24 font-normal"><?= Translate::get('moderation log'); ?></h1>
  <?php if (!empty($data['moderations'])) { ?>
    <div class="mt15">
      <?php foreach ($data['moderations'] as  $mod) { ?>
        <div class="mb15 br-bottom p5">
          <div class="size-14 lowercase">
            <a class="black" href="<?= getUrlByName('user', ['login' => $mod['user_login']]); ?>">
              <?= user_avatar_img($mod['user_avatar'], 'small', $mod['user_login'], 'w24'); ?>
              <span class="mr5 ml5">
                <?= $mod['user_login']; ?>
              </span>
            </a>
            <span class="ml5 gray-light">
              <?= $mod['mod_created_at']; ?>
            </span>
          </div>
          <div>
            <a href="<?= getUrlByName('post', ['id' => $mod['post_id'], 'slug' => $mod['post_slug']]); ?>">
              <?= $mod['post_title']; ?>
            </a>
            <?php if ($mod['post_type'] == 1) { ?>
              <i class="bi bi-question-lg green"></i>
            <?php } ?>
          </div>
          <div class="size-14">
            <span class="gray-light">
              <?= Translate::get('action'); ?>:
            </span>
            <b><?= Translate::get($mod['mod_action']); ?></b>
          </div>
        </div>
      <?php } ?>

    <?php } else { ?>
      <?= no_content(Translate::get('no moderation logs'), 'bi bi-info-lg'); ?>
    <?php } ?>
    </div>
</main>
<?= includeTemplate('/_block/sidebar/lang', ['lang' => Translate::get('meta-moderation')]); ?>