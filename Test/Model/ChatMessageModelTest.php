<?php

use Kanboard\Plugin\Chat\Model\ChatMessageModel;
use Kanboard\Plugin\Chat\Model\ChatUserModel;

require_once __DIR__.'/BaseModelTest.php';

class ChatMessageModelTest extends BaseModelTest
{
    public function testCreate()
    {
        $chatMessageModel = new ChatMessageModel($this->container);
        $this->assertEquals(1, $chatMessageModel->create(1, 'test'));
    }

    public function testGetLastMessageId()
    {
        $chatMessageModel = new ChatMessageModel($this->container);
        $this->assertSame(0, $chatMessageModel->getLastMessageId());
        $this->assertEquals(1, $chatMessageModel->create(1, 'test'));
        $this->assertSame(1, $chatMessageModel->getLastMessageId());
    }

    public function testHasUnseenMessages()
    {
        $chatMessageModel = new ChatMessageModel($this->container);
        $this->assertEquals(1, $chatMessageModel->create(1, 'test'));
        $this->assertTrue($chatMessageModel->hasUnseenMessages(0));
        $this->assertFalse($chatMessageModel->hasUnseenMessages(1));
        $this->assertFalse($chatMessageModel->hasUnseenMessages(2));
    }

    public function testGetMessages()
    {
        $chatMessageModel = new ChatMessageModel($this->container);
        $chatUserModel = new ChatUserModel($this->container);

        $this->assertEquals(1, $chatMessageModel->create(1, 'test1'));
        $this->assertEquals(2, $chatMessageModel->create(1, 'test2'));

        $messages = $chatMessageModel->getMessages(1);

        $this->assertCount(2, $messages);
        $this->assertEquals(2, $messages[0]['id']);
        $this->assertEquals('test2', $messages[0]['message']);
        $this->assertEquals(1, $messages[1]['id']);
        $this->assertEquals(1, $messages[1]['user_id']);
        $this->assertEquals('admin', $messages[1]['username']);
        $this->assertEquals('', $messages[1]['name']);

        $this->assertEquals(2, $chatUserModel->getLastPosition(1));
    }
}
