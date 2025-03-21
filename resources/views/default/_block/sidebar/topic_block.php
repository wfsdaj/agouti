<div class="bg-white br-rd5 mb15 br-box-gray p15">
  <h3 class="uppercase mb5 mt0 font-light size-14 gray"><?= Translate::get($lang); ?></h3>
  <?php foreach ($data as $sub) { ?>
    <a class="flex relative pt5 pb5 items-center hidden gray-light" href="<?= getUrlByName('topic', ['slug' => $sub['facet_slug']]); ?>" title="<?= $sub['facet_title']; ?>">
      <?= facet_logo_img($sub['facet_img'], 'max', $sub['facet_title'], 'w24 mr10 br-box-gray'); ?>
      <?= $sub['facet_title']; ?>
    </a>
  <?php } ?>
</div>