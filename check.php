<?php

define('WORKING_DIR', __DIR__);
require(WORKING_DIR . '/src/boot.php');

$myTypes = new \Notification\Storage();
$tracker = new \Notification\Tracker();
$tracker->Execute($myTypes->getTypes());
