<?php

namespace Kanboard\Plugin\WechatWork\Notification;

use Kanboard\Core\Base;
use Kanboard\Plugin\WechatWork\Plugin;

class BaseNotification extends Base
{
    private function sendMessage($jsonTemplate)
    {
        $result = false;

        if ($this->getToken()){
            $result = $this->doSend($this->getToken(), $jsonTemplate);
        }

        if (! $result){
            $result = $this->doSend($this->getToken(true), $jsonTemplate);
        }
        return $result;
    }

    private function doSend($token, $jsonTemplate){
        if ($token){
            try{
                $result = $this->httpClient->doRequest(
                    'POST',
                    "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=".$token,
                    json_encode($jsonTemplate, JSON_UNESCAPED_UNICODE),
                    ['Content-type: application/json']
                );
                $result = json_decode($result);
                if ($result->errcode == 0){
                    return true;
                }
                $this->logger->debug(serialize($result));
            }
            catch(Exception $error){
                $this->logger->debug(serialize($error));
            }
        }
        return false;
    }

    private function getToken($force = false){
        if (! session_exists("WORK_WEIXIN_TOKEN") || $force){
            $token = $this->getRemoteToken(
                $this->getPlugin()->configs["CORPID"],
                $this->getPlugin()->configs["SECRET"]
            );

            if ($token){
                session_set("WORK_WEIXIN_TOKEN", $token);
            }
        }
        return session_get("WORK_WEIXIN_TOKEN");
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

    private function getAudiences($eventData, $assigneeOnly = false){
        $audiences = array();

        $owner = $this->userModel->getById($eventData["task"]["owner_id"]);
        if (!empty($owner) && !empty($owner['email']))
        {
            $audiences[] = $owner['email'];
        }
        
        if (!$assigneeOnly)
        {
            $creator = $this->userModel->getById($eventData["task"]["creator_id"]);
            if (!empty($creator) && !empty($creator['email'])) {
                $audiences[] = $creator['email'];
            }

            $multimembers = $this->multiselectMemberModel->getMembers($eventData["task"]['owner_ms']);
            if (!empty($multimembers)){
                foreach ($multimembers as $member) {
                    $user = $this->userModel->getById($member['id']);
                    if (!empty($user['email'])) {
                        $audiences[] = $user['email'];
                    }
                }
            }
            
            $groupmembers = $this->groupMemberModel->getMembers($eventData["task"]['owner_gp']);
            if (!empty($groupmembers)){
                foreach ($groupmembers as $member) {
                    $user = $this->userModel->getById($member['id']);
                    if (!empty($user['email'])) {
                        $audiences[] = $user['email'];
                    }
                }
            }
        }

        return implode("|", array_unique($audiences));
    }

    private function getKanboardURL(){
        $url = $this->getPlugin()->configs["KANBOARD_URL"];
        if (strrpos($url, '/', -1) == strlen($a) - 1){
            $url = substr($url, 0, -1);
        }
        return $url;
    }
    
    private function getPlugin(){
        return Plugin::getInstance($this->container);
    }
}
