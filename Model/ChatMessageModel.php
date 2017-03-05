<?php

namespace Kanboard\Plugin\Chat\Model;

use Kanboard\Core\Base;
use Kanboard\Model\UserModel;

/**
 * Class ChatMessageModel
 *
 * @package  Kanboard\Plugin\Chat\Model
 * @author   Frederic Guillot
 *
 * @property \Kanboard\Plugin\Chat\Model\ChatUserModel $chatUserModel
 */
class ChatMessageModel extends Base
{
    const TABLE = 'chat_messages';
    const MAX_MESSAGES = 1000;

    /**
     * Add a new message
     *
     * @param  integer $userId
     * @param  string  $message
     * @return bool|int
     */
    public function create($userId, $message)
    {
        $messageId = $this->db->table(self::TABLE)->persist(array(
            'user_id'       => $userId,
            'message'       => $message,
            'creation_date' => time(),
        ));

        if ($messageId > 0) {
            $this->chatUserModel->setLastPosition($userId, $messageId);
        }

        $this->cleanup(self::MAX_MESSAGES);

        return $messageId;
    }

    /**
     * Get last messages for a given user
     *
     * @param  integer $userId
     * @param  integer $limit
     * @return array
     */
    public function getMessages($userId, $limit = 50)
    {
        $position = $this->chatUserModel->getLastPosition($userId);
        $records = $this->db->table(self::TABLE)
            ->columns(
                self::TABLE.'.id',
                self::TABLE.'.creation_date',
                self::TABLE.'.message',
                self::TABLE.'.user_id',
                UserModel::TABLE.'.username',
                UserModel::TABLE.'.name',
                UserModel::TABLE.'.email',
                UserModel::TABLE.'.avatar_path'
            )
            ->join(UserModel::TABLE, 'id', 'user_id')
            ->desc(self::TABLE.'.id')
            ->limit($limit)
            ->findAll();

        foreach ($records as &$record) {
            $record['unread'] = $record['id'] > $position;
        }

        if (count($records) > 0) {
            $this->chatUserModel->setLastPosition($userId, $records[0]['id']);
        }

        asort($records);
        return $records;
    }

    /**
     * Check if the user has unseen messages
     *
     * @param  integer $messageId
     * @return bool
     */
    public function hasUnseenMessages($messageId)
    {
        return $this->db->table(self::TABLE)->gt('id', $messageId)->count() > 0;
    }

    /**
     * Get last messageID from the database
     *
     * @return integer
     */
    public function getLastMessageId()
    {
        return (int) $this->db->table(self::TABLE)->desc('id')->findOneColumn('id');
    }

    /**
     * Remove old messages to avoid large table
     *
     * @param  integer $max
     */
    public function cleanup($max)
    {
        $total = $this->db->table(self::TABLE)->count();

        if ($total > $max) {
            $ids = $this->db->table(self::TABLE)->asc('id')->limit($total - $max)->findAllByColumn('id');

            if (! empty($ids)) {
                $this->db->table(self::TABLE)->in('id', $ids)->remove();
            }
        }
    }
}
