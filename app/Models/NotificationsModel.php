<?php

namespace App\Models;

use Hleb\Scheme\App\Models\MainModel;
use DB;
use PDO;

class NotificationsModel extends MainModel
{
    // $action_type
    // 1 - сообщение
    // 2 - пост
    // 3 - ответ
    // 4 - комментарий
    // 5 - пост в чат
    // 6 - понравился пост
    // 7 - понравился ответ
    // 10 - обращение в постах (@login)
    // 11 - в ответах (@login)
    // 12 - в комментариях (@login)
    // 15 - аудит
    // 20 - флаг система

    // Лист уведомлений
    public static function listNotification($user_id)
    {
        $sql = "SELECT
                    notification_id,
                    notification_sender_id,
                    notification_recipient_id,
                    notification_action_type,
                    notification_connection_type,
                    notification_url,
                    notification_add_time,
                    notification_read_flag,
                    notification_is_deleted,
                    user_id, 
                    user_login, 
                    user_avatar
                        FROM notifications
                        JOIN users ON user_id = notification_sender_id
                        WHERE notification_recipient_id = :user_id
                        ORDER BY notification_id DESC LIMIT 100";

        return DB::run($sql, ['user_id' => $user_id])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Уведомление
    public static function usersNotification($user_id)
    {
        $sql = "SELECT
                    notification_id,
                    notification_sender_id,
                    notification_recipient_id,
                    notification_action_type,
                    notification_connection_type,
                    notification_url,
                    notification_add_time,
                    notification_read_flag,
                    notification_is_deleted
                        FROM notifications
                        WHERE notification_recipient_id = :user_id
                        AND notification_read_flag = 0";

        return DB::run($sql, ['user_id' => $user_id])->fetch(PDO::FETCH_ASSOC);
    }

    // Отправка
    public static function send($sender_id, $recipient_id, $action_type, $connection_type, $url, $model_type = 0)
    {
        if (!$recipient_id) {
            return false;
        }

        $params = [
            'notification_sender_id'        => $sender_id,
            'notification_recipient_id'     => $recipient_id,
            'notification_action_type'      => $action_type,
            'notification_connection_type'  => $connection_type,
            'notification_url'              => $url,
            'notification_read_flag'        => 0,
        ];

        $sql = "INSERT INTO notifications(notification_sender_id, 
                                notification_recipient_id, 
                                notification_action_type, 
                                notification_connection_type, 
                                notification_url, 
                                notification_read_flag) 
                       VALUES(:notification_sender_id, 
                               :notification_recipient_id, 
                               :notification_action_type, 
                               :notification_connection_type, 
                               :notification_url, 
                               :notification_read_flag)";

        return DB::run($sql, $params);
    }

    // Кто подписан на данный вопрос / пост
    public static function getFocusUsersPost($post_id)
    {
        $sql = "SELECT
                    signed_post_id,
                    signed_user_id
                        FROM posts_signed
                        WHERE signed_post_id = :post_id";

        return DB::run($sql, ['post_id' => $post_id])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Список читаемых постов
    public static function getFocusPostUser($user_id)
    {
        $sql = "SELECT
                    signed_post_id,
                    signed_user_id 
                        FROM posts_signed
                        WHERE signed_user_id = :user_id";

        return DB::run($sql, ['user_id' => $user_id])->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getFocusPostsListUser($user_id)
    {
        $focus_posts = self::getFocusPostUser($user_id);

        $result = [];
        foreach ($focus_posts as $ind => $row) {
            $result[$ind] = $row['signed_post_id'];
        }

        if ($result) {
            $string = "WHERE post_id IN(" . implode(',', $result) . ") AND post_draft = 0";
        } else {
            $string = "WHERE post_id IN(0) AND post_draft = 0";
        }

        $sql = "SELECT 
                    post_id,
                    post_title,
                    post_slug,
                    post_type,
                    post_translation,
                    post_draft,
                    post_date,
                    post_published,
                    post_user_id,
                    post_votes,
                    post_answers_count,
                    post_comments_count,
                    post_content,
                    post_content_img,
                    post_thumb_img,
                    post_merged_id,
                    post_closed,
                    post_tl,
                    post_lo,
                    post_top,
                    post_url_domain,
                    post_is_deleted,
                    rel.*,
                    votes_post_item_id, votes_post_user_id,
                    user_id, user_login, user_avatar, 
                    favorite_tid, favorite_user_id, favorite_type
                    
                        FROM posts
                        LEFT JOIN
                        (
                            SELECT 
                                relation_post_id,

                                GROUP_CONCAT(facet_slug, '@', facet_title SEPARATOR '@') AS facet_list
                                FROM facets  
                                LEFT JOIN facets_posts_relation 
                                    on facet_id = relation_facet_id
                                GROUP BY relation_post_id
                        ) AS rel
                            ON rel.relation_post_id = post_id 

            INNER JOIN users ON user_id = post_user_id
            LEFT JOIN votes_post ON votes_post_item_id = post_id AND votes_post_user_id = :user_id
            LEFT JOIN favorites ON favorite_tid = post_id AND favorite_user_id = :user_id AND favorite_type = 1
            $string  LIMIT 100";

        return DB::run($sql, ['user_id' => $user_id])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Оповещение просмотрено
    public static function updateMessagesUnread($user_id, $notif_id)
    {
        $sql = "UPDATE notifications SET notification_read_flag = 1 WHERE notification_recipient_id = :user_id AND notification_id = :notif_id";

        return DB::run($sql, ['user_id' => $user_id, 'notif_id' => $notif_id]);
    }

    public static function getNotification($id)
    {
        $sql = "SELECT
                    notification_id,
                    notification_sender_id,
                    notification_recipient_id,
                    notification_action_type,
                    notification_connection_type,
                    notification_url,
                    notification_add_time,
                    notification_read_flag,
                    notification_is_deleted
                        FROM notifications
                            WHERE notification_id = :id";

        return DB::run($sql, ['id' => $id])->fetch(PDO::FETCH_ASSOC);
    }

    public static function setRemove($user_id)
    {
        $sql = "UPDATE notifications SET notification_read_flag = 1 
                        WHERE notification_recipient_id = :user_id";

        return DB::run($sql, ['user_id' => $user_id]);
    }
}
