<?php

return [
    
    'token'=> json_decode(env("WECHAT_TOKEN"), true), //填写应用接口的Token
    'encodingAesKey'=> json_decode(env("WECHAT_KEY"), true), //填写加密用的EncodingAESKey
    'appid'=> env("WECHAT_APP"), //填写高级调用功能的app id
    'appsecret'=> json_decode(env("WECHAT_SECRET"), true), //填写高级调用功能的密钥
    'agent'=> json_decode(env("WECHAT_AGENT"), true)

];
