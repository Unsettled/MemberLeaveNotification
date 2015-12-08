<?php

$files = array(
    'src/Config.php',
    'src/lib/AutoLoader.php',
    'vendor/autoload.php'
);

foreach ($files as $file) {
    if (file_exists($file) && is_readable($file)) {
        require_once $file;
    } else {
        exit("Unable to load required file $file.");
    }
}

spl_autoload_register('\\AutoLoader\\AutoLoader::Load');
