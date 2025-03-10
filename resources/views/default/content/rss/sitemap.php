<?php $this->setCacheTime(3600); ?>
<?= '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc><?= $data['url']; ?></loc>
    <priority>1.0</priority>
    <changefreq>daily</changefreq>
  </url>
  <!-- Sitemap -->
  <?php foreach ($data['topics'] as $topic) { ?>
    <url>
      <loc><?= $data['url']; ?>/topic/<?= $topic['facet_slug']; ?></loc>
      <priority>0.5</priority>
      <changefreq>daily</changefreq>
    </url>
  <?php } ?>
  <?php foreach ($data['posts'] as $post) { ?>
    <url>
      <loc><?= $data['url']; ?><?= getUrlByName('post', ['id' => $post['post_id'], 'slug' => $post['post_slug']]); ?></loc>
      <priority>0.5</priority>
      <changefreq>daily</changefreq>
    </url>
  <?php } ?>
  <url>
    <loc><?= $data['url']; ?>/info</loc>
    <priority>0.5</priority>
    <changefreq>daily</changefreq>
  </url>
</urlset>