<?php

namespace Kanboard\Plugin\WechatWorkNotifier\Notification;

use Kanboard\Core\Base;
use Kanboard\Plugin\WechatWorkNotifier\Model\MessageModel;
use Kanboard\Core\Notification\NotificationInterface;
use Kanboard\Model\TaskModel;

class ChangesNotification extends Base implements NotificationInterface
{
    public function notifyUser(array $user, $eventName, array $eventData){}

    public function notifyProject(array $project, $eventName, array $eventData)
    {
        // Send task changes to task members
        if ($eventName === TaskModel::EVENT_UPDATE)
        {
            $changes = array();
            if (!empty($eventData["changes"])){
                foreach($eventData["changes"] as $key => $value){
                    $changes[t($key)] = gettype(strpos($key, "date_")) == "integer" ? date("Y-m-d H:i", $value): $value;
                }
            }

            $this->helper->message->send
            (
                $audiences  = $this->helper->message->getAudiences($project, $eventData, $assigneeOnly = false),
                $message    = MessageModel::create
                (
                    $taskId         = $eventData["task"]["id"], 
                    $title          = $eventData["task"]["project_name"], 
                    $subTitle       = $eventData["task"]["title"], 
                    $key            = t("Task Changed"), 
                    $desc           = null, 
                    $quoteTitle     = null, 
                    $quote          = null, 
                    $contentList    = $changes, 
                    $taskLink       = $this->helper->message->getTaskLink($eventData["task"]["id"]), 
                    $projectLink    = $this->helper->message->getProjectLink($eventData["task"]["project_id"])
                )
            );
        }
    }
}

