<?php

namespace ltj\pptgo;

use GuzzleHttp\Client;

class Api
{
    private static $_instance = null;

    private $_client;
    private $_baseUrl = 'https://sdk.pptgo.cn/pptsdk/';
    private $_accessToken;
    private $_tokenType;

    public function __construct(string $access_token, string $token_type, string $base_url)
    {
        $this->_accessToken = $access_token;
        $this->_tokenType = $token_type;
        $this->_baseUrl = $base_url ?: $this->_baseUrl;

        $this->_client = new Client(['base_uri' => $this->_baseUrl]);
    }

    public static function getInstance(string $access_token = '', string $token_type = 'Bearer', string $base_url = '')
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($access_token, $token_type, $base_url);
        }

        return self::$_instance;
    }

    /**
     * 测试环境下调用
     */
    public function test()
    {
        $this->_client = new Client(['base_uri' => $this->_baseUrl, 'verify' => false]);
        return $this;
    }

    /**
     * 获取应⽤级access_token
     * 集成⽅获取应⽤级 PPTGO SDK的授权访问凭证access_token
     * 
     * @param string $client_id 客户端id
     * @param string $client_secret 客户端密钥
     * @param string $scope 授权范围（all_scopes）
     */
    public function appToken(string $client_id, string $client_secret, string $scope = 'all_scopes')
    {
        $response = $this->_client->request(
            'POST',
            'oauth/token',
            [
                'multipart' => [
                    ['name' => 'grant_type', 'contents' => 'client_credentials'],
                    ['name' => 'scope', 'contents' => $scope],
                    ['name' => 'client_id', 'contents' => $client_id],
                    ['name' => 'client_secret', 'contents' => $client_secret]
                ],
                'http_errors' => false
            ]
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * 获取⽂件级access_token
     * 接⼊⽅获取⽂件级access_token后，⽤于提供给 PPTGO 编辑器SDK鉴权使⽤
     * ⽂件级access_token，接⼊⽅可根据⾃⾝业务决定授予什么操作权限，在PPTGO SDK编辑器中，⽬前共分两种⻆⾊：编辑者、查看者。 授予编辑者权限，可传⼊scope: "file.edit file.view"； 授予查看者权限，则传⼊scope: "file.view"...
     * 
     * @param string $client_id 客户端id
     * @param string $client_secret 客户端密钥
     * @param string $file_key 文件key
     * @param string $scope 授权范围（file.edit file.view）
     * @param string $user_id 用户id
     * @param string $nick_name 用户昵称
     * @param string $avatar_url 当前登录⽤⼾头像链接
     */
    public function fileToken(string $client_id, string $client_secret, string $file_key, string $scope, string $user_id = '', string $nick_name = '', string $avatar_url = '')
    {
        $response = $this->_client->request(
            'POST',
            'oauth/token',
            [
                'multipart' => [
                    ['name' => 'grant_type', 'contents' => 'client_credentials'],
                    ['name' => 'file_key', 'contents' => $file_key],
                    ['name' => 'scope', 'contents' => $scope],
                    ['name' => 'client_id', 'contents' => $client_id],
                    ['name' => 'client_secret', 'contents' => $client_secret],
                    ['name' => 'user_id', 'contents' => $user_id],
                    ['name' => 'nick_name', 'contents' => $nick_name],
                    ['name' => 'avatar_url', 'contents' => $avatar_url]
                ],
                'http_errors' => false
            ]
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * 创建文件
     * 创建⼀个空⽩⽂档
     * scope: file.create
     */
    public function createFile()
    {
        $response = $this->_client->request(
            'POST',
            'openapi/v1/file',
            [
                'headers' => [
                    'Authorization' => "{$this->_tokenType} {$this->_accessToken}"
                ],
                'http_errors' => false
            ]
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * 删除文件
     * 删除文件
     * scope: file.delete
     * 
     * @param string $file_key 文件key
     */
    public function deleteFile(string $file_key)
    {
        $response = $this->_client->request('DELETE', "openapi/v1/file/{$file_key}", [
            'headers' => [
                'Authorization' => "{$this->_tokenType} {$this->_accessToken}"
            ],
            'http_errors' => false
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * 创建副本
     * 从指定的⽂件创建⼀个副本⽂件
     * scope: file.copy
     * 
     * @param string $file_key 文件key
     */
    public function copyFile(string $file_key)
    {
        $response = $this->_client->request('POST', "openapi/v1/file/{$file_key}/copy", [
            'headers' => [
                'Authorization' => "{$this->_tokenType} {$this->_accessToken}"
            ],
            'http_errors' => false
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * 获取aigc签名
     * 获取aigc签名
     */
    public function aigcSign(string $body)
    {
        $response = $this->_client->request('POST', 'openapi/v1/ai/grant', [
            'headers' => [
                'Authorization' => "{$this->_tokenType} {$this->_accessToken}"
            ],
            'json' => [
                'body' => $body,
            ],
            'http_errors' => false
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * 获取 PPTGO 官⽅模板
     * 获取pptgo官⽅模板列表
     */
    public function theme()
    {
        $response = $this->_client->request('GET', 'openapi/v1/cordwood/ppt_theme/list', [
            'headers' => [
                'Authorization' => "{$this->_tokenType} {$this->_accessToken}"
            ],
            'http_errors' => false
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * 获取 PPTGO 标签列表
     * 获取 PPTGO 标签列表
     * 
     * @param array $ttype 标签类型（查询标签类型，theme：主题，scene：场景，style：⻛格，color：⾊系，career：职业）
     */
    public function tag(array $ttype = [])
    {
        $ttype_params = [];

        foreach ($ttype as $type) {
            $ttype_params[] = 'ttype[]=' . $type;
        }

        $response = $this->_client->request('GET', 'openapi/v1/tag/list?' . implode('&', $ttype_params), [
            'headers' => [
                'Authorization' => "{$this->_tokenType} {$this->_accessToken}"
            ],
            'http_errors' => false
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * 获取 PPTGO 推荐md模板列表
     * 获取 PPTGO 推荐md模板列表
     */
    public function preTemplate()
    {
        $response = $this->_client->request('GET', 'openapi/v1/recommendation/pre_template', [
            'headers' => [
                'Authorization' => "{$this->_tokenType} {$this->_accessToken}"
            ],
            'http_errors' => false
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * 上报 PPTGO 官⽅主题使⽤记录
     * 上报 PPTGO 官⽅主题使⽤记录
     * 
     * @param string $theme_key 主题key
     * @param string $color_id 颜色id
     */
    public function themeHistory(string $theme_key, string $color_id)
    {
        $response = $this->_client->request('POST', 'openapi/v1/cordwood/ppt_theme/use_history', [
            'headers' => [
                'Authorization' => "{$this->_tokenType} {$this->_accessToken}"
            ],
            'json' => [
                'elem_key' => $theme_key,
                'color_id' => $color_id,
            ],
            'http_errors' => false
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * 上报 PPTGO 官⽅主题收藏
     * 上报 PPTGO 官⽅主题收藏
     * 
     * @param string $theme_key 主题key
     * @param string $collect 收藏状态（true/false）
     */
    public function collect($theme_key, $collect)
    {
        $response = $this->_client->request('POST', 'openapi/v1/cordwood/user/collect', [
            'headers' => [
                'Authorization' => "{$this->_tokenType} {$this->_accessToken}"
            ],
            'json' => [
                'elem_key' => $theme_key,
                'collect'  => $collect,
            ],
            'http_errors' => false
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
