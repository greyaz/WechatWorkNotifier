<?php

// 用于在企业微信内点击打开看板，请使用绝对地址。Example: 'https://www.yourkanboard.com'
$WWN_CONFIGS['KANBOARD_URL'] = '';

// https://developer.work.weixin.qq.com/document/path/90665#corpid
$WWN_CONFIGS['CORPID'] = '';

// https://developer.work.weixin.qq.com/document/path/90665#secret
$WWN_CONFIGS['SECRET'] = '';

// 企业应用的id，企业内部开发，可在应用的设置页面查看；第三方服务商，可通过接口 获取企业授权信息 获取该参数值。
// https://developer.work.weixin.qq.com/document/path/90236#%E6%A8%A1%E6%9D%BF%E5%8D%A1%E7%89%87%E6%B6%88%E6%81%AF
$WWN_CONFIGS['AGENTID'] = '';

// 用于在企业微信通知中展示的logo图片，请使用绝对地址。尺寸建议为72*72。
// https://developer.work.weixin.qq.com/document/path/90236#%E6%A8%A1%E6%9D%BF%E5%8D%A1%E7%89%87%E6%B6%88%E6%81%AF
$WWN_CONFIGS['ICON_URL'] = '';

// 通知的时间间隔，Integer整型， 单位为秒。如果间隔时间内触发了指向同一个用户的多条通知，则仅向该用户发送第一条。
// 默认为1秒。间隔时间为负数时，功能关闭。
$WWN_CONFIGS['NOTIFICATION_INTERVAL'] = 1;
