<?php

namespace App\Models;

use DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use App\Utils\SlackWrapper;

class SlackChannel extends Model
{
    use Notifiable;

    protected $table = 'slack_channels';

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
        'slack_org_id',
        'channel_id',
        'name',
        'is_member',
        'is_archived',
        'is_private',
        'num_members',
        'is_backup_enabled',
        'sync_cursor',
        'synced_at',
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
        'is_member' => 'boolean',
        'is_archived' => 'boolean',
        'is_private' => 'boolean',
        'num_members' => 'integer',
        'is_backup_enabled' => 'boolean',
        'sync_cursor' => 'real',
        'synced_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
    public function slack_org() {
        return $this->belongsTo('App\Models\SlackOrg', 'slack_org_id');
    }

    public function move_scraping_cursor($cursor_pos) {
        // laravel does not like 'timestamp with timezone' pg type :-(
        if ($cursor_pos != NULL)
            DB::statement("UPDATE slack_channels SET synced_at = NOW(), sync_cursor = $cursor_pos WHERE id = '$this->id'");
        else
            DB::statement("UPDATE slack_channels SET synced_at = NOW() WHERE id = '$this->id'");
    }

    static function bulk_upsert($user_id, $slack_org_id, $channels) {
        DB::statement(
            "
            INSERT INTO slack_channels(user_id, slack_org_id, channel_id, name, is_member, is_archived, is_private, num_members)

                VALUES
            "

            .

            implode(", ", array_map(function ($c) use ($user_id, $slack_org_id) {
                $is_member = $c->is_member ? 'TRUE' : 'FALSE';
                $is_archived = $c->is_archived ? 'TRUE' : 'FALSE';
                $is_private = $c->is_private ? 'TRUE' : 'FALSE';
                return "('$user_id', '$slack_org_id',  '$c->id', '$c->name', $is_member, $is_archived, $is_private, $c->num_members)
                ";
            }, $channels))

            .

            "
                ON CONFLICT (user_id, slack_org_id, channel_id)
                    DO UPDATE SET
                        name = EXCLUDED.name,
                        is_member = EXCLUDED.is_member,
                        is_archived = EXCLUDED.is_archived,
                        is_private = EXCLUDED.is_private,
                        num_members = EXCLUDED.num_members
            ;
            "
        );
    }

}
