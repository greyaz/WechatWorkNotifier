<?php

namespace Kanboard\Plugin\WechatWorkNotifier;

use Kanboard\Core\Translator;
use Kanboard\Core\Plugin\Base;


class Plugin extends Base
{
    public $configs;

    public function initialize()
    {
        if (file_exists('plugins/WechatWorkNotifier/config.php'))
        {
            require_once('plugins/WechatWorkNotifier/config.php');

        $this->projectNotificationTypeModel->setType('WechatWorkTaskNotifier', t('Wechat Work: Send task updates to task members'), '\Kanboard\Plugin\WechatWorkNotifier\Notification\TaskNotification');
        
        $this->projectNotificationTypeModel->setType('WechatWorkAssigneeNotifier', t('Wechat Work: Send a notification to someone who has been assigned'), '\Kanboard\Plugin\WechatWorkNotifier\Notification\AssigneeNotification');
        }
        
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
        return 'https://github.com/greyaz/WechatWorkNotifier';
    }

    public function getCompatibleVersion()
    {
        return '>=1.2.8';
    }
}


