<?php include TEMPLATE_DIR . '/header.php'; ?>
<section>
    <div class="wrap">
        <div class="telo detail">
            <h1 class="titl"><?= $data['post']['title']; ?></h1>
            <div class="footer">
                <img class="ava" src="/images/user/small/<?= $data['post']['avatar']; ?>">
                <span class="user"> 
                    <a href="/u/<?= $data['post']['login']; ?>"><?= $data['post']['login']; ?></a> 
                </span>
                <span class="date"> 
                    <?= $data['post']['date']; ?>
                    <?php if($data['post']['edit_date']) { ?> (изм. <?= $data['post']['edit_date']; ?>) <?php } ?>
                </span>
                <span class="date"> 
                    <a class="space space_<?= $data['post']['space_tip'] ?>" href="/s/<?= $data['post']['space_slug']; ?>" title="<?= $data['post']['space_name']; ?>">
                        <?= $data['post']['space_name']; ?>
                    </a>
                </span>
                <?php if($usr['login'] == $data['post']['login']) { ?>
                    <span class="date">
                       &nbsp; <a href="/post/edit/<?= $data['post']['id']; ?>">
                            <svg class="md-icon moon">
                                <use xlink:href="/svg/icons.svg#edit"></use>
                            </svg>
                        </a>
                    </span>
                <?php } ?> 
            </div>   
            <div class="post">
                <?= $data['post']['content']; ?> 
            </div> 

            <?php if ($usr['id'] > 0) { ?>
            <form id="add_comm" class="new_comment" action="/comment/add" accept-charset="UTF-8" method="post">
            <?= csrf_field() ?>
                <textarea rows="5" placeholder="Напишите, что нибудь..." name="comment" id="comment"></textarea>
                <div> 
                    <input type="hidden" name="post_id" id="post_id" value="<?= $data['post']['id']; ?>">
                    <input type="hidden" name="comm_id" id="comm_id" value="0">
                    <input type="submit" name="commit" value="Комментарий" class="comment-post">
                </div> 
            </form>
            <?php } else { ?>
                <textarea rows="5" disabled="disabled" placeholder="Вы должны войти в систему, чтобы оставить комментарий." name="comment" id="comment"></textarea>
                <div> 
                    <input type="submit" name="commit" value="Комментарий" class="comment-post" disabled="disabled">
                </div> 
            <?php } ?>
            
        </div>
 
        <?php if (!empty($data['comments'])) { ?>
            <div class="telo comments">
                <h2><?= $data['post']['num_comments'] ?> <?= $data['post']['post_comments'] ?></h2>
                
                <?php foreach ($data['comments'] as  $comm) { ?>
                <div class="block-comments">
                    <ol class="comment-telo<?php if ($comm['level'] == 0) { ?> one<?php } ?><?php if ($comm['level'] == 2) { ?> two<?php } ?><?php if ($comm['level'] > 2) { ?> three<?php } ?>"> 
                        <li class="comments_subtree" id="comm_<?= $comm['comment_id']; ?>">

                           <?php if ($comm['comm_vote_status'] || $usr['id'] == $comm['comment_user_id']) { ?>
                                <div class="voters active">
                                    <div class="comm-up-id"></div>
                                    <div class="score"><?= $comm['comment_votes']; ?></div>
                                </div>
                            <?php } else { ?>
                                <div id="up<?= $comm['comment_id']; ?>" class="voters">
                                    <div data-id="<?= $comm['comment_id']; ?>" class="comm-up-id"></div>
                                    <div class="score"><?= $comm['comment_votes']; ?></div>
                                </div>
                            <?php } ?>

                            <div class="comm-telo">
                                <div class="comm-header">
                                    <img class="ava" src="/images/user/small/<?= $comm['avatar'] ?>">
                                    <span class="user"> 
                                        <a href="/u/<?= $comm['login']; ?>"><?= $comm['login']; ?></a> 
                                    </span> 
                                    <span class="date">  
                                       <?= $comm['date']; ?>
                                    </span>
                                    <span class="date">  
                                        <?php if ($data['post']['post_user_id'] == $comm['comment_user_id']) { ?><span class="authorpost">&#x21af;</span> <?php } ?>
                                    </span>
                                    <span class="date">
                                        <a rel="nofollow" href="/posts/<?= $data['post']['slug']; ?>#comm_<?= $comm['comment_id']; ?>">#</a>
                                    </span>
                                    <?php if ($comm['level'] != 0) { ?> 
                                        <span class="date">
                                            <a rel="nofollow" href="/posts/<?= $data['post']['slug']; ?>#comm_<?= $comm['comment_on']; ?>">&#8679;</a>
                                        </span>
                                    <?php } ?> 
                                </div>
                                <div class="comm-telo-body">
                                    <?= $comm['content'] ?> 
                                </div>
                            </div>
                            <span id="cm_add_link<?php $comm['comment_id']; ?>" class="cm_add_link">
                                <a data-post_id="<?= $data['post']['id']; ?>" data-id="<?= $comm['comment_id']; ?>" class="addcomm">Ответить</a>
                            </span>

                            <div id="cm_addentry<?= $comm['comment_id']; ?>" class="reply"></div> 
                        
                        </li>
                    </ol>
                </div>
                <?php } ?>
                 
            </div>
        <?php } else { ?>
            <div class="telo">
                <p>К сожалению комментариев пока нет...</p>
            </div>
        <?php } ?>
  
    </div>
</section>
<?php include TEMPLATE_DIR . '/footer.php'; ?> 