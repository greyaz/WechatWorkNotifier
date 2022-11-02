<?php

namespace Kanboard\Plugin\WechatWorkNotifier\Notification;

use Kanboard\Core\Base;
use Kanboard\Plugin\WechatWorkNotifier\Model\MessageModel;
use Kanboard\Core\Notification\NotificationInterface;
use Kanboard\Model\CommentModel;
use Kanboard\Model\TaskModel;

class UserMentionNotification extends Base implements NotificationInterface
{
    public function notifyUser(array $user, $eventName, array $eventData){
        // Send to the user who has been mentioned
        if ($eventName === CommentModel::EVENT_USER_MENTION ||
            $eventName === TaskModel::EVENT_USER_MENTION
        ){
            $this->helper->message->send
            (
                $audiences  = $eventData["mention"]["email"],
                $message    = MessageModel::create
                (
                    $taskId         = $eventData["task"]["id"], 
                    $title          = $eventData["task"]["project_name"], 
                    $subTitle       = $eventData["task"]["title"], 
                    $key            = t("@ You"), 
                    $desc           = null, 
                    $quote          = isset($eventData["comment"]) ? $eventData["comment"]["username"].": ".$eventData["comment"]["comment"] : $eventData["task"]["description"], 
                    $contentList    = isset($eventData["comment"]) ? null : array(
                        t("Creator") => $eventData["task"]["creator_username"]
                    ), 
                    $taskLink       = $this->helper->message->getTaskLink($eventData["task"]["id"], $eventData["comment"]["id"]), 
                    $projectLink    = $this->helper->message->getProjectLink($eventData["task"]["project_id"])
                )
            );
        }
    }

    public function notifyProject(array $project, $eventName, array $eventData){}
}
            
