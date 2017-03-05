<?php

namespace Kanboard\Plugin\Chat\Model;

use Kanboard\Core\Base;

/**
 * Class ChatUserModel
 *
 * @package Kanboard\Plugin\Chat\Model
 * @author  Frederic Guillot
 */
class ChatUserModel extends Base
{
    const TABLE = 'chat_users';

    public function getLastPosition($userId)
    {
        return (int) $this->db->table(self::TABLE)->eq('user_id', $userId)->findOneColumn('message_id') ?: 0;
    }

    public function setLastPosition($userId, $messageId)
    {
        $this->create($userId);

        return $this->db->table(self::TABLE)
            ->eq('user_id', $userId)
            ->update(array('message_id' => $messageId));
    }

    public function hasUserMention($userId)
    {
        return $this->db->table(self::TABLE)->eq('user_id', $userId)->findOneColumn('mentioned') == 1;
    }

    public function unsetUserMention($userId)
    {
        return $this->db->table(self::TABLE)
            ->eq('user_id', $userId)
            ->update(array('mentioned' => 0));
    }

    public function setUserMention($username)
    {
        $userId = $this->userModel->getIdByUsername($username);

        if ($userId > 0) {
            $this->create($userId);

            return $this->db->table(self::TABLE)
                ->eq('user_id', $userId)
                ->update(array('mentioned' => 1));
        }

        return false;
    }

    public function getMentionedUsers($message)
    {
        $users = array();

        if (preg_match_all('/@([^\s,!:?]+)/', $message, $matches)) {
            array_walk($matches[1], function (&$username) { $username = rtrim($username, '.'); });
            $users = array_unique($matches[1]);
        }

        return $users;
    }

    public function createUserMentions($message, $currentUserName)
    {
        $users = $this->getMentionedUsers($message);

        foreach ($users as $username) {
            if ($currentUserName !== $username) {
                $this->setUserMention($username);
            }
        }
    }

    public function countUnreadMessages($userId)
    {
        $position = $this->getLastPosition($userId);
        return $this->db->table(ChatMessageModel::TABLE)->gt('id', $position)->count();
    }

    public function create($userId)
    {
        if (! $this->db->table(self::TABLE)->eq('user_id', $userId)->exists()) {
            $this->db->table(self::TABLE)->insert(array('user_id' => $userId));
        }
    }
}
