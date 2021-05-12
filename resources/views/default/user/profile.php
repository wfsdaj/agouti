<?php include TEMPLATE_DIR . '/header.php'; ?>
</div>
<style nonce="<?= $_SERVER['nonce']; ?>">
.profile-box {background:<?= $user['color']; ?>;}
.gravatar {border: 5px solid <?= $user['color']; ?>;}
</style>
<div class="profile-box">
    <div class="wrap">
        <div class="profile-header">
            <?php if($uid['trust_level'] > 0) { ?>
                <?php if($uid['login'] != $user['login']) { ?> &nbsp; 
                    <a class="right pm" href="/u/<?= $user['login']; ?>/mess">
                        <i class="icon envelope"></i>
                    </a> 
                <?php } else { ?>
                    <a class="right pm"  href="/u/<?= $uid['login']; ?>/setting">
                        <i class="icon pencil"></i>
                    </a> 
                <?php } ?>
            <?php } ?>
            
            <div class="gravatar">
                <img alt="<?= $user['login']; ?>" src="/uploads/avatar/<?= $user['avatar']; ?>">
            </div>
            
            <center>
            <h1 class="profile">
                <?= $user['login']; ?> 
            
                <?php if($user['name']) { ?> / <?= $user['name']; ?><?php } ?>
            </h1>

            <div class="na about">
                <?php if($user['about']) { ?>
                <?= $user['about']; ?>
                <?php } else { ?>
                <?= lang('Riddle'); ?>...
                <?php } ?>
            </div>

            <div class="stats">
                <?php if($data['post_num_user'] != 0) { ?>
                    <div class="d">
                        <a title="Всего постов <?= $user['login']; ?>" href="/u/<?= $user['login']; ?>/posts">
                            <?= $data['post_num_user']; ?>
                        </a>
                    </div> 
                    <div>Постов</div>
                    <br>
                <?php } ?>
                <?php if($data['answ_num_user'] != 0) { ?>
                    <div class="d">
                        <a title="Всего ответов <?= $user['login']; ?>" href="/u/<?= $user['login']; ?>/answers">
                            <?= $data['answ_num_user']; ?>
                        </a>
                    </div> 
                    <div>Ответов</div>
                    <br>
                <?php } ?>
                <?php if($data['comm_num_user'] != 0) { ?>
                    <div class="d">
                        <a title="Все комментарии <?= $user['login']; ?>" href="/u/<?= $user['login']; ?>/comments">
                            <?= $data['comm_num_user']; ?>
                        </a>
                    </div>  
                    <div>Комментариев</div>
                    <br>
                <?php } ?>
            </div>
            
            </center>
        </div>
        
        <div class="box profile-telo">
     
            <label class="required">TL:</label>
            <span class="d">
                <a title="Уровень доверия" href="/info/trust-level"><?= $data['trust_level']['trust_name']; ?></a>
            </span>

            <br>
            
            <label class="required">Присоединился:</label>
            <span class="d"><?= $user['created_at']; ?></span>

            <br>

            <?php if($data['post_num_user'] != 0) { ?>
                <label class="required">Постов:</label>
                <span class="d">
                    <a title="Всего постов <?= $user['login']; ?>" href="/u/<?= $user['login']; ?>/posts">
                        <?= $data['post_num_user']; ?>
                    </a>
                </span> <br>
            <?php } ?>
            <?php if($data['answ_num_user'] != 0) { ?>
                <label class="required">Ответов:</label>
                <span class="d">
                    <a title="Всего ответов <?= $user['login']; ?>" href="/u/<?= $user['login']; ?>/answers">
                        <?= $data['answ_num_user']; ?>
                    </a>
                </span> <br>
            <?php } ?>
            <?php if($data['comm_num_user'] != 0) { ?>
                <label class="required">Комментариев:</label>
                <span class="d">
                    <a title="Все комментарии <?= $user['login']; ?>" href="/u/<?= $user['login']; ?>/comments">
                        <?= $data['comm_num_user']; ?>
                    </a>
                </span>  <br>
            <?php } ?>
            
            <?php if($data['space_user']) { ?>
                <label class="required"><?= lang('Created by'); ?>:</label>
                <span class="d">
                    <?php foreach ($data['space_user'] as  $space) { ?>
                        <a href="/s/<?= $space['space_slug'];?>"><?= $space['space_name'];?></a> &nbsp; 
                    <?php } ?>
                </span>     
                <br>
            <?php } ?>
            
     

            <?php if($user['my_post'] != 0) { ?>
                <h4><?= lang('Selected Post'); ?>:</h4>

                <div class="post-telo">
                    <div id="vot<?= $post['post_id']; ?>" class="voters">
                        <div data-id="<?= $post['post_id']; ?>" class="post-up-id"></div>
                        <div class="score"><?= $post['post_votes']; ?></div>
                    </div>
                    <div class="post-body">
                        <a class="u-url" href="/post/<?= $post['post_id']; ?>/<?= $post['post_slug']; ?>">
                            <h2 class="titl"><?= $post['post_title']; ?></h2>
                        </a>
                        
                        <div class="space-color space_<?= $post['space_color'] ?>"></div>
                        <a class="space-u" href="/s/<?= $post['space_slug']; ?>" title="<?= $post['space_name']; ?>">
                            <?= $post['space_name']; ?>
                        </a>
                        
                        <div class="footer"> 
                            <img class="ava" alt="<?= $user['login']; ?>" src="/uploads/avatar/small/<?= $user['avatar']; ?>">
                            <span class="user"> 
                                <a href="/u/<?= $user['login']; ?>">
                                    <?= $user['login']; ?>
                                </a> 
                            </span>
       
                            <span class="date"> 
                               <?= $post['post_date'] ?>
                            </span>
                            <?php if($post['post_answers_num'] !=0) { ?> 
                                <span class="otst"> | </span>
                                <a class="u-url" href="/post/<?= $post['post_id']; ?>/<?= $post['post_slug']; ?>">
                                  ответов <?= $post['post_answers_num']; ?>  
                                </a>
                            <?php } ?>
                        </div>
                    </div>                        
                </div>
            <br>   
            <?php } ?>
        </div>
        
        
    </div>
</div>

<div class="wrap">
 
 
<?php include TEMPLATE_DIR . '/footer.php'; ?>