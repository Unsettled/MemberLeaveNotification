<?php

namespace Notification;


class Type
{
    /** @var int Type ID */
    public $ID;
    /** @var string Short name for use in files */
    public $name;
    /** @var string Text to send with message */
    public $messageText;
    /** @var string Channel to send message to */
    public $channel;

    function __construct($ID, $name, $messageText, $channel)
    {
        $this->ID = $ID;
        $this->name = $name;
        $this->messageText = $messageText;
        $this->channel = $channel;
    }
}
