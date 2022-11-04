<?php

namespace Kanboard\Plugin\WechatWorkNotifier\Notification;

use Kanboard\Core\Base;
use Kanboard\Plugin\WechatWorkNotifier\Model\MessageModel;
use Kanboard\Core\Notification\NotificationInterface;
use Kanboard\Model\TaskModel;

class MovementNotification extends Base implements NotificationInterface
{
    public function notifyUser(array $user, $eventName, array $eventData){}

    public function notifyProject(array $project, $eventName, array $eventData)
    {
        // fix the translations unloading bug
        Translator::load($this->languageModel->getCurrentLanguage(), implode(DIRECTORY_SEPARATOR, array(__DIR__, 'Locale')));
        
        // Send task movemens to task members
        if ($eventName === TaskModel::EVENT_MOVE_PROJECT ||
            $eventName === TaskModel::EVENT_MOVE_COLUMN ||
            $eventName === TaskModel::EVENT_MOVE_POSITION ||
            $eventName === TaskModel::EVENT_MOVE_SWIMLANE
        ){
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
    }
}
