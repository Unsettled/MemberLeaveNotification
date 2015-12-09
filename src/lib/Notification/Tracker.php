<?php

namespace Notification;


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
        Config::getInstance()->cache = new FileCache(WORKING_DIR . '/tmp/cache/');
        Config::getInstance()->access = new StaticCheck();
        Config::getInstance()->log = new FileLog(WORKING_DIR . '/tmp/log/');
        Config::getInstance()->http_ssl_verifypeer = false;
        Config::getInstance()->http_user_agent = USER_AGENT;

        // Load and configure classes
        $this->pheal = new Pheal(API_KEYID, API_VCODE);
        $this->cache = new Cacher();
        $options = $this->GetClientOptions();
        $guzzle = new Client($options);
        $this->slack = new ApiClient(SLACK_TOKEN, $guzzle);
        $this->payload = new ChatPostMessagePayload();
        $this->payload->setUsername(SLACK_USERNAME);
    }

    /**
     * @param Type[] $notificationTypes
     */
    function Execute($notificationTypes)
    {
        $notifications = $this->GetNotificationData();

        foreach($notificationTypes as $type) {
            $typeNotifications = $this->GetRequestedNotifications($notifications, $type->ID);
            $lastNotificationID = $this->GetLastNotificationID($type->name);
            $this->SendNotifications($typeNotifications, $lastNotificationID, $type);
        }
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
     * @param int $typeID
     * @return array
     * @internal param $eventID
     */
    function GetRequestedNotifications($notifications, $typeID)
    {
        $members = array();

        foreach ($notifications->notifications as $notification) {
            if ((int)$notification->typeID === $typeID) {
                $members[] = json_decode(json_encode($notification), true);
            }
        }

        return $members;
    }

    /**
     * @param string $action
     * @return int
     */
    function GetLastNotificationID($action)
    {
        $notificationID = $this->cache->LoadData("last$action");
        if (!empty($notificationID)) {
            return (int)$notificationID;
        }

        // Last member leaving not saved, assume none have been recorded yet.
        return 0;
    }

    /**
     * Update our cache with the last member that left corp
     * @param int $notification
     * @param string $typeName
     */
    function UpdateLastNotification($notification, $typeName)
    {
        $this->cache->SaveData($notification, "last$typeName");
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
     * Sends a message to Slack with notification info.
     * First check if notification has not already been sent.
     * @param array $notifications
     * @param int $last The previous notification of the same type
     * @param Type $type
     * @throws \CL\Slack\Exception\SlackException
     */
    private function SendNotifications($notifications, $last, $type)
    {
        foreach ($notifications as $notification) {
            $currentNotificationID = (int)$notification['notificationID'];
            if ($currentNotificationID > $last) {
                $message = "{$notification['senderName']} {$type->messageText} at {$notification['sentDate']}";
                $payload = $this->payload;
                $payload->setChannel($type->channel);
                $payload->setText($message);
                $response = $this->slack->send($payload);
                if ($response->isOk()) {
                    $this->UpdateLastNotification($currentNotificationID, $type->name);
                }
            }
        }
    }
}
