<?php
/**
 * Created by PhpStorm.
 * User: Jarvan
 * Date: 20/3/27
 * Time: 10:18.
 */

namespace AlibabaAllianceSdk\Util;

class RequestUtil
{
    /**
     * 协程模式请求
     *
     * @param $url
     * @param array $postData
     *
     * @return mixed
     */
    public static function coroutineSend($url, $postData = [])
    {
        //普通fpm
        if (!extension_loaded('swoole') || PHP_SAPI != 'cli') {
            $output = self::fpmCurlSend($url, $postData);

            return $output;
        }

        //协程
        $urlsInfo = \parse_url($url);
        $path     = $urlsInfo['path'];
        if (isset($urlsInfo['query'])) {
            $path .= '?' . $urlsInfo['query'];
        }
        $domain = $urlsInfo['host'];
        if (isset($urlsInfo['port'])) {
            $port = $urlsInfo['port'];
        } else {
            $port = ('https' == $urlsInfo['scheme'] ? 443 : 80);
        }
        $chan = new \Swoole\Coroutine\Channel(1);
        go(function () use ($chan, $domain, $path, $postData, $port) {
            $client = new \Swoole\Coroutine\Http\Client($domain, $port, 443 == $port ? true : false);
            $client->set(['timeout' => 15]);
            $client->post($path, $postData);
            $output = $client->body;
            $chan->push($output);
            $client->close();
        });
        $output = $chan->pop();

        return $output;
    }

    /**
     * fpm模式请求.
     *
     * @param $url
     * @param array $postData
     *
     * @return mixed
     */
    public static function fpmCurlSend($url, $postData = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);

        return $data;
    }
}
