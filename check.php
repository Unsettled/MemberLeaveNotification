<?php

define('WORKING_DIR', __DIR__);
require(WORKING_DIR . '/src/boot.php');

$tracker = new \Notifier\Tracker();
$tracker->Execute();
