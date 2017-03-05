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

        return $messageId;
    }

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

    public function hasUnseenMessages($messageId)
    {
        return $this->db->table(self::TABLE)->gt('id', $messageId)->count() > 0;
    }

    public function getLastMessageId()
    {
        return (int) $this->db->table(self::TABLE)->desc('id')->findOneColumn('id');
    }
}
