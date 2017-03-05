<form method="post" autocomplete="off" action="<?= $this->url->href('ChatController', 'create', array('plugin' => 'Chat')) ?>" id="chat-form">
    <?= $this->form->csrf() ?>
    <?= $this->form->text('message') ?>
</form>