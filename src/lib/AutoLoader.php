<?php

namespace AutoLoader;


class AutoLoader {
    protected static $srcPath = "../src/lib/";

    public static function Load($class) {
        $classPath = str_replace("\\", DIRECTORY_SEPARATOR, $class);
        if (is_file(self::$srcPath . $classPath . ".php")) {
            include self::$srcPath . $classPath . ".php";
        }
    }
}
