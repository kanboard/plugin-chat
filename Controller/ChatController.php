<?php

namespace Kanboard\Plugin\Chat\Controller;

use Kanboard\Controller\BaseController;

/**
 * Class ChatController
 *
 * @package Kanboard\Plugin\Chat\Controller
 * @author  Frederic Guillot
 * @property \Kanboard\Plugin\Chat\Model\ChatMessageModel  $chatMessageModel
 * @property \Kanboard\Plugin\Chat\Model\ChatSequenceModel $chatSequenceModel
 */
class ChatController extends BaseController
{
    public function create()
    {
        $values = $this->request->getValues();

        if (! empty($values['message'])) {
            $this->chatMessageModel->create($this->userSession->getId(), $values['message']);
        }

        $this->response->html($this->renderWidget());
    }

    public function show()
    {
        $this->response->html($this->renderWidget());
    }

    public function check()
    {
        $lastSeenMessageId = $this->request->getIntegerParam('lastMessageId');

        if ($this->chatMessageModel->hasUnseenMessages($lastSeenMessageId)) {
            $this->response->json(array(
                'lastMessageId' => $this->chatMessageModel->getLastMessageId(),
                'nbUnread'      => $this->chatSequenceModel->countUnreadMessages($this->userSession->getId()),
                'messages'      => $this->template->render('Chat:chat/messages', array(
                    'messages' => $this->chatMessageModel->getMessages($this->userSession->getId()),
                )),
            ));
        } else {
            $this->response->status(304);
        }
    }

    public function ping()
    {
        $lastSeenMessageId = $this->request->getIntegerParam('lastMessageId');

        if ($this->chatMessageModel->hasUnseenMessages($lastSeenMessageId)) {
            $this->response->json(array(
                'lastMessageId' => $this->chatMessageModel->getLastMessageId(),
                'nbUnread'      => $this->chatSequenceModel->countUnreadMessages($this->userSession->getId()),
            ));
        } else {
            $this->response->status(304);
        }
    }

    protected function renderWidget()
    {
        return $this->template->render('Chat:chat/widget', array(
            'messages' => $this->chatMessageModel->getMessages($this->userSession->getId()),
        ));
    }
}
