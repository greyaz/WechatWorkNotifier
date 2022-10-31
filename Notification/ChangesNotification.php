<?php

namespace Kanboard\Plugin\WechatWorkNotifier\Notification;

use Kanboard\Plugin\WechatWorkNotifier\Notification\BaseNotification;
use Kanboard\Core\Notification\NotificationInterface;
use Kanboard\Model\TaskModel;

class ChangesNotification extends BaseNotification implements NotificationInterface
{
    public function notifyUser(array $user, $eventName, array $eventData){}

    public function notifyProject(array $project, $eventName, array $eventData)
    {
        // Send task movemens to task members
        if ($eventName === TaskModel::EVENT_UPDATE)
        {
            $postData = array();

            $postData["touser"]                                                  = $this->getAudiences($project, $eventData, $assigneeOnly = false);
            $postData["msgtype"]                                                 = "template_card";
            $postData["agentid"]                                                 = $GLOBALS["WWN_CONFIGS"]['AGENTID'];
            $postData["template_card"]["card_type"]                              = "text_notice";
            $postData["template_card"]["source"]["icon_url"]                     = $GLOBALS["WWN_CONFIGS"]['ICON_URL'];
            $postData["template_card"]["source"]["desc"]                         = t("Task Management");
            $postData["template_card"]["task_id"]                                = $eventData["task_id"];
            $postData["template_card"]["main_title"]["title"]                    = t("Task Changed");
            $postData["template_card"]["main_title"]["desc"]                     = $eventData["task"]["title"];
            $postData["template_card"]["emphasis_content"]["title"]              = t("Changed Content");
            $postData["template_card"]["horizontal_content_list"]                = array();
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

            if (!empty($eventData["changes"])){
                foreach($eventData["changes"] as $key => $value){
                    $postData["template_card"]["horizontal_content_list"][] = array(
                        "keyname" => t($key),
                        "value" => $value
                    );
                }
            }

            $this->sendMessage($postData);
        }
    }
}

