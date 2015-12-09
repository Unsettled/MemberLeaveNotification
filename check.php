<?php

define('WORKING_DIR', __DIR__);
require(WORKING_DIR . '/src/boot.php');

// TODO: make singleton configuration class
$notifications = array(
    new \Notification\Type(16, "MemberApplication", "applied to join", "unset-internal-affair"),
    new \Notification\Type(128, "MemberJoined", "joined corp", "unset-internal-affair"),
    new \Notification\Type(21, "MemberLeft", "left corp", "unset-internal-affair"),
);

$tracker = new \Notification\Tracker();
$tracker->Execute($notifications);
