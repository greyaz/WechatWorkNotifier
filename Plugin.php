<?php

namespace Kanboard\Plugin\WechatWork;

use Kanboard\Core\Translator;
use Kanboard\Core\Plugin\Base;


class Plugin extends Base
{
    public $configs;

    public function initialize()
    {
        require_once('plugins/WechatWork/config.php');
        $this->projectNotificationTypeModel->setType('WechatWorkTask', t('Wechat Work: Send task updates to task members'), '\Kanboard\Plugin\WechatWork\Notification\TaskNotification');
        $this->projectNotificationTypeModel->setType('WechatWorkAssignee', t('Wechat Work: Send a notification to someone who has been assigned'), '\Kanboard\Plugin\WechatWork\Notification\AssigneeNotification');
    }
    public function onStartup()
    {
        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__.'/Locale');
    }

    public function getPluginDescription()
    {
        return 'Receive notifications on Wechat Work (企业微信), aka Wecom.';
    }

    public function getPluginAuthor()
    {
        return 'Greyaz';
    }

    public function getPluginVersion()
    {
        return '0.1.0';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/greyaz/Wechat-Work-Notifier';
    }

    public function getCompatibleVersion()
    {
        return '>=1.2.8';
    }
}


