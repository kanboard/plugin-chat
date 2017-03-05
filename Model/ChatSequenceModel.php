<?php

namespace Kanboard\Plugin\Chat\Model;

use Kanboard\Core\Base;

/**
 * Class ChatSequenceModel
 *
 * @package Kanboard\Plugin\Chat\Model
 * @author  Frederic Guillot
 */
class ChatSequenceModel extends Base
{
    const TABLE = 'chat_sequences';

    public function getLastPosition($userId)
    {
        return (int) $this->db->table(self::TABLE)->eq('user_id', $userId)->findOneColumn('message_id') ?: 0;
    }

    public function setLastPosition($userId, $messageId)
    {
        return $this->db->hashtable(self::TABLE)->columnKey('user_id')->columnValue('message_id')->put(array(
            $userId => $messageId
        ));
    }

    public function countUnreadMessages($userId)
    {
        $position = $this->getLastPosition($userId);
        return $this->db->table(ChatMessageModel::TABLE)->gt('id', $position)->count();
    }
}
