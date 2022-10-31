<?php

namespace Kanboard\Plugin\WechatWorkNotifier\Notification;

use Kanboard\Plugin\WechatWorkNotifier\Notification\BaseNotification;
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
            $postData = array();

            $postData["touser"]                                                  = $this->getAudiences($project, $eventData, $assigneeOnly = false);
            $postData["msgtype"]                                                 = "template_card";
            $postData["agentid"]                                                 = $GLOBALS["WWN_CONFIGS"]['AGENTID'];
            $postData["template_card"]["card_type"]                              = "text_notice";
            $postData["template_card"]["source"]["icon_url"]                     = $GLOBALS["WWN_CONFIGS"]['ICON_URL'];
            $postData["template_card"]["source"]["desc"]                         = t("Task Management");
            $postData["template_card"]["task_id"]                                = $eventData["task_id"];
            $postData["template_card"]["main_title"]["title"]                    = $eventData["task"]["project_name"];
            $postData["template_card"]["main_title"]["desc"]                     = $eventData["task"]["title"];
            $postData["template_card"]["emphasis_content"]["title"]              = t("Comments Updated");
            $postData["template_card"]["horizontal_content_list"][0]["keyname"]  = t("Comment");
            $postData["template_card"]["horizontal_content_list"][0]["value"]    = $eventData["comment"]["comment"];
            $postData["template_card"]["horizontal_content_list"][1]["keyname"]  = t("Commentator");
            $postData["template_card"]["horizontal_content_list"][1]["value"]    = $eventData["comment"]["name"];
            $postData["template_card"]["jump_list"][0]["type"]                   = "1";
            $postData["template_card"]["jump_list"][0]["title"]                  = t("View the task");
            $postData["template_card"]["jump_list"][0]["url"]                    = $this->getKanboardURL()."/task/".$eventData["task_id"]."#comment-".$eventData["comment"]["id"];
            $postData["template_card"]["jump_list"][1]["type"]                   = "1";
            $postData["template_card"]["jump_list"][1]["title"]                  = t("View the kanban");
            $postData["template_card"]["jump_list"][1]["url"]                    = $this->getKanboardURL()."/board/".$eventData["task"]["project_id"];
            $postData["template_card"]["card_action"]["type"]                    = "1";
            $postData["template_card"]["card_action"]["url"]                     = $this->getKanboardURL()."/task/".$eventData["task_id"]."#comment-".$eventData["comment"]["id"];
            $postData["enable_duplicate_check"]                                  = "1";
            $postData["duplicate_check_interval"]                                = "3";

            $this->sendMessage($postData);
        }
    }
}


