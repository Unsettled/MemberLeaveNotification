<?php

define('WORKING_DIR', __DIR__);
require(WORKING_DIR . '/src/boot.php');

// TODO: make singleton configuration class
$notifications = array(
    new \Notification\Type(16, "MemberApplication", "applied to join", "unset-internal-affair"),
    // This does nothing as only the player that applied receives it.
    new \Notification\Type(18, "MemberAccepted", "application accepted", "unset-internal-affair"),
    new \Notification\Type(21, "MemberLeft", "left corp", "unset-internal-affair"),
);

$tracker = new \Notification\Tracker();
$tracker->Execute($notifications);
