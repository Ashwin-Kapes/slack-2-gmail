<?php

namespace App\Http\Controllers\Slack;

use Config;
use Validator;
use App\Models\User;
use App\Models\SlackOrg;
use App\Models\SlackChannel;
use App\Utils\SlackWrapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddSlackOrg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class OrgsController extends Controller
{
    public function list(Request $request)
    {
        $user = Auth::user();

        $orgs = SlackOrg::where('user_id', $user->id)->orderBy('created_at', 'asc')->get();

        return response(view('pages.slack.orgs-list', [
            'user' => $user,
            'orgs' => $orgs,
        ]));
    }

    public function new(AddSlackOrg $request)
    {
        $user = Auth::user();

        $token = $request->validated()['token'];

        $sw = new SlackWrapper($token);
        $org = $sw->org_info();
        if ($org->ok == false) {
          Log::error($org->error);
          return redirect('/slack/orgs')
            ->withErrors([
                'token' => $org->error,
            ])
            ->withInput();
        }

        // Log::error((array)$org);
        $slack_org_id = SlackOrg::insertGetId([
          'user_id' => $user->id,
          'name' => $org->team->name,
          'domain' => $org->team->domain,
          'icon' => $org->team->icon->image_44,
          'api_key' => $token,
        ], 'id');

        $org = SlackOrg::where('user_id', $user->id)
          ->where('id', $slack_org_id)
          ->firstOrFail();
        $org->sync_channels();

        return redirect('/slack/orgs/'.$slack_org_id.'/channels');
        // return redirect('/slack/orgs');
    }

    public function delete(Request $request, String $id)
    {
        $user = Auth::user();
        $org = SlackChannel::where('user_id', $user->id)
          ->where('slack_org_id', $id)
          ->delete();
        $org = SlackOrg::where('user_id', $user->id)
          ->where('id', $id)
          ->delete();
        return redirect('/slack/orgs');
    }
}
