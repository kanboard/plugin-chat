<?php

use Kanboard\Core\Plugin\Loader;

require_once 'tests/units/Base.php';

abstract class BaseModelTest extends Base
{
    public function setUp()
    {
        parent::setUp();

        $plugin = new Loader($this->container);
        $plugin->initializePlugin('Chat');
    }
}
