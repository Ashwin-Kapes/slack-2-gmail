<?php

namespace App\Console\Commands;

use Log;
use Illuminate\Console\Command;
use App\Models\SlackChannel;
use App\Utils\SlackWrapper;
use App\Utils\GoogleWrapper;

class SyncChannels extends Command
{
    protected $SLEEP_TIME = 2;
    protected $BATCH_SIZE = 100;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slack:sync:channels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync slack channels';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // quick and dirty
        while (42) {
            // quick and dirty
            try {
                $channel = $this->fetchSlackChannelToScrape();
                if ($channel != NULL) {
                    $org = $channel->slack_org;
                    $user = $channel->user;
                    Log::info("Sync'ing channel $channel->channel_id, cursor: $channel->sync_cursor");

                    // fetch messages
                    $channel->sync_cursor = $this->fetchMessages($org, $user, $channel);
                }
            } catch (\Exception $e) {
                // catch all motherfucker
                Log::error($e);
            }

            // sleep for preserving rate limits
            // quick and dirty
            sleep($this->SLEEP_TIME);
        }
    }

    // here, we fetch messages in a loop, from `latest` to `oldest` (reverse order)
    private function fetchMessages($org, $user, $channel)
    {
        $sw = new SlackWrapper($org->api_key);
        $gw = NULL;
        try {
            $gw = new GoogleWrapper($user);
        } catch (\Exception $e) {
            Log::error($e);
            return $channel->sync_cursor;
        }

        $oldest = $channel->sync_cursor;
        $latest = NULL;

        // we fetch from the most recent msg until the oldest msg,
        // while has_more is true
        try {
            do {
                $history = $sw->channel_history($channel->channel_id, $oldest = $oldest, $latest = $latest, $count = $this->BATCH_SIZE);
                if ($history->ok == false)
                    throw new Exception($history->error);

                // we process every messages and forward to gmail
                foreach ($history->messages as $message) {
                    $msg = $this->parseMessage($message);

                    if ($msg != NULL)
                        $gw->insert_email($org, $channel, $msg);

                    // move local cursor
                    $latest = $message->ts;

                    // move cursor in db
                    if ($channel->sync_cursor < $message->ts) {
                        $channel->sync_cursor = $message->ts;
                        $channel->move_scraping_cursor($channel->sync_cursor);
                    }

                }

                // if we still have some messages to read, we sleep few seconds ;-)
                if ($history->has_more == true)
                    sleep($this->SLEEP_TIME);
            }
            while (count($history->messages) == $this->BATCH_SIZE && $history->has_more == true);
        } catch (\Exception $e) {
            // Looks like Exceptions happen here, when we exceed 10k messages limit
            // quick and dirty
            Log::info($e);
        }

        // room is empty
        if ($channel->sync_cursor == 0)
            $channel->sync_cursor = time();

        return $channel->sync_cursor;
    }

    private function parseMessage($message)
    {
        if ($message->type == 'message') {
            $username = '';
            $user_type = '';
            $user_id = '';
            if (property_exists($message, 'subtype') && $message->subtype == 'bot_message') {
                $user_type = 'bot';
                $user_id = $message->bot_id;
                if (property_exists($message, 'username'))
                    $username = $message->username;
                else
                    $username = $message->bot_id;
            } elseif (property_exists($message, 'subtype') == false) {
                // here, `user` is something like U123456789 -> we should use the real user name !
                $user_type = 'user';
                $user_id = $message->user;
                $username = $message->user;
            } else {
                return NULL;
            }

            $body = $message->text;
            if (property_exists($message, 'attachment')) {
                // @TODO: append to message the attachement
            }

            // @TODO: format correctly the message for gmail !

            // Log::info((array)$message);
            Log::info($username . ' said: "' . $body . '" at ' . (string)$message->ts);
            return [
                'user_type' => $user_type,
                'user_id' => $user_id,
                'username' => $username,
                'body' => $body,
                'timestamp' => $message->ts,
            ];
        }
        return NULL;
    }

    private function fetchSlackChannelToScrape()
    {
        return SlackChannel::with(['user', 'slack_org'])
            ->where('is_backup_enabled', true)
            ->orderBy("synced_at", "ASC NULLS FIRST")
            ->first();
    }
}
