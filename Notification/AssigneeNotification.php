<?php

namespace Kanboard\Plugin\WechatWorkNotifier\Notification;

use Kanboard\Plugin\WechatWorkNotifier\Notification\BaseNotification;
use Kanboard\Plugin\WechatWorkNotifier\Model\MessageModel;
use Kanboard\Core\Notification\NotificationInterface;
use Kanboard\Model\TaskModel;

class AssigneeNotification extends BaseNotification implements NotificationInterface
{
    public function notifyUser(array $user, $eventName, array $eventData){}

    public function notifyProject(array $project, $eventName, array $eventData)
    {
        // Send a notification to someone who has been assigned
        if ($eventName === TaskModel::EVENT_ASSIGNEE_CHANGE)
        {
            $this->helper->message->send
            (
                $audiences  = $this->helper->message->getAudiences($project, $eventData, $assigneeOnly = true),
                $message    = MessageModel::create
                (
                    $taskId         = $eventData["task"]["id"], 
                    $title          = t("You have a new task"), 
                    $subTitle       = null, 
                    $key            = "P".$eventData["task"]["priority"], 
                    $desc           = $eventData["task"]["title"], 
                    $quote          = null, 
                    $contentList    = array(
                        t("Start time") => date("Y-m-d H:i", $eventData["task"]["date_started"]),
                        t("Due time") => date("Y-m-d H:i", $eventData["task"]["date_due"])
                    ), 
                    $taskLink       = $this->helper->message->getTaskLink($eventData["task"]["id"]), 
                    $projectLink    = $this->helper->message->getProjectLink($eventData["task"]["project_id"])
                )
            );
        }
    }
}
