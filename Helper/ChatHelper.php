<?php

namespace Kanboard\Plugin\Chat\Helper;

use Kanboard\Core\Base;

/**
 * Class ChatHelper
 *
 * @package Kanboard\Plugin\Chat\Helper
 * @author  Frederic Guillot
 */
class ChatHelper extends Base
{
    public function markdown($text)
    {
        $parser = new ChatMarkdown($this->container, false);
        $parser->setMarkupEscaped(true);
        return $parser->text($text);
    }
}
