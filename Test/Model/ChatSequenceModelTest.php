<?php

use Kanboard\Core\Plugin\Loader;
use Kanboard\Model\UserModel;
use Kanboard\Plugin\Chat\Model\ChatMessageModel;
use Kanboard\Plugin\Chat\Model\ChatSequenceModel;

require_once 'tests/units/Base.php';

class ChatSequenceModelTest extends Base
{
    public function setUp()
    {
        parent::setUp();
        $plugin = new Loader($this->container);
        $plugin->scan();
    }

    public function testGetLastPosition()
    {
        $chatSequenceModel = new ChatSequenceModel($this->container);
        $this->assertSame(0, $chatSequenceModel->getLastPosition(1));
        $this->assertTrue($chatSequenceModel->setLastPosition(1, 2));
        $this->assertSame(2, $chatSequenceModel->getLastPosition(1));
    }

    public function testCountUnread()
    {
        $chatSequenceModel = new ChatSequenceModel($this->container);
        $chatMessageModel = new ChatMessageModel($this->container);
        $userModel = new UserModel($this->container);

        $this->assertNotFalse($userModel->create(array('username' => 'test')));

        $this->assertSame(0, $chatSequenceModel->countUnreadMessages(1));

        $this->assertEquals(1, $chatMessageModel->create(1, 'test1'));
        $this->assertEquals(2, $chatMessageModel->create(1, 'test2'));

        $this->assertSame(0, $chatSequenceModel->countUnreadMessages(1));
        $this->assertSame(2, $chatSequenceModel->countUnreadMessages(2));

        $this->assertTrue($chatSequenceModel->setLastPosition(2, 1));
        $this->assertSame(1, $chatSequenceModel->countUnreadMessages(2));

        $this->assertTrue($chatSequenceModel->setLastPosition(2, 2));
        $this->assertSame(0, $chatSequenceModel->countUnreadMessages(2));
    }
}