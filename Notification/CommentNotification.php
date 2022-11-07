<?php

namespace Kanboard\Plugin\WechatWorkNotifier\Notification;

use Kanboard\Core\Base;
use Kanboard\Core\Translator;
use Kanboard\Plugin\WechatWorkNotifier\Model\MessageModel;
use Kanboard\Core\Notification\NotificationInterface;
use Kanboard\Model\CommentModel;

class CommentNotification extends Base implements NotificationInterface
{
    public function notifyUser(array $user, $eventName, array $eventData){}

    public function notifyProject(array $project, $eventName, array $eventData)
    {
        // Send task changes to task members
        if ($eventName === CommentModel::EVENT_UPDATE ||                                                                           
            $eventName === CommentModel::EVENT_CREATE
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
    }
}


