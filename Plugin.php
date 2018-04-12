<?php

namespace Kanboard\Plugin\Chat;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Translator;
use Kanboard\Plugin\Chat\Model\ChatMessageModel;

class Plugin extends Base
{
    public function initialize()
    {
        $this->hook->on('template:layout:js', array('template' => 'plugins/Chat/Assets/chat.js'));
        $this->hook->on('template:layout:css', array('template' => 'plugins/Chat/Assets/chat.css'));
        $this->helper->hook->attach('template:config:application', 'Chat:config/application');

        $this->helper->hook->attach('template:layout:bottom', 'Chat:layout/bottom', array(
            'last_message_id' => ChatMessageModel::getInstance($this->container)->getLastMessageId()
        ));

        $this->helper->register('chat', '\Kanboard\Plugin\Chat\Helper\ChatHelper');
    }

    public function onStartup()
    {
        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__.'/Locale');
    }

    public function getClasses()
    {
        return array(
            'Plugin\Chat\Model' => array(
                'ChatMessageModel',
                'ChatUserModel',
            )
        );
    }

    public function getPluginName()
    {
        return 'Chat';
    }

    public function getPluginDescription()
    {
        return t('Minimalist Chat for Kanboard.');
    }

    public function getPluginAuthor()
    {
        return 'Frédéric Guillot';
    }

    public function getPluginVersion()
    {
        return '1.0.3';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/kanboard/plugin-chat';
    }

    public function getCompatibleVersion()
    {
        return '>=1.2.3';
    }
}
