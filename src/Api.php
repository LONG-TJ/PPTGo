<?php

namespace ltj\aippt;

class Api
{
    private static $_instance = null;

    private $_client;
    private $_baseUrl = 'https://sdk.pptgo.cn/pptsdk/';
    private $_baseTestUrl = 'https://sdk-pre.pptgo.cn/pptsdk/';
    private $_clientId;
    private $_clientSecret;

    public function __construct(string $client_id, string $client_secret)
    {
        $this->_clientId = $client_id;
        $this->_clientSecret = $client_secret;

        $this->_client = new \GuzzleHttp\Client(['base_uri' => $this->_baseUrl]);
    }

    public static function getInstance(string $client_id, string $client_secret)
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($client_id, $client_secret);
        }

        return self::$_instance;
    }

    /**
     * 测试环境下调用
     */
    public function test()
    {
        $this->_client = new \GuzzleHttp\Client(['base_uri' => $this->_baseTestUrl]);
        return $this;
    }

    /**
     * 获取应⽤级access_token
     * 集成⽅获取应⽤级 PPTGO SDK的授权访问凭证access_token
     * 
     * @param string $scope 授权范围（all_scopes）
     */
    public function appToken(string $scope = 'all_scopes')
    {
        $response = $this->_client->request('POST', 'oauth/token', [
            'headers' => [
                'Content-Type' => 'multipart/form-data'
            ],
            'body' => [
                'grant_type'    => 'client_credentials',
                'scope'         => $scope,
                'client_id'     => $this->_clientId,
                'client_secret' => $this->_clientSecret
            ]
        ]);

        print_r($response);exit;

        // // TODO
        print_r($body = $response->getBody());exit;
        // return $access_token 、 token_type;
    }

    /**
     * 获取⽂件级access_token
     * 接⼊⽅获取⽂件级access_token后，⽤于提供给 PPTGO 编辑器SDK鉴权使⽤
     * ⽂件级access_token，接⼊⽅可根据⾃⾝业务决定授予什么操作权限，在PPTGO SDK编辑器中，⽬前共分两种⻆⾊：编辑者、查看者。 授予编辑者权限，可传⼊scope: "file.edit file.view"； 授予查看者权限，则传⼊scope: "file.view"...
     * 
     * @param string $file_key 文件key
     * @param string $scope 授权范围（file.edit file.view）
     * @param string $user_id 用户id
     * @param string $nick_name 用户昵称
     * @param string $avatar_url 当前登录⽤⼾头像链接
     */
    public function fileToken(string $file_key, string $scope, string $user_id, string $nick_name, string $avatar_url)
    {
        $response = (new \GuzzleHttp\Client())->request('POST', "{$this->_baseUrl}/oauth/token", [
            'headers' => [
                'Content-Type' => 'multipart/form-data'
            ],
            'query' => [
                'grant_type'    => 'client_credentials',
                'file_key'      => $file_key,
                'scope'         => $scope,
                'client_id'     => $this->_clientId,
                'client_secret' => $this->_clientSecret,
                'user_id'       => $user_id,
                'nick_name'     => $nick_name,
                'avatar_url'    => $avatar_url,
            ]
        ]);

        // // TODO
        // print_r($body = $response->getBody());
        // return $access_token 、 token_type;
    }

    /**
     * 创建文件
     * 创建⼀个空⽩⽂档
     * scope: file.create
     * 
     * @param string $access_token
     * @param string $token_type token类型，bearer
     */
    public function createFile(string $access_token, string $token_type)
    {
        $response = (new \GuzzleHttp\Client())->request('POST', "{$this->_baseUrl}/openapi/v1/file", [
            'headers' => [
                'Authorization' => "{$token_type} {$access_token}"
            ]
        ]);

        // // TODO
        // print_r($body = $response->getBody());
        // return file_key;
    }

    /**
     * 删除文件
     * 删除文件
     * scope: file.delete
     * 
     * @param string $access_token
     * @param string $token_type token类型，bearer
     * @param string $file_key 文件key
     */
    public function deleteFile(string $access_token, string $token_type, string $file_key)
    {
        $response = (new \GuzzleHttp\Client())->request('DELETE', "{$this->_baseUrl}/openapi/v1/file/{$file_key}", [
            'headers' => [
                'Authorization' => "{$token_type} {$access_token}"
            ]
        ]);

        // // TODO
        // print_r($body = $response->getBody());
        // return ;
    }

    /**
     * 创建副本
     * 从指定的⽂件创建⼀个副本⽂件
     * scope: file.copy
     * 
     * @param string $access_token
     * @param string $token_type token类型，bearer
     * @param string $file_key 文件key
     */
    public function copyFile(string $access_token, string $token_type, string $file_key)
    {
        $response = (new \GuzzleHttp\Client())->request('POST', "{$this->_baseUrl}/openapi/v1/file/{$file_key}/copy", [
            'headers' => [
                'Authorization' => "{$token_type} {$access_token}"
            ]
        ]);

        // // TODO
        // print_r($body = $response->getBody());
        // return file_key;
    }

    /**
     * 获取aigc签名
     * 获取aigc签名
     * 
     * @param string $access_token
     * @param string $token_type token类型，bearer
     */
    public function aigcSign(string $access_token, string $token_type)
    {
        $response = (new \GuzzleHttp\Client())->request('POST', "{$this->_baseUrl}/openapi/v1/ai/grant", [
            'headers' => [
                'Authorization' => "{$token_type} {$access_token}"
            ],
            'body' => [
                'body' => json_encode([
                    'sequence'  => 1,
                    'timestamp' => time()
                ]),
            ]
        ]);

        // // TODO
        // print_r($body = $response->getBody());
        // return data;
    }

    /**
     * 获取 PPTGO 官⽅模板
     * 获取pptgo官⽅模板列表
     * 
     * @param string $access_token
     * @param string $token_type token类型，bearer
     */
    public function theme(string $access_token, string $token_type)
    {
        $response = (new \GuzzleHttp\Client())->request('GET', "{$this->_baseUrl}/openapi/v1/cordwood/ppt_theme/list", [
            'headers' => [
                'Authorization' => "{$token_type} {$access_token}"
            ]
        ]);

        // // TODO
        // print_r($body = $response->getBody());
        // return list;
    }

    /**
     * 获取 PPTGO 标签列表
     * 获取 PPTGO 标签列表
     * 
     * @param string $access_token
     * @param string $token_type token类型，bearer
     * @param array $ttype 标签类型（查询标签类型，theme：主题，scene：场景，style：⻛格，color：⾊系，career：职业）
     */
    public function tag(string $access_token, string $token_type, array $ttype = [])
    {
        $ttype_params = [];

        foreach ($ttype as $type) {
            $ttype_params[] = 'ttype[]=' . $type;
        }

        $response = (new \GuzzleHttp\Client())->request('GET', "{$this->_baseUrl}/openapi/v1/tag/list?" . implode('&', $ttype_params), [
            'headers' => [
                'Authorization' => "{$token_type} {$access_token}"
            ]
        ]);

        // // TODO
        // print_r($body = $response->getBody());
        // return list;
    }

    /**
     * 获取 PPTGO 推荐md模板列表
     * 获取 PPTGO 推荐md模板列表
     * 
     * @param string $access_token
     * @param string $token_type token类型，bearer
     */
    public function preTemplate(string $access_token, string $token_type)
    {
        $response = (new \GuzzleHttp\Client())->request('GET', "{$this->_baseUrl}/openapi/v1/recommendation/pre_template", [
            'headers' => [
                'Authorization' => "{$token_type} {$access_token}"
            ]
        ]);

        // // TODO
        // print_r($body = $response->getBody());
        // return list;
    }

    /**
     * 上报 PPTGO 官⽅主题使⽤记录
     * 上报 PPTGO 官⽅主题使⽤记录
     * 
     * @param string $access_token
     * @param string $token_type token类型，bearer
     * @param string $theme_key 主题key
     * @param string $color_id 颜色id
     */
    public function themeHistory(string $access_token, string $token_type, $theme_key, $color_id)
    {
        $response = (new \GuzzleHttp\Client())->request('POST', "{$this->_baseUrl}/openapi/v1/cordwood/ppt_theme/use_history", [
            'headers' => [
                'Authorization' => "{$token_type} {$access_token}"
            ],
            'body' => [
                'elem_key' => $theme_key,
                'color_id' => $color_id,
            ]
        ]);

        // // TODO
        // print_r($body = $response->getBody());
        // return list;
    }

    /**
     * 上报 PPTGO 官⽅主题收藏
     * 上报 PPTGO 官⽅主题收藏
     * 
     * @param string $access_token
     * @param string $token_type token类型，bearer
     * @param string $theme_key 主题key
     * @param string $collect 收藏状态（true/false）
     */
    public function collect(string $access_token, string $token_type, $theme_key, $collect)
    {
        $response = (new \GuzzleHttp\Client())->request('POST', "{$this->_baseUrl}/openapi/v1/cordwood/user/collect", [
            'headers' => [
                'Authorization' => "{$token_type} {$access_token}"
            ],
            'body' => [
                'elem_key' => $theme_key,
                'collect'  => $collect,
            ]
        ]);

        // // TODO
        // print_r($body = $response->getBody());
        // return list;
    }
}
