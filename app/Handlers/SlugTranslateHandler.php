<?php

namespace App\Handlers;

use GuzzleHttp\Client;
use Overtrue\Pinyin\Pinyin;

class SlugTranslateHandler
{
    private $api;       //百度翻译api
    private $from;      //源语言
    private $to;        //目标语言
    private $appid;     //百度翻译appid
    private $key;       //百度翻译key
    private $text;      //要翻译的文本

    public function __construct($text, $from = '', $to = '')
    {
        // 初始化配置信息
        $this->api = 'http://api.fanyi.baidu.com/api/trans/vip/translate?';
        $this->appid = config('services.baidu_translate.appid');
        $this->key = config('services.baidu_translate.key');
        $this->text = $text;
        $this->from = $from ? $from : 'zh';
        $this->to = $to ? $to : 'en';
    }

    public function translate()
    {
        // 实例化 HTTP 客户端
        $http = new Client;

        // 如果没有配置百度翻译，自动使用兼容的拼音方案
        if (empty($this->appid) || empty($this->key)) {
            return $this->pinyin($this->text);
        }

        // 根据文档，生成 sign
        // http://api.fanyi.baidu.com/api/trans/product/apidoc
        // appid+q+salt+密钥 的MD5值
        $salt = time();
        $sign = md5($this->appid. $this->text . $salt . $this->key);

        // 构建请求参数
        $query = http_build_query([
            "q"     =>  $this->text,
            "from"  => "zh",
            "to"    => "en",
            "appid" => $this->appid,
            "salt"  => $salt,
            "sign"  => $sign,
        ]);

//        dd($query);

        // 发送 HTTP Get 请求
        $response = $http->get($this->api.$query);

        $result = json_decode($response->getBody(), true);

        /**
        获取结果，如果请求成功，dd($result) 结果如下：

        array:3 [▼
        "from" => "zh"
        "to" => "en"
        "trans_result" => array:1 [▼
        0 => array:2 [▼
        "src" => "XSS 安全漏洞"
        "dst" => "XSS security vulnerability"
        ]
        ]
        ]

         **/

        // 尝试获取获取翻译结果
        if (isset($result['trans_result'][0]['dst'])) {
            if($result['trans_result'][0]['dst'] == 'edit') {
                return 'edits';
            }
            return str_slug($result['trans_result'][0]['dst']);
        } else {
            // 如果百度翻译没有结果，使用拼音作为后备计划。
            $val = str_slug(app(Pinyin::class)->permalink($this->text));
            if($val == 'edit') {
                return 'edits';
            }
            return $val;
        }
    }
}