<?php

namespace Kanboard\Plugin\WechatWorkNotifier\Notification;

use Kanboard\Plugin\WechatWorkNotifier\Notification\BaseNotification;
use Kanboard\Core\Notification\NotificationInterface;
use Kanboard\Model\CommentModel;
use Kanboard\Model\TaskModel;

class UserMentionNotification extends BaseNotification implements NotificationInterface
{
    public function notifyUser(array $user, $eventName, array $eventData){
        // Send to the user who has been mentioned
        if ($eventName === CommentModel::EVENT_USER_MENTION ||
            $eventName === TaskModel::EVENT_USER_MENTION
        ){
            $postData = array();

            $postData["touser"]                                                  = $eventData["mention"]["email"];
            $postData["msgtype"]                                                 = "template_card";
            $postData["agentid"]                                                 = $GLOBALS["WWN_CONFIGS"]['AGENTID'];
            $postData["template_card"]["card_type"]                              = "text_notice";
            $postData["template_card"]["source"]["icon_url"]                     = $GLOBALS["WWN_CONFIGS"]['ICON_URL'];
            $postData["template_card"]["source"]["desc"]                         = t("Task Management");
            $postData["template_card"]["task_id"]                                = $eventData["task"]["id"];
            $postData["template_card"]["main_title"]["title"]                    = $eventData["task"]["project_name"];
            $postData["template_card"]["main_title"]["desc"]                     = $eventData["task"]["title"];
            $postData["template_card"]["emphasis_content"]["title"]              = t("@ You");
            if ($eventName === CommentModel::EVENT_USER_MENTION)
            {
                $postData["template_card"]["horizontal_content_list"][0]["keyname"]  = t("Comment");
                $postData["template_card"]["horizontal_content_list"][0]["value"]    = $eventData["comment"]["comment"];
                $postData["template_card"]["horizontal_content_list"][1]["keyname"]  = t("Commentator");
                $postData["template_card"]["horizontal_content_list"][1]["value"]    = $eventData["comment"]["name"];
            }
            else
            {
                $postData["template_card"]["horizontal_content_list"][0]["keyname"]  = t("Description");
                $postData["template_card"]["horizontal_content_list"][0]["value"]    = $eventData["task"]["description"];
                $postData["template_card"]["horizontal_content_list"][1]["keyname"]  = t("Creator");
                $postData["template_card"]["horizontal_content_list"][1]["value"]    = $eventData["task"]["creator_name"];
            }
            $postData["template_card"]["jump_list"][0]["type"]                   = "1";
            $postData["template_card"]["jump_list"][0]["title"]                  = t("View the task");
            if ($eventName === CommentModel::EVENT_USER_MENTION)
            {
                $postData["template_card"]["jump_list"][0]["url"]                    = $this->getKanboardURL()."/task/".$eventData["task"]["id"]."#comment-".$eventData["comment"]["id"];
            }
            else
            {
                $postData["template_card"]["jump_list"][0]["url"]                    = $this->getKanboardURL()."/task/".$eventData["task"]["id"];
            }
            $postData["template_card"]["jump_list"][1]["type"]                   = "1";
            $postData["template_card"]["jump_list"][1]["title"]                  = t("View the kanban");
            $postData["template_card"]["jump_list"][1]["url"]                    = $this->getKanboardURL()."/board/".$eventData["task"]["project_id"];
            $postData["template_card"]["card_action"]["type"]                    = "1";
            if ($eventName === CommentModel::EVENT_USER_MENTION)
            {
                $postData["template_card"]["card_action"]["url"]                     = $this->getKanboardURL()."/task/".$eventData["task"]["id"]."#comment-".$eventData["comment"]["id"];
            }
            else
            {
                $postData["template_card"]["card_action"]["url"]                     = $this->getKanboardURL()."/task/".$eventData["task"]["id"];
            }
            $postData["enable_duplicate_check"]                                  = "1";
            $postData["duplicate_check_interval"]                                = "3";

            $this->sendMessage($postData);
        }
    }

    public function notifyProject(array $project, $eventName, array $eventData){}
}
            
