<?php

namespace Kanboard\Plugin\WechatWorkNotifier\Notification;

use Kanboard\Plugin\WechatWorkNotifier\Notification\BaseNotification;
use Kanboard\Plugin\WechatWorkNotifier\Model\MessageModel;
use Kanboard\Core\Notification\NotificationInterface;
use Kanboard\Model\CommentModel;

class CommentNotification extends BaseNotification implements NotificationInterface
{
    public function notifyUser(array $user, $eventName, array $eventData){}

    public function notifyProject(array $project, $eventName, array $eventData)
    {
        // Send task changes to task members
        if ($eventName === CommentModel::EVENT_UPDATE ||                                                                           
            $eventName === CommentModel::EVENT_CREATE
        ){
            $this->sendMessage(MessageModel::create(
                $audiences      = $this->getAudiences($project, $eventData, $assigneeOnly = false),
                $taskId         = $eventData["task"]["id"], 
                $title          = $eventData["task"]["project_name"], 
                $subTitle       = $eventData["task"]["title"], 
                $key            = t("Comments Updated"), 
                $desc           = null, 
                $quote          = $eventData["comment"]["username"].": ".$eventData["comment"]["comment"], 
                $contentList    = null, 
                $taskLink       = $this->getTaskLink($eventData["task"]["id"], $eventData["comment"]["id"]), 
                $projectLink    = $this->getProjectLink($eventData["task"]["project_id"])
            ));
        }
    }
}


