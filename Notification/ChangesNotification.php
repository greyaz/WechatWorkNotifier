<?php

namespace Kanboard\Plugin\WechatWorkNotifier\Notification;

use Kanboard\Core\Base;
use Kanboard\Core\Translator;
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
            // fix the translations unloading bug
            Translator::load($this->languageModel->getCurrentLanguage(), implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'Locale')));

            $changes = "";
            if (!empty($eventData["changes"])){
                foreach($eventData["changes"] as $key => $value){
                    $value = gettype(strpos($key, "date_")) == "integer" ? date("Y-m-d H:i", $value): $value;
                    $changes .= t($key)."⇢".$value." | ";
                }
                $changes = substr($changes, 0, -2);
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
                    $quoteTitle     = t("Changes: "), 
                    $quote          = $changes, 
                    $contentList    = null, 
                    $taskLink       = $this->helper->message->getTaskLink($eventData["task"]["id"]), 
                    $projectLink    = $this->helper->message->getProjectLink($eventData["task"]["project_id"])
                )
            );
        }
    }
}

