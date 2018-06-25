<?php

namespace App\Utils;

use Log;
use DB;
use Config;
use Google;
use Swift_Message;
use PulkitJalan\Google\Client;
use Google_Service_Gmail_Message;
use Google_Service_Gmail_Label;
use App\Models\SlackOrg;
use App\Models\SlackChannel;

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
function base64url_decode($data) {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

class GoogleWrapper {

    private $user = NULL;
    private $labels = NULL; // kind of cache

    private $client = NULL;
    private $google_client = NULL;
    private $gmail_service = NULL;
    private $oauth2_service = NULL;

    public function __construct($user)
    {
        $this->user = $user;
        $this->client = new Client([
            'application_name' => 'Slack2Gmail',
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uri' => config('services.google.redirect'),
            'service' => NULL,
            'access_type' => false,
        ]);

        $this->client->setAccessToken([
            'access_token' => $user->gmail_access_token,
            'expires_in' => $user->gmail_expires_at - time(),
            'created' => $user->gmail_expires_at - 3600,
            'token_type' => 'Bearer',
        ]);

        if ($this->client->isAccessTokenExpired()) {
            Log::warning("Google access_token expired for $user->email");
            $this->client->fetchAccessTokenWithRefreshToken($user->gmail_refresh_token);
            $newToken = $this->client->getAccessToken();
            $user->gmail_access_token = $newToken['access_token'];
            $user->gmail_expires_at = $newToken['expires_in'] + time();
            $user->save();
        }

        $this->google_client = $this->client->getClient();
        $this->gmail_service = $this->client->make('gmail');
        $this->oauth2_service = $this->client->make('oauth2');

        try {
            $this->get_user_infos();
        } catch (\Exception $e) {
            // @TODO: sent reconnect email !
            $user->gmail_refresh_token = NULL;
            $user->save();
            throw $e;
        }
    }

    public function get_user_infos()
    {
        return $this->oauth2_service->userinfo->get();
    }

    private function get_label_by_name($name)
    {
        if ($this->labels == NULL)
            $this->labels = $this->gmail_service->users_labels->listUsersLabels('me');
        foreach ($this->labels as $label)
            if ($label->getName() == $name)
                return $label;
        return NULL;
    }

    public function find_or_create_label($name)
    {
        $label = $this->get_label_by_name($name);
        if ($label != NULL)
            return $label;

        $this->labels = NULL;   // flush cache

        $label = new Google_Service_Gmail_Label();
        $label->setName($name);

        return $this->gmail_service->users_labels->create('me', $label);
    }

    public function insert_email($org, $channel, $message)
    {
        $label = $this->find_or_create_label(config('services.gmail.label'));

        $to = 'channel.' . strtolower($channel->channel_id) . config('services.gmail.address_suffix');
        $to_name = "#$channel->name ($org->name)";
        $from = $message['user_type'] . '.' . strtolower($message['user_id']) . config('services.gmail.address_suffix');
        $from_name = $message['username'] . '(' . $message['user_type'] . ')';
        $subject = "Message in channel #$channel->name ($org->domain)";
        $body = "Sent by " . $message['username'] . " in #" . $channel->name . ":\n\n" . $message['body'];
        $date = (int)$message['timestamp'];

        $swiftMsg = new Swift_Message();
        $swiftMsg->setTo([$to => $to_name]);
        $swiftMsg->setFrom([$from => $from_name]);
        $swiftMsg->setSubject($subject);
        $swiftMsg->setBody($body, 'text/plain');
        $swiftMsg->setDate((new \DateTime())->setTimestamp($date));

        $email = new Google_Service_Gmail_Message();
        $email->setRaw(base64url_encode($swiftMsg->toString()));
        $email->setLabelIds([$label->getId()]);
        $email->setInternalDate($date);

        // let's slowdown a bit for rate limits
        usleep(42);

        return $this->gmail_service->users_messages->insert('me', $email, ['internalDateSource' => 'dateHeader']);
    }

    static function revoke_access($access_token)
    {
        $client = new Client([
            'application_name' => 'Slack2Gmail',
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'service' => NULL,
        ]);
        $google_client = $client->getClient();
        $google_client->setAccessToken($access_token);
        $google_client->revokeToken();
    }

}
