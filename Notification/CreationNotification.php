<?php

namespace Kanboard\Plugin\WechatWorkNotifier\Notification;

use Kanboard\Core\Base;
use Kanboard\Core\Translator;
use Kanboard\Plugin\WechatWorkNotifier\Model\MessageModel;
use Kanboard\Core\Notification\NotificationInterface;
use Kanboard\Model\TaskModel;

class CreationNotification extends Base implements NotificationInterface
{
    public function notifyUser(array $user, $eventName, array $eventData){}

    public function notifyProject(array $project, $eventName, array $eventData)
    {
        
        
        // Send to task members after creation.
        if ($eventName === TaskModel::EVENT_CREATE
        ){
            // fix the translations unloading bug
            Translator::load($this->languageModel->getCurrentLanguage(), implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'Locale')));

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

