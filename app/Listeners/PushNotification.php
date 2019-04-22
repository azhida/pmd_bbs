<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\DatabaseNotification;
use JPush\Client;

class PushNotification
{
    protected $client;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(DatabaseNotification $notification)
    {
        // 本地环境默认不推送
        if (app()->environment('local')) {
            return;
        }

        $user = $notification->notifiable;

        // 没有 registration_id 的不推送
        if (!$user->registration_id) {
            return;
        }

        // 推送消息
        $pusher = $this->client->push();
        $pusher->setPlatform('all');
        $pusher->addRegistrationId($user->registration_id);
        $pusher->setNotificationAlert(strip_tags($notification->data['reply_content']));
        try {
            $pusher->send();
        } catch (\JPush\Exceptions\JPushException $e) {
            \Log::error('极光推送：' . $e->getMessage());
        }
    }
}
