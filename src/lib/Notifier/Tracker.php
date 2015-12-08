<?php

namespace Notifier;


use CL\Slack\Payload\ChatPostMessagePayload;
use CL\Slack\Transport\ApiClient;
use GuzzleHttp\Client;
use Helpers\Cacher;
use Pheal\Pheal;
use Pheal\Access\StaticCheck;
use Pheal\Core\Config;
use Pheal\Cache\FileStorage as FileCache;
use Pheal\Log\FileStorage as FileLog;

class Tracker
{
    /** @var Pheal */
    private $pheal;
    /** @var Cacher */
    private $cache;
    /** @var ApiClient */
    private $slack;
    /** @var ChatPostMessagePayload */
    private $payload;

    function __construct()
    {
        // Configure Pheal
        Config::getInstance()->cache = new FileCache('tmp/cache/');
        Config::getInstance()->access = new StaticCheck();
        Config::getInstance()->log = new FileLog('tmp/log/');
        Config::getInstance()->http_ssl_verifypeer = false;
        Config::getInstance()->http_user_agent = USER_AGENT;

        // Load and configure classes
        $this->pheal = new Pheal(API_KEYID, API_VCODE);
        $this->cache = new Cacher();
        $options = $this->GetClientOptions();
        $guzzle = new Client($options);
        $this->slack = new ApiClient(SLACK_TOKEN, $guzzle);
        $this->payload = new ChatPostMessagePayload();
        $this->payload->setChannel(SLACK_CHANNEL);
        $this->payload->setUsername(SLACK_USERNAME);
    }

    function Execute()
    {
        $notifications = $this->GetNotificationData();
        $left = $this->GetMemberLeft($notifications);
        $last = $this->GetLastLeftNotification();
        $this->SendNotifications($left, $last);
    }

    /**
     * @return \Pheal\Core\RowSetRow
     */
    function GetNotificationData()
    {
        return $this->pheal->charScope->Notifications(['characterID' => CHAR_ID]);
    }

    /**
     * @param \Pheal\Core\RowSetRow $notifications
     * @return array
     */
    function GetMemberLeft($notifications)
    {
        $left = array();

        foreach ($notifications->notifications as $notification) {
            if ((int)$notification->typeID === 21) {
                $left[] = $notification;
            }
        }

        return $left;
    }

    /**
     * @return array
     */
    function GetLastLeftNotification()
    {
        $notification = $this->cache->LoadArray("lastMemberLeft");
        if (!empty($notification)) {
            return $notification;
        }

        // Last member leaving not saved, assume none have been recorded yet.
        return array('notificationID' => 0);
    }

    /**
     * Update our cache with the last member that left corp
     * @param \Pheal\Core\RowSetRow $notification
     */
    function UpdateLastLeft($notification)
    {
        $this->cache->SaveArray($notification, "lastMemberLeft");
    }

    /**
     * Disable Guzzle/Curl HTTPS verification on Windows
     * @return array
     */
    private function GetClientOptions()
    {
        if (PHP_OS === "WINNT") {
            return array('verify' => false);
        }

        return array();
    }

    /**
     * Send a notification about members who last left the corp.
     * First check if notification has not already been sent.
     * @param array $left Array of RowSetRow objects from Pheal
     * @param array $last Associative array
     */
    private function SendNotifications($left, $last)
    {
        foreach ($left as $member) {
            if ((int)$member->notificationID > (int)$last['notificationID']) {
                $message = $member->senderName . " left corp at " . $member->sentDate;
                $payload = $this->payload;
                $payload->setText($message);
                $response = $this->slack->send($payload);
                if ($response->isOk()) {
                    $this->UpdateLastLeft($member);
                }
            }
        }
    }
}
