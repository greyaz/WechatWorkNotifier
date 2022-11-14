<?php

namespace Kanboard\Plugin\WechatWorkNotifier\Notification;

use Kanboard\Core\Base;
use Kanboard\Core\Translator;
use Kanboard\Plugin\WechatWorkNotifier\Model\MessageModel;
use Kanboard\Core\Notification\NotificationInterface;
use Kanboard\Model\TaskModel;

class AssigneeNotification extends Base implements NotificationInterface
{
    public function notifyUser(array $user, $eventName, array $eventData){}

    public function notifyProject(array $project, $eventName, array $eventData)
    {
        // Send a notification to someone who has been assigned
        if ($eventName === TaskModel::EVENT_ASSIGNEE_CHANGE)
        {
            // fix the translations unloading bug
            Translator::load($this->languageModel->getCurrentLanguage(), implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'Locale')));

            $this->helper->message->send
            (
                $audiences  = $this->helper->message->getAudiences($project, $eventData, $assigneeOnly = true),
                $message    = MessageModel::create
                (
                    $taskId         = $eventData["task"]["id"], 
                    $title          = t("You have a new task"), 
                    $subTitle       = $eventData["task"]["project_name"], 
                    $key            = "P".$eventData["task"]["priority"], 
                    $desc           = $eventData["task"]["title"], 
                    $quoteTitle     = t("Description"), 
                    $quote          = $eventData["task"]["description"], 
                    $contentList    = array
                    (
                        t("Start time") => empty($eventData["task"]["date_started"]) ? "-" : date("Y-m-d H:i", $eventData["task"]["date_started"]),
                        t("Due time")   => empty($eventData["task"]["date_due"]) ? "-" : date("Y-m-d H:i", $eventData["task"]["date_due"]),
                        t("Creator")    => $eventData["task"]["creator_username"]
                    ), 
                    $taskLink       = $this->helper->message->getTaskLink($eventData["task"]["id"]), 
                    $projectLink    = $this->helper->message->getProjectLink($eventData["task"]["project_id"])
                )
            );
        }
    }
}
