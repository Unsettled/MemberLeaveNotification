<?php

$files = array(
    WORKING_DIR . '/src/Config.php',
    WORKING_DIR . '/src/lib/AutoLoader.php',
    WORKING_DIR . '/vendor/autoload.php'
);

foreach ($files as $file) {
    if (file_exists($file) && is_readable($file)) {
        require_once $file;
    } else {
        exit("Unable to load required file $file.");
    }
}

spl_autoload_register('\\AutoLoader\\AutoLoader::Load');
