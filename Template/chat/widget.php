<div id="chat-widget-messages-container">
    <?= $this->render('Chat:chat/messages', array('messages' => $messages)) ?>
</div>
<?= $this->render('Chat:chat/form') ?>