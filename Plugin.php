<?php

namespace Kanboard\Plugin\WechatWorkNotifier;

use Kanboard\Core\Translator;
use Kanboard\Core\Plugin\Base;


class Plugin extends Base
{

    public function initialize()
    {
        $this->helper->register('message', '\Kanboard\Plugin\WechatWorkNotifier\Helper\MessageHelper');
    }

    public function onStartup()
    {
        Translator::load($this->languageModel->getCurrentLanguage(), implode(DIRECTORY_SEPARATOR, array(__DIR__, 'Locale')));

        if (file_exists('plugins/WechatWorkNotifier/config.php'))
        {
            global $WWN_CONFIGS;
            require_once('plugins/WechatWorkNotifier/config.php');

            $this->userNotificationTypeModel->setType('WWN_UserMentionNotifier', t('Wechat Work: Notifying me after being mentioned.'), '\Kanboard\Plugin\WechatWorkNotifier\Notification\UserMentionNotification');
            $this->projectNotificationTypeModel->setType('WWN_AssigneeNotifier', t('Wechat Work: Notifying someone who has been assigned.'), '\Kanboard\Plugin\WechatWorkNotifier\Notification\AssigneeNotification');
            $this->projectNotificationTypeModel->setType('WWN_MovementNotifier', t('Wechat Work: Notifying members after moving a task.'), '\Kanboard\Plugin\WechatWorkNotifier\Notification\MovementNotification');
            $this->projectNotificationTypeModel->setType('WWN_ChangesNotifier', t('Wechat Work: Notifying members after changing a task.'), '\Kanboard\Plugin\WechatWorkNotifier\Notification\ChangesNotification');
            $this->projectNotificationTypeModel->setType('WWN_CreationNotifier', t('Wechat Work: Notifying task members after creation.'), '\Kanboard\Plugin\WechatWorkNotifier\Notification\CreationNotification');
            $this->projectNotificationTypeModel->setType('WWN_CommentNotifier', t('Wechat Work: Notifying members after updating comments.'), '\Kanboard\Plugin\WechatWorkNotifier\Notification\CommentNotification');
        }
    }

    public function getPluginName()	{ 	 
		return 'Wechat Work Notifier'; 
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
        return '0.4.2';
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


