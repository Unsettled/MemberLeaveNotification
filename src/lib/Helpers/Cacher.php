<?php

namespace Helpers;


class Cacher
{
    public $cachePath = WORKING_DIR . '/tmp/cache/';
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
        $filePath = $this->CleanFilename($filename);
        $data = json_encode($array);
        file_put_contents($filePath, $data);
    }

    public function LoadArray($filename)
    {
        $filePath = $this->CleanFilename($filename);
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

    private function CleanFilename($filename)
    {
        // Replace spaces with dashes
        $filename = str_replace(' ', '-', $filename);
        // Replace multiple dashes with a single dash
        $filename = preg_replace('/-+/', '-', $filename);
        // Strip any non-alphanumeric character or dash
        $filename = preg_replace('/[^[:alnum:]\-]/', '', $filename);

        return "{$this->cachePath}$filename.cache";
    }
}
