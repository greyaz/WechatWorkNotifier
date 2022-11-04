<?php

namespace Kanboard\Plugin\WechatWorkNotifier\Notification;

use Kanboard\Core\Base;
use Kanboard\Core\Translator;
use Kanboard\Plugin\WechatWorkNotifier\Model\MessageModel;
use Kanboard\Core\Notification\NotificationInterface;
use Kanboard\Model\CommentModel;
use Kanboard\Model\TaskModel;

class WechatWorkNotification extends Base implements NotificationInterface
{
    public function notifyUser(array $user, $eventName, array $eventData){
        // fix the translations unloading bug
        Translator::load($user['language'], implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'Locale')));
        
        // Send to the user who has been mentioned
        if ($eventName === CommentModel::EVENT_USER_MENTION ||
            $eventName === TaskModel::EVENT_USER_MENTION
        ){
            
        }
    }

    public function notifyProject(array $project, $eventName, array $eventData){}

    private function userMentionNotification($eventData){
        $this->helper->message->send
        (
            $audiences  = $eventData["mention"]["email"],
            $message    = MessageModel::create
            (
                $taskId         = $eventData["task"]["id"], 
                $title          = $eventData["task"]["project_name"], 
                $subTitle       = $eventData["task"]["title"], 
                $key            = t("Mentioned You"), 
                $desc           = null, 
                $quoteTitle     = isset($eventData["comment"]) ? $eventData["comment"]["username"].": " : $eventData["task"]["creator_username"].": ", 
                $quote          = isset($eventData["comment"]) ? $eventData["comment"]["comment"] : $eventData["task"]["description"], 
                $contentList    = null, 
                $taskLink       = $this->helper->message->getTaskLink($eventData["task"]["id"], isset($eventData["comment"]) ? $eventData["comment"]["id"] : null), 
                $projectLink    = $this->helper->message->getProjectLink($eventData["task"]["project_id"])
            )
        );
    }

    private function movementNotification($eventData){
        $this->helper->message->send
        (
            $audiences  = $this->helper->message->getAudiences($project, $eventData, $assigneeOnly = false),
            $message    = MessageModel::create
            (
                $taskId         = $eventData["task"]["id"], 
                $title          = $eventData["task"]["project_name"], 
                $subTitle       = $eventData["task"]["title"], 
                $key            = $eventData["task"]["column_title"], 
                $desc           = t("Progress updated"), 
                $quoteTitle     = null, 
                $quote          = null, 
                $contentList    = array(
                    t("Assignee") => $eventData["task"]["assignee_username"]
                ), 
                $taskLink       = $this->helper->message->getTaskLink($eventData["task"]["id"]), 
                $projectLink    = $this->helper->message->getProjectLink($eventData["task"]["project_id"])
            )
        );
    }

    private function creationNotification($eventData){
        $this->helper->message->send
        (
            $audiences  = $this->helper->message->getAudiences($project, $eventData, $assigneeOnly = false),
            $message    = MessageModel::create
            (
                $taskId         = $eventData["task"]["id"], 
                $title          = $eventData["task"]["project_name"], 
                $subTitle       = $eventData["task"]["title"], 
                $key            = t("New Task"), 
                $desc           = null, 
                $quoteTitle     = t("Description"), 
                $quote          = $eventData["task"]["description"], 
                $contentList    = array(
                    t("Creator") => $eventData["task"]["creator_username"]
                ), 
                $taskLink       = $this->helper->message->getTaskLink($eventData["task"]["id"]), 
                $projectLink    = $this->helper->message->getProjectLink($eventData["task"]["project_id"])
            )
        );
    }

    private function commentNotification($eventData){
        $this->helper->message->send
        (
            $audiences  = $this->helper->message->getAudiences($project, $eventData, $assigneeOnly = false),
            $message    = MessageModel::create
            (
                $taskId         = $eventData["task"]["id"], 
                $title          = $eventData["task"]["project_name"], 
                $subTitle       = $eventData["task"]["title"], 
                $key            = t("Comments Updated"), 
                $desc           = null, 
                $quoteTitle     = $eventData["comment"]["username"].": ", 
                $quote          = $eventData["comment"]["comment"], 
                $contentList    = null, 
                $taskLink       = $this->helper->message->getTaskLink($eventData["task"]["id"], $eventData["comment"]["id"]), 
                $projectLink    = $this->helper->message->getProjectLink($eventData["task"]["project_id"])
            )
        );
    }

    private function changesNotification($eventData){

        $changes = "";
        if (!empty($eventData["changes"])){
            foreach($eventData["changes"] as $key => $value){
                $value = gettype(strpos($key, "date_")) == "integer" ? date("Y-m-d H:i", $value): $value;
                $change .= t($key).": ".$value." | ";
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
                $quoteTitle     = t("Changes"), 
                $quote          = $changes, 
                $contentList    = null, 
                $taskLink       = $this->helper->message->getTaskLink($eventData["task"]["id"]), 
                $projectLink    = $this->helper->message->getProjectLink($eventData["task"]["project_id"])
            )
        );
    }

    private function assigneeNotification($eventData){
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
                $quoteTitle     = null, 
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