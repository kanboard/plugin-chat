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

    /**
     * Get last read messageId for a given user
     *
     * @param  int $userId
     * @return int
     */
    public function getLastPosition($userId)
    {
        return (int) $this->db->table(self::TABLE)->eq('user_id', $userId)->findOneColumn('message_id') ?: 0;
    }

    /**
     * Set read position for a user
     *
     * @param  int $userId
     * @param  int $messageId
     * @return bool
     */
    public function setLastPosition($userId, $messageId)
    {
        $this->create($userId);

        return $this->db->table(self::TABLE)
            ->eq('user_id', $userId)
            ->update(array('message_id' => $messageId));
    }

    /**
     * Check if the given user is mentioned
     *
     * @param  int $userId
     * @return bool
     */
    public function hasUserMention($userId)
    {
        return $this->db->table(self::TABLE)->eq('user_id', $userId)->findOneColumn('mentioned') == 1;
    }

    /**
     * Acknowledge a mentioned user
     *
     * @param  int $userId
     * @return bool
     */
    public function unsetUserMention($userId)
    {
        return $this->db->table(self::TABLE)
            ->eq('user_id', $userId)
            ->update(array('mentioned' => 0));
    }

    /**
     * Set mention flag if username exists
     *
     * @param  string $username
     * @return bool
     */
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

    /**
     * Parse message to get mentioned users
     *
     * @param  string $message
     * @return array
     */
    public function getMentionedUsers($message)
    {
        $users = array();

        if (preg_match_all('/@([^\s,!:?]+)/', $message, $matches)) {
            array_walk($matches[1], function (&$username) { $username = rtrim($username, '.'); });
            $users = array_unique($matches[1]);
        }

        return $users;
    }

    /**
     * Parse message and ignore mention for connected user
     *
     * @param string $message
     * @param string $currentUserName
     */
    public function createUserMentions($message, $currentUserName)
    {
        $users = $this->getMentionedUsers($message);

        foreach ($users as $username) {
            if ($currentUserName !== $username) {
                $this->setUserMention($username);
            }
        }
    }

    /**
     * Get unread counter for a given user
     *
     * @param  int $userId
     * @return int
     */
    public function countUnreadMessages($userId)
    {
        $position = $this->getLastPosition($userId);
        return $this->db->table(ChatMessageModel::TABLE)->gt('id', $position)->count();
    }

    /**
     * Create user record if missing
     *
     * @param int $userId
     */
    public function create($userId)
    {
        if (! $this->db->table(self::TABLE)->eq('user_id', $userId)->exists()) {
            $this->db->table(self::TABLE)->insert(array('user_id' => $userId));
        }
    }
}
