<?php

namespace Kanboard\Plugin\WechatWorkNotifier\Notification;

use Kanboard\Plugin\WechatWorkNotifier\Notification\BaseNotification;
use Kanboard\Core\Notification\NotificationInterface;
use Kanboard\Model\TaskModel;

class AssigneeNotification extends BaseNotification implements NotificationInterface
{
    public function notifyUser(array $user, $eventName, array $eventData){}

    public function notifyProject(array $project, $eventName, array $eventData)
    {
        // Send a notification to someone who has been assigned
        if ($eventName === TaskModel::EVENT_ASSIGNEE_CHANGE)
        {
            $postData = array();

            $postData["touser"]                                                  = $this->getAudiences($eventData, $assigneeOnly = true);
            $postData["msgtype"]                                                 = "template_card";
            $postData["agentid"]                                                 = $this->getConfigs()['AGENTID'];
            $postData["template_card"]["card_type"]                              = "text_notice";
            $postData["template_card"]["source"]["icon_url"]                     = $this->getConfigs()['ICON_URL'];
            $postData["template_card"]["source"]["desc"]                         = t("Task Management");
            $postData["template_card"]["task_id"]                                = $eventData["task_id"];
            $postData["template_card"]["main_title"]["title"]                    = t("You have a new task");
            $postData["template_card"]["emphasis_content"]["title"]              = "P".$eventData["task"]["priority"];
            $postData["template_card"]["emphasis_content"]["desc"]               = $eventData["task"]["title"];
            $postData["template_card"]["horizontal_content_list"][0]["keyname"]  = t("Start time");
            $postData["template_card"]["horizontal_content_list"][0]["value"]    = date("Y-m-d H:i", $eventData["task"]["date_started"]);
            $postData["template_card"]["horizontal_content_list"][1]["keyname"]  = t("Due time");
            $postData["template_card"]["horizontal_content_list"][1]["value"]    = date("Y-m-d H:i", $eventData["task"]["date_due"]);
            $postData["template_card"]["jump_list"][0]["type"]                   = "1";
            $postData["template_card"]["jump_list"][0]["title"]                  = t("View the task");
            $postData["template_card"]["jump_list"][0]["url"]                    = $this->getKanboardURL()."/task/".$eventData["task_id"];
            $postData["template_card"]["jump_list"][1]["type"]                   = "1";
            $postData["template_card"]["jump_list"][1]["title"]                  = t("View the kanban");
            $postData["template_card"]["jump_list"][1]["url"]                    = $this->getKanboardURL()."/board/".$eventData["task"]["project_id"];
            $postData["template_card"]["card_action"]["type"]                    = "1";
            $postData["template_card"]["card_action"]["url"]                     = $this->getKanboardURL()."/task/".$eventData["task_id"];
            $postData["enable_duplicate_check"]                                  = "1";
            $postData["duplicate_check_interval"]                                = "3";

            $this->sendMessage($postData);
        }
    }
}
