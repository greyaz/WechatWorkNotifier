<?php

namespace Kanboard\Plugin\WechatWorkNotifier\Helper;

use Kanboard\Core\Base;

class MessageHelper extends Base
{
    private $lastRoundAudiences = array();
    private $lastRoundTime = 0;
    private $notificationInterval = 1;

    public function __construct($c) {
        parent::__construct($c);
        if(!empty($GLOBALS['WWN_CONFIGS']['NOTIFICATION_INTERVAL'])){
            $this->notificationInterval = $GLOBALS['WWN_CONFIGS']['NOTIFICATION_INTERVAL'];
        }
    }

    public function send($audiences, $message)
    {
        $result = false;

        if ($this->getToken()){
            $result = $this->doSend($this->getToken(), $audiences, $message);
        }

        if (! $result){
            $result = $this->doSend($this->getToken(true), $audiences, $message);
        }
        return $result;
    }

    public function getAudiences($project, $eventData, $assigneeOnly = false){
        $audiences = array();

        $owner = $this->userModel->getById($eventData["task"]["owner_id"]);
        if (!empty($owner) && !empty($owner['email']))
        {
            $audiences[] = $owner['email'];
        }
        
        if (!$assigneeOnly)
        {
            $projectOwner = $this->userModel->getById($project["owner_id"]);
            if (!empty($projectOwner) && !empty($projectOwner['email']))
            {
                $audiences[] = $projectOwner['email'];
            }

            $creator = $this->userModel->getById($eventData["task"]["creator_id"]);
            if (!empty($creator) && !empty($creator['email'])) {
                $audiences[] = $creator['email'];
            }

            $multimembers = isset($this->multiselectMemberModel) ? $this->multiselectMemberModel->getMembers($eventData["task"]['owner_ms']) : null;
            if (!empty($multimembers)){
                foreach ($multimembers as $member) {
                    $user = $this->userModel->getById($member['id']);
                    if (!empty($user['email'])) {
                        $audiences[] = $user['email'];
                    }
                }
            }
            
            $groupmembers = isset($this->groupMemberModel) ? $this->groupMemberModel->getMembers($eventData["task"]['owner_gp']) : null;
            if (!empty($groupmembers)){
                foreach ($groupmembers as $member) {
                    $user = $this->userModel->getById($member['id']);
                    if (!empty($user['email'])) {
                        $audiences[] = $user['email'];
                    }
                }
            }
        }

        return array_unique($audiences);
    }

    public function getTaskLink($taskId, $commentId = null){
        $taskLink = $this->getKanboardURL()."/task/".$taskId;
        if (!empty($commentId)){
            $taskLink .= "#comment-".$commentId;
        }
        return $taskLink;
    }

    public function getProjectLink($projectId){
        return $this->getKanboardURL()."/board/".$projectId;
    }

    private function getKanboardURL(){
        $url = $GLOBALS["WWN_CONFIGS"]["KANBOARD_URL"];
        if (strrpos($url, '/', -1) == strlen($url) - 1){
            $url = substr($url, 0, -1);
        }
        return $url;
    }

    private function doSend($token, $audiences, $jsonTemplate){
        if ($token){
            $prevAudiences = $this->lastRoundAudiences;
            // try
            try{
                $jsonTemplate["touser"] = implode("|", $this->getFilteredAudiencesAndSetLast($audiences));
                // send
                $result = $this->httpClient->doRequest(
                    'POST',
                    "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=".$token,
                    json_encode($jsonTemplate, JSON_UNESCAPED_UNICODE),
                    ['Content-type: application/json']
                );
                $result = json_decode($result);
                // result
                if ($result->errcode == 0){
                    return true;
                }
                else{
                    $this->lastRoundAudiences = $prevAudiences;
                    $this->logger->debug(serialize($result));
                }
            }
            // catch error
            catch(Exception $error){
                $this->lastRoundAudiences = $prevAudiences;
                $this->logger->debug(serialize($error));
            }
        }
        return false;
    }

    private function getFilteredAudiencesAndSetLast($audiences){
        $time = time();
        if ($time - $this->lastRoundTime > $this->notificationInterval){
            $this->lastRoundTime = $time;
            $this->lastRoundAudiences = $audiences;
            return $audiences;
        }
        else{
            $newAudiences = array_diff($audiences, array_intersect($this->lastRoundAudiences, $audiences));
            $this->lastRoundAudiences = array_merge($this->lastRoundAudiences, $newAudiences);
            return $newAudiences;
        }
    }

    private function getToken($force = false){
        if (! session_exists("WWN_TOKEN") || $force){
            $token = $this->getRemoteToken(
                $GLOBALS["WWN_CONFIGS"]["CORPID"],
                $GLOBALS["WWN_CONFIGS"]["SECRET"]
            );

            if ($token){
                session_set("WWN_TOKEN", $token);
            }
        }
        return session_get("WWN_TOKEN");
    }

    private function getRemoteToken($corpid, $secret){
        try{
            $data = $this->httpClient->getJson("https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=".$corpid."&corpsecret=".$secret);
            if (isset($data["access_token"])){
                return $data["access_token"];
            }
        }
        catch(Exception $error){
            $this->logger->debug(serialize($error));
        }
        return "";
    }
}
