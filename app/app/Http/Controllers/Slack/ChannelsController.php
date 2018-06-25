<?php

namespace App\Http\Controllers\Slack;

use Config;
use Validator;
use App\Models\User;
use App\Models\SlackOrg;
use App\Models\SlackChannel;
use App\Utils\SlackWrapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSlackChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ChannelsController extends Controller
{
    public function list(Request $request, String $slack_org_id)
    {
        $user = Auth::user();

        $org = SlackOrg::where('user_id', $user->id)
            ->where('id', $slack_org_id)
            ->firstOrFail();
        $org->sync_channels();

        $channels = SlackChannel::where('user_id', $user->id)
            ->where('slack_org_id', $slack_org_id)
            ->orderBy('name', 'asc')
            ->get();

        return response(view('pages.slack.channels-list', [
            'user' => $user,
            'org' => $org,
            'channels' => $channels,
        ]));
    }

    public function update(UpdateSlackChannel $request, String $slack_org_id)
    {
        $user = Auth::user();

        $channels = SlackChannel::where('user_id', $user->id)
        ->where('slack_org_id', $slack_org_id)
        ->update([
          'is_backup_enabled' => false,
          ]);

        if (array_key_exists('channels', $request->all())) {
            $channel_ids = $request->validated()['channels'];
            $channels = SlackChannel::where('user_id', $user->id)
                ->where('slack_org_id', $slack_org_id)
                ->whereIn('id', $channel_ids)
                ->update([
                  'is_backup_enabled' => true,
                ]);
        }

        return redirect('/slack/orgs/'.$slack_org_id.'/channels');
    }
}
