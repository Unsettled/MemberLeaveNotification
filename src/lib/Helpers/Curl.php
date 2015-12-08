<?php

namespace Helpers;


class Curl
{
    /**
     * Helper function to retrieve data with cURL.
     *
     * @param $url
     * @return string
     */
    public static function GetData($url)
    {
        if (empty($url)) {
            exit("Link given was empty. Check your configuration.");
        }

        $cache = new Cacher();
        $data = $cache->LoadData($url);

        if (!empty($data)) return $data;

        if (!function_exists("curl_init")) {
            exit("You don't appear to have cURL installed or configured.");
        }

        $CH = curl_init();
        curl_setopt($CH, CURLOPT_URL, $url);
        curl_setopt($CH, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($CH, CURLOPT_USERAGENT, USER_AGENT);
        curl_setopt($CH, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($CH, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($CH, CURLOPT_RETURNTRANSFER, true);

        $data = curl_exec($CH);
        curl_close($CH);

        if (empty($data)) {
            exit("Data downloaded was empty!");
        }

        $cache->SaveData($data, $url);

        return $data;
    }

    /**
     * Helper function to send data with cURL.
     *
     * @param $url
     * @param $data
     */
    public static function SendData($url, $data)
    {
        // TODO
    }
}
