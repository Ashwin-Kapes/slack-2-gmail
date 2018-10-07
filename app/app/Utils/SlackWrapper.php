<?php

namespace App\Utils;

use Log;
use DB;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Models\SlackOrg;
use App\Models\SlackChannel;

class SlackWrapper {

    // python3 -m http.server 8080
    // private $url = 'http://1.2.3.4:8080/';
    private $url = 'https://slack.com/api/';

    private $token = NULL;
    private $client = NULL;

    public function __construct($token)
    {
        $this->token = $token;

        $this->client = new Client(['base_uri' => $this->url, 'verify' => true]);
        if (method_exists($this->client, 'setDefaultOption')) {
            $this->client->setDefaultOption('verify', false);
        }
    }


    protected function mergeParams($verb, $params)
    {
      $params['token'] = $this->token;
      $params['t'] = time();

      if (isset($params['attachments']) and is_array($params['attachments'])) {
        $params['attachments'] = json_encode($params['attachments']);
      }

      $options = [];
      $options[strtolower($verb) == 'get' ? 'query' : 'form_params'] = $params;
      // Log::info($options);
      return $options;
    }

    protected function request($verb, $method, $params = [])
    {
        $params = $this->mergeParams($verb, $params);

        // quick and dirty
        try {
            try {
                $params['headers'] = ['User-Agent' => 'github.com/samber/slack-2-gmail'];
                $response = $this->client->$verb($method, $params);
            } catch (\InvalidArgumentException $e) {
                $params['body'] = $params['form_params'];
                unset($params['form_params']);
                $response = $this->client->$verb($method, $params);
            }
        } catch (\Exception $e) {
            return (object)[
                'ok' => false,
                'error' => 'Unkown error',
            ];
        }
        return $this->responseToJson($response);
    }

    protected function responseToJson($response)
    {
        return json_decode($response->getBody()->getContents());
    }

    // team.info => max 50 req/min per app per workspace
    public function org_info()
    {
        return $this->request('get', 'team.info');
    }

    // channel.info => max 50 req/min per app per workspace
    public function channel_info($channel_id)
    {
        return $this->request('get', 'channel.info', [
          'channel' => $channel_id,
        ]);
    }

    // channel.list => max 20 req/min per app per workspace
    public function channel_list()
    {
        return $this->request('get', 'channels.list');
    }

    // channel.history => max 50 req/min per app per workspace
    public function channel_history($channel_id, $oldest = NULL, $latest = NULL, $count = 1000)
    {
        return $this->request('get', 'channels.history', [
          'channel' => $channel_id,
          'count' => $count,
          'oldest' => $oldest,
          'latest' => $latest,
          'inclusive' => false,
        ]);
    }

    // `channel_history` get messages from `newest` -> `oldest` with limit to `count`
    // `channel_history_reverse` do the same but from `oldest` to  `newest`
    public function channel_history_reverse($channel_id, $oldest = NULL, $count = 1000)
    {
        // @TODO
    }

    public function auth_test(): bool
    {
        try {
            $result = $this->request('get', 'auth.test')->ok;
        } catch (GuzzleException $e) {
            Log::error($e);
            $result = false;
        }
        return $result;
    }


    // users.info => get information about a user id
    public function users_info($user_id)
    {
        return $this->request('get', 'users.info', [
          'user' => $user_id,
        ]);
    }

}
