<?php

use Kanboard\Model\UserModel;
use Kanboard\Plugin\Chat\Model\ChatMessageModel;
use Kanboard\Plugin\Chat\Model\ChatUserModel;

require_once __DIR__.'/BaseModelTest.php';

class ChatUserModelTest extends BaseModelTest
{
    public function testGetLastPosition()
    {
        $chatUserModel = new ChatUserModel($this->container);
        $this->assertSame(0, $chatUserModel->getLastPosition(1));
        $this->assertTrue($chatUserModel->setLastPosition(1, 2));
        $this->assertSame(2, $chatUserModel->getLastPosition(1));
    }

    public function testCountUnread()
    {
        $chatUserModel = new ChatUserModel($this->container);
        $chatMessageModel = new ChatMessageModel($this->container);
        $userModel = new UserModel($this->container);

        $this->assertNotFalse($userModel->create(array('username' => 'test')));

        $this->assertSame(0, $chatUserModel->countUnreadMessages(1));

        $this->assertEquals(1, $chatMessageModel->create(1, 'test1'));
        $this->assertEquals(2, $chatMessageModel->create(1, 'test2'));

        $this->assertSame(0, $chatUserModel->countUnreadMessages(1));
        $this->assertSame(2, $chatUserModel->countUnreadMessages(2));

        $this->assertTrue($chatUserModel->setLastPosition(2, 1));
        $this->assertSame(1, $chatUserModel->countUnreadMessages(2));

        $this->assertTrue($chatUserModel->setLastPosition(2, 2));
        $this->assertSame(0, $chatUserModel->countUnreadMessages(2));
    }

    public function testSetUserMention()
    {
        $chatUserModel = new ChatUserModel($this->container);
        $this->assertTrue($chatUserModel->setUserMention('admin'));
        $this->assertFalse($chatUserModel->setUserMention('notfound'));
    }

    public function testGetMentionedUsers()
    {
        $chatUserModel = new ChatUserModel($this->container);

        $users = $chatUserModel->getMentionedUsers('@admin this is a message');
        $this->assertEquals(array('admin'), $users);

        $users = $chatUserModel->getMentionedUsers('Hey @firstname.lastname this is a message');
        $this->assertEquals(array('firstname.lastname'), $users);

        $users = $chatUserModel->getMentionedUsers('Hey @firstname.lastname, this is a message for @admin');
        $this->assertEquals(array('firstname.lastname', 'admin'), $users);
    }

    public function testCreateUserMentions()
    {
        $chatUserModel = new ChatUserModel($this->container);
        $chatUserModel->createUserMentions('Hey @firstname.lastname, this is a message for @admin', 'foobar');

        $this->assertTrue($chatUserModel->hasUserMention(1));
        $this->assertFalse($chatUserModel->hasUserMention(2));

        $this->assertTrue($chatUserModel->unsetUserMention(1));
        $this->assertFalse($chatUserModel->hasUserMention(1));
    }

    public function testCreateUserMentionsWithSelfMention()
    {
        $chatUserModel = new ChatUserModel($this->container);
        $chatUserModel->createUserMentions('Hey @firstname.lastname, this is a message for @admin', 'admin');

        $this->assertFalse($chatUserModel->hasUserMention(1));
        $this->assertFalse($chatUserModel->hasUserMention(2));
    }
}
