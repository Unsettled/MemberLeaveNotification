<?php

namespace Helpers;


class Cacher
{
    public $cachePath = '../tmp/cache/';
    public $cacheTime = 3600;

    function __construct()
    {
        if (!file_exists($this->cachePath)) {
            if (!mkdir($this->cachePath, 0755, true)) {
                exit("Failed to create cache folder.");
            }
        }
    }

    public function SaveData($data, $filename)
    {
        $filePath = $this->CleanFilename($filename);
        file_put_contents($filePath, $data);
    }

    public function LoadData($filename)
    {
        $filePath = $this->CleanFilename($filename);

        if (!self::IsCached($filePath)) {
            return false;
        }

        return file_get_contents($filePath);
    }

    public function SaveArray($array, $filename)
    {
        $filePath = $this->CleanFilename($filename, false);
        $data = json_encode($array);
        file_put_contents($filePath, $data);
    }

    public function LoadArray($filename)
    {
        $filePath = $this->CleanFilename($filename, false);
        if (!is_readable($filePath)) {
            return false;
        }

        $contents = file_get_contents($filePath);
        if ($contents === false) {
            return false;
        }

        return json_decode($contents, true);
    }

    private function IsCached($filePath)
    {
        if (file_exists($filePath) && (filemtime($filePath) + $this->cacheTime >= time())) {
            return true;
        }

        return false;
    }

    private function CleanFilename($filename, $hash = true)
    {
        if ($hash) return $this->cachePath . md5($filename) . ".cache";

        // Replace spaces with dashes
        $string = str_replace(' ', '-', $filename);
        // Replace multiple dashes with a single dash
        $string = preg_replace('/-+/', '-', $string);
        // Strip any non-alphanumeric character or dash
        $string = preg_replace('/[^[:alnum:]\-]/', '', $string);

        return $this->cachePath . $string . ".cache";
    }
}
