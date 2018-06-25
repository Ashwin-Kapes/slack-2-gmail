<?php

namespace App\Models;

use DB;
use Log;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use App\Utils\SlackWrapper;

class SlackOrg extends Model
{
    use Notifiable;

    protected $table = 'slack_orgs';

    public $incrementing = false;
    public $timestamps = false;      // created_at, updated_at

    protected $dateFormat = 'Y-m-d H:i:s.u T';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'name',
        'domain',
        'icon',
        'api_key',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    protected $cast = [
        'id' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function sync_channels() {
        $sw = new SlackWrapper($this->api_key);
        $channels = $sw->channel_list($this->user_id);

        if ($channels->ok == false) {
            Log::error("Cannot sync channels :-( -> " . $channels->error);
            abort(504);
        }

        $channel_ids = [];
        foreach ($channels->channels as $channel)
          $channel_ids[] = $channel->id;

        $removed_channels = SlackChannel::where('user_id', $this->user_id)
          ->where('slack_org_id', $this->id)
          ->whereNotIn('channel_id', $channel_ids)
          ->delete();

        SlackChannel::bulk_upsert($this->user_id, $this->id, $channels->channels);

        // laravel does not like 'timestamp with timezone' pg type :-(
        DB::statement(
            "UPDATE slack_orgs SET synced_at = NOW() WHERE id = '$this->id'"
        );
    }

}
