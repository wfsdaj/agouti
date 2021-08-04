<?php include TEMPLATE_ADMIN_DIR . '/_block/header-admin.php'; ?>
<div class="wrap">
    <main class="admin">
        <div class="white-box">
            <div class="pt5 pr15 pb5 pl15">
                <a class="right" title="<?= lang('Add'); ?>" href="/admin/topics/add">
                    <i class="light-icon-plus middle"></i>
                </a>
                <?= breadcrumb('/admin', lang('Admin'), null, null, $data['meta_title']); ?>

                <div class="space">
                    <?php if (!empty($topics)) { ?>
                        <div class="t-table">
                            <div class="t-th">
                                <span class="t-td center">Id</span>
                                <span class="t-td"><?= lang('Logo'); ?></span>
                                <span class="t-td"><?= lang('Info'); ?></span>
                                <span class="t-td center"><?= lang('Action'); ?></span>
                            </div>
                            <?php foreach ($topics as $key => $topic) { ?>
                                <div class="t-tr">
                                    <span class="t-td width-30 center">
                                        <?= $topic['topic_id']; ?>
                                    </span>
                                    <span class="t-td width-30 center">
                                        <?= topic_logo_img($topic['topic_img'], 'max', $topic['topic_title'], 'ava-64'); ?>
                                    </span>
                                    <span class="t-td">
                                        <a class="size-21" rel="nofollow noreferrer" href="/topic/<?= $topic['topic_slug']; ?>">
                                            <?= $topic['topic_title']; ?>
                                        </a>
                                        <span class="mr5 ml5"> &#183; </span>
                                        <span class="green">topic/<?= $topic['topic_slug']; ?></span>
                                        <span class="mr5 ml5"> &#183; </span>
                                        <?= $topic['topic_count']; ?>
                                        <?php if ($topic['topic_is_parent'] == 1) { ?>
                                            <span class="mr5 ml5"> &#183; </span>
                                            <span class="red"><?= lang('Root'); ?></span>
                                        <?php } ?>
                                        <?php if ($topic['topic_parent_id'] != 0) { ?>
                                            <span class="mr5 ml5"> &#183; </span>
                                            <span class="green"><?= lang('Subtopic'); ?></span>
                                        <?php } ?>
                                        <div class="content-telo">
                                            <?= $topic['topic_description']; ?>
                                        </div>
                                    </span>
                                    <span class="t-td width-30 center">
                                        <a title="<?= lang('Edit'); ?>" href="topics/<?= $topic['topic_id']; ?>/edit">
                                            <i class="light-icon-edit middle"></i>
                                        </a>
                                    </span>
                                </div>
                            <?php } ?>
                        </div>

                    <?php } else { ?>
                        <div class="no-content"><?= lang('No'); ?>...</div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?= pagination($data['pNum'], $data['pagesCount'], $data['sheet'], '/admin/topics'); ?>
    </main>
</div>
<?php include TEMPLATE_ADMIN_DIR . '/_block/footer-admin.php'; ?>