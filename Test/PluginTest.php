<?php

require_once 'tests/units/Base.php';

use Kanboard\Plugin\Chat\Plugin;

class PluginTest extends Base
{
    public function testPlugin()
    {
        $plugin = new Plugin($this->container);
        $this->assertNotEmpty($plugin->getPluginName());
        $this->assertNotEmpty($plugin->getPluginDescription());
        $this->assertNotEmpty($plugin->getPluginAuthor());
        $this->assertNotEmpty($plugin->getPluginVersion());
        $this->assertNotEmpty($plugin->getPluginHomepage());
    }
}
