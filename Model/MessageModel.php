<?php

namespace Kanboard\Plugin\WechatWorkNotifier\Model;

class MessageModel
{
    public static function create($taskId, $title, $subTitle, $key, $desc, $quoteTitle, $quote, $contentList, $taskLink, $projectLink){
        $message = array();

        $message["msgtype"]                                                 = "template_card";
        $message["agentid"]                                                 = $GLOBALS["WWN_CONFIGS"]['AGENTID'];
        $message["template_card"]["card_type"]                              = "text_notice";
        $message["template_card"]["task_id"]                                = $taskId;

        $message["template_card"]["source"]["icon_url"]                     = $GLOBALS["WWN_CONFIGS"]['ICON_URL'];
        $message["template_card"]["source"]["desc"]                         = t("Task Management");

        $message["template_card"]["main_title"]["title"]                    = $title;
        if (isset($subTitle))
        {
            $message["template_card"]["main_title"]["desc"]                 = $subTitle;
        }

        $message["template_card"]["emphasis_content"]["title"]              = $key;
        if (isset($desc))
        {
            $message["template_card"]["emphasis_content"]["desc"]           = $desc;
        }

        if (isset($quoteTitle))
        {
            $message["template_card"]["quote_area"]["title"]           = $quoteTitle;
        }

        if (isset($quote))
        {
            $message["template_card"]["quote_area"]["quote_text"]           = $quote;
        }

        if (isset($contentList)){
            foreach($contentList as $key => $value){
                $message["template_card"]["horizontal_content_list"][]      = array("keyname" => $key, "value" => $value);
            }
        }

        $message["template_card"]["jump_list"][0]["type"]                   = "1";
        $message["template_card"]["jump_list"][0]["title"]                  = t("View the task");
        $message["template_card"]["jump_list"][0]["url"]                    = $taskLink;
        $message["template_card"]["jump_list"][1]["type"]                   = "1";
        $message["template_card"]["jump_list"][1]["title"]                  = t("View the kanban");
        $message["template_card"]["jump_list"][1]["url"]                    = $projectLink;

        $message["template_card"]["card_action"]["type"]                    = "1";
        $message["template_card"]["card_action"]["url"]                     = $taskLink;
        
        return $message;
    }
}