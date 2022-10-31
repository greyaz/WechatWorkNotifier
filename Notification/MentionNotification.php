<?php

namespace Kanboard\Plugin\WechatWorkNotifier\Notification;

use Kanboard\Plugin\WechatWorkNotifier\Notification\BaseNotification;
use Kanboard\Core\Notification\NotificationInterface;
use Kanboard\Model\TaskModel;
use Kanboard\Model\CommentModel;

class MentionNotification extends BaseNotification implements NotificationInterface
{
    public function notifyUser(array $user, $eventName, array $eventData){}

    public function notifyProject(array $project, $eventName, array $eventData)
    {
        // Send a notification to someone who has been mentioned
        if ($eventName === TaskModel::EVENT_USER_MENTION ||
            $eventName === CommentModel::EVENT_USER_MENTION
        ){
            var_dump($eventName);
            var_dump($eventData);
        }
    }
}

