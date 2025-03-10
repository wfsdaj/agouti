<?php

namespace App\Models\User;

use Hleb\Scheme\App\Models\MainModel;
use DB;
use PDO;

class UserModel extends MainModel
{
    // Страница участников
    public static function getUsersAll($page, $limit, $user_id, $sheet)
    {
        if ($sheet == 'all') {
            $string = "ORDER BY user_id DESC LIMIT";
        } elseif ($sheet == 'ban') {
            $string = "WHERE user_ban_list > 0 ORDER BY user_id DESC LIMIT";
        } else {
            $string = "WHERE user_is_deleted != 1 and user_ban_list != 1
                        ORDER BY user_id = $user_id DESC, user_trust_level DESC LIMIT";
        }

        $start  = ($page - 1) * $limit;
        $sql = "SELECT  
                    user_id,
                    user_login,
                    user_email,
                    user_name,
                    user_avatar,
                    user_created_at,
                    user_whisper,
                    user_updated_at,
                    user_whisper,
                    user_trust_level,
                    user_activated,
                    user_invitation_id,
                    user_limiting_mode,
                    user_reg_ip,
                    user_ban_list,
                    user_is_deleted
                        FROM users 
                        $string
                        $start, $limit";

        return DB::run($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Количество
    public static function getUsersAllCount($sheet)
    {
        $string = "WHERE user_ban_list > 0";
        if ($sheet == 'all') {
            $string = "";
        }

        $sql = "SELECT 
                    user_id,
                    user_is_deleted
                        FROM users $string";

        return  DB::run($sql)->rowCount();
    }

    // Информация по участнику (id, slug)
    public static function getUser($params, $name)
    {
        $sort = "user_id = :params";
        if ($name == 'slug') {
            $sort = "user_login = :params";
        }

        $sql = "SELECT 
                    user_id,
                    user_login,
                    user_name,
                    user_whisper,
                    user_activated,
                    user_limiting_mode,
                    user_reg_ip,
                    user_email,
                    user_avatar,
                    user_trust_level,
                    user_cover_art,
                    user_color,
                    user_template,
                    user_lang,
                    user_invitation_available,
                    user_about,
                    user_website,
                    user_location,
                    user_public_email,
                    user_skype,
                    user_twitter,
                    user_telegram,
                    user_vk,
                    user_created_at,
                    user_updated_at,
                    user_my_post,
                    user_ban_list,
                    user_hits_count,
                    user_up_count,
                    user_is_deleted 
                        FROM users WHERE $sort";

        $result = DB::run($sql, ['params' => $params]);

        return $result->fetch(PDO::FETCH_ASSOC);
    }

    // Регистрация участника
    public static function createUser($data)
    {
        $data = [
            'user_login'                => $data['user_login'],
            'user_email'                => $data['user_email'],
            'user_template'             => $data['user_template'],
            'user_lang'                 => $data['user_lang'],
            'user_whisper'              => $data['user_whisper'],
            'user_password'             => $data['user_password'],
            'user_limiting_mode'        => $data['user_limiting_mode'],
            'user_activated'            => $data['user_activated'],
            'user_reg_ip'               => $data['user_reg_ip'],
            'user_trust_level'          => $data['user_trust_level'],
            'user_invitation_id'        => $data['user_invitation_id'],
        ];

        $sql = "INSERT INTO users(user_login, 
                                    user_email,
                                    user_template,
                                    user_lang,
                                    user_whisper,
                                    user_password, 
                                    user_limiting_mode, 
                                    user_activated, 
                                    user_reg_ip, 
                                    user_trust_level, 
                                    user_invitation_id) 
                                    
                            VALUES(:user_login, 
                                    :user_email, 
                                    :user_template,
                                    :user_lang,
                                    :user_whisper,
                                    :user_password, 
                                    :user_limiting_mode, 
                                    :user_activated, 
                                    :user_reg_ip, 
                                    :user_trust_level, 
                                    :user_invitation_id)";

        DB::run($sql, $data);

        $sql_last_id =  DB::run("SELECT LAST_INSERT_ID() as last_id")->fetch(PDO::FETCH_ASSOC);

        return $sql_last_id['last_id'];
    }

    // Просмотры  
    public static function userHits($user_id)
    {
        $sql = "UPDATE users SET user_hits_count = (user_hits_count + 1) WHERE user_id = :user_id";

        return  DB::run($sql, ['user_id' => $user_id]);
    }

    // TL - название
    public static function getUserTrust($user_id)
    {
        $sql = "SELECT 
                    user_id,
                    user_trust_level, 
                    trust_id,
                    trust_name                    
                        FROM users_trust_level
                        LEFT JOIN users ON user_trust_level = trust_id
                        WHERE user_id = :user_id";

        return DB::run($sql, ['user_id' => $user_id])->fetch(PDO::FETCH_ASSOC);
    }

    // Страница закладок участника (комментарии и посты)
    public static function userFavorite($user_id)
    {
        $sql = "SELECT 
                    favorite_id,
                    favorite_user_id, 
                    favorite_type,
                    favorite_tid,
                    user_id, 
                    user_login,
                    user_avatar, 
                    post_id,
                    post_title,
                    post_slug,
                    post_date,
                    post_answers_count,
                    answer_id,
                    answer_post_id,
                    answer_content
                        FROM favorites
                        LEFT JOIN users ON user_id = favorite_user_id
                        LEFT JOIN posts ON post_id = favorite_tid AND favorite_type = 1
                        LEFT JOIN answers ON answer_id = favorite_tid AND favorite_type = 2
                        WHERE favorite_user_id = :user_id ORDER BY favorite_id DESC LIMIT 100";

        return DB::run($sql, ['user_id' => $user_id])->fetchAll(PDO::FETCH_ASSOC);
    }


    // Страница закладок участника (комментарии и посты)
    public static function blogs($user_id)
    {
        $sql = "SELECT 
                    facet_id,
                    facet_title,
                    facet_description,
                    facet_short_description,
                    facet_info,
                    facet_slug,
                    facet_img,
                    facet_add_date,
                    facet_seo_title,
                    facet_user_id,
                    facet_count,
                    facet_type
                        FROM facets
                        WHERE facet_user_id = :user_id AND facet_type = 'blog'";

        return DB::run($sql, ['user_id' => $user_id])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Страница черновиков
    public static function userDraftPosts($user_id)
    {
        $sql = "SELECT
                   post_id,
                   post_title,
                   post_slug,
                   post_user_id,
                   post_draft,
                   post_is_deleted,
                   post_date,
                   user_id, 
                   user_login,
                   user_name,
                   user_avatar
                       FROM posts
                       LEFT JOIN users ON user_id = post_user_id
                           WHERE user_id = :user_id 
                           AND post_draft = 1 AND post_is_deleted = 0
                           ORDER BY post_id DESC";

        return DB::run($sql, ['user_id' => $user_id])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Информация участника
    public static function userInfo($email)
    {
        $sql = "SELECT 
                   user_id, 
                   user_email, 
                   user_password,
                   user_login,
                   user_name,
                   user_template,
                   user_lang,
                   user_avatar,
                   user_trust_level,
                   user_ban_list,
                   user_limiting_mode
                        FROM users 
                        WHERE user_email = :email";

        return DB::run($sql, ['email' => $email])->fetch(PDO::FETCH_ASSOC);
    }

    // Количество контента участника
    public static function contentCount($user_id)
    {
        $sql = "SELECT 
                    (SELECT COUNT(post_id) 
                        FROM posts 
                        WHERE post_user_id = :user_id and post_draft = 0 and post_is_deleted = 0) 
                            AS count_posts,
                  
                    (SELECT COUNT(answer_id) 
                        FROM answers 
                        WHERE answer_user_id = :user_id and answer_is_deleted = 0) 
                            AS count_answers,
                  
                    (SELECT COUNT(comment_id) 
                        FROM comments 
                        WHERE comment_user_id = :user_id and comment_is_deleted = 0) 
                            AS count_comments";

        return DB::run($sql, ['user_id' => $user_id])->fetch(PDO::FETCH_ASSOC);
    }

    // Находит ли пользователь в бан- листе
    public static function isBan($user_id)
    {
        $sql = "SELECT
                    banlist_user_id,
                    banlist_status
                        FROM users_banlist
                        WHERE banlist_user_id = :user_id AND banlist_status = 1";

        return DB::run($sql, ['user_id' => $user_id])->fetch(PDO::FETCH_ASSOC);
    }

    // Находит ли пользователь в бесшумном режиме
    public static function isLimitingMode($user_id)
    {
        $sql = "SELECT
                    user_id,
                    user_limiting_mode
                        FROM users
                        WHERE user_id = :user_id AND user_limiting_mode = 1";

        return DB::run($sql, ['user_id' => $user_id])->fetch(PDO::FETCH_ASSOC);
    }

    // Активирован ли пользователь (e-mail)
    public static function isActivated($user_id)
    {
        $sql = "SELECT
                    user_id,
                    user_activated
                        FROM users
                        WHERE user_id = :user_id AND user_activated = 1";

        return DB::run($sql, ['user_id' => $user_id])->fetch(PDO::FETCH_ASSOC);
    }

    // Восстановления пароля
    public static function initRecover($user_id, $code)
    {
        $params = [
            'activate_date'     => date('Y-m-d H:i:s'),
            'activate_user_id'  => $user_id,
            'activate_code'     => $code,
        ];

        $sql = "INSERT INTO users_activate(activate_date, activate_user_id, activate_code) 
                       VALUES(:activate_date, :activate_user_id, :activate_code)";

        return DB::run($sql, $params);
    }

    // Для одноразового использования кода восстановления
    public static function editRecoverFlag($user_id)
    {
        $sql = "UPDATE users_activate SET activate_flag = 1 WHERE activate_user_id = :user_id";

        return DB::run($sql, ['user_id' => $user_id]);
    }

    // Проверяем код смены пароля (использовали его или нет)
    public static function getPasswordActivate($code)
    {
        $sql = "SELECT
                    activate_id,
                    activate_date,
                    activate_user_id,
                    activate_code,
                    activate_flag
                        FROM users_activate
                        WHERE activate_code = :code AND activate_flag != 1";

        return DB::run($sql, ['code' => $code])->fetch(PDO::FETCH_ASSOC);
    }

    // Делаем запись в таблицу активации e-mail
    public static function sendActivateEmail($user_id, $email_code)
    {
        $params = [
            'pubdate'       => date("Y-m-d H:i:s"),
            'user_id'       => $user_id,
            'email_code'    => $email_code,
        ];

        $sql = "INSERT INTO users_email_activate(pubdate, user_id, email_code) 
                       VALUES(:pubdate, :user_id, :email_code)";

        return DB::run($sql, $params);
    }

    // Проверяем код активации e-mail
    public static function getEmailActivate($code)
    {
        $sql = "SELECT
                    user_id,
                    email_code,
                    email_activate_flag
                        FROM users_email_activate
                        WHERE email_code = :code AND email_activate_flag != :flag";

        return DB::run($sql, ['code' => $code, 'flag' => 1])->fetch(PDO::FETCH_ASSOC);
    }

    // Активируем e-mail
    public static function EmailActivate($user_id)
    {
        $sql = "UPDATE users_email_activate SET email_activate_flag = :flag WHERE user_id = :user_id";

        DB::run($sql, ['user_id' => $user_id, 'flag' => 1]);

        $sql = "UPDATE users SET user_activated = :flag WHERE user_id = :user_id";

        return DB::run($sql, ['user_id' => $user_id, 'flag' => 1]);
    }
}
