<?php

namespace Kanboard\Plugin\WechatWorkNotifier\Notification;

use Kanboard\Plugin\WechatWorkNotifier\Notification\BaseNotification;
use Kanboard\Plugin\WechatWorkNotifier\Model\MessageModel;
use Kanboard\Core\Notification\NotificationInterface;
use Kanboard\Model\TaskModel;

class CreationNotification extends BaseNotification implements NotificationInterface
{
    public function notifyUser(array $user, $eventName, array $eventData){}

    public function notifyProject(array $project, $eventName, array $eventData)
    {
        // Send to task members after creation.
        if ($eventName === TaskModel::EVENT_CREATE
        ){
            $this->sendMessage(MessageModel::create(
                $audiences      = $this->getAudiences($project, $eventData, $assigneeOnly = false),
                $taskId         = $eventData["task"]["id"], 
                $title          = $eventData["task"]["project_name"], 
                $subTitle       = $eventData["task"]["title"], 
                $key            = t("New Task"), 
                $desc           = null, 
                $quote          = $eventData["task"]["description"], 
                $contentList    = array{
                    t("Creator") => $eventData["task"]["creator_username"]
                }, 
                $taskLink       = $this->getTaskLink($eventData["task"]["id"]), 
                $projectLink    = $this->getProjectLink($eventData["task"]["project_id"])
            ));
        }
    }
}

