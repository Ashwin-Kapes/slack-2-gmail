<?php

namespace App\Http\Controllers\Auth;

use Socialite;
use Config;
use DB;
use Mail;
use Cookie;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Utils\SlackWrapper;
use App\Utils\GoogleWrapper;
use App\Utils\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class GoogleOauth2Controller extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('google')
            ->scopes(config('services.google.scopes'))
            ->with(["access_type" => "offline", "prompt" => "consent select_account"])
            ->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        try {
            $google = Socialite::driver('google')
                ->stateless()
                ->user();
        } catch (\Exception $e) {
            Log::error($e);
            throw new BadRequestHttpException('Failed to connect, please retry.');
        }

        try {
            $user = $this->findOrCreateUser($google);
            Auth::loginUsingId($user->id);
        } catch (QueryException $e) {
            Log::error($e);
            // throw new BadRequestHttpException('Error occured');
            return redirect('/join');
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            // throw new BadRequestHttpException('Error occured');
            return redirect('/join');
        } catch (Socialite\Two\InvalidStateException $e) {
            Log::error($e);
            // throw new BadRequestHttpException('Error occured');
            return redirect('/join');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect('/join');
        }
        return redirect('/app');
    }

    protected function getFirstname(String $name) {
        $arr = explode(' ', $name);
        return $arr[0];
    }
    protected function getLastname(String $name) {
        $arr = explode(' ', $name, 2);
        return $arr[1];
    }

    protected function findOrCreateUser($info) {
        try {
            $user = User::where('gmail_account_id', '=', $info->getId())->firstOrFail();

            if (property_exists($info, 'token') && $info->token != NULL) {
              $user['gmail_access_token'] = $info->token;
              $user['gmail_expires_at'] = $info->expiresIn + time();
            }
            if (property_exists($info, 'refreshToken') && $info->refreshToken != NULL) {
              $user['gmail_refresh_token'] = $info->refreshToken;
            }
            $user->save();

            if ($user->gmail_refresh_token == NULL || $user->gmail_refresh_token == "") {
                GoogleWrapper::revoke_access($info->token);
                throw new \Exception("Please reconnect.");
            }

            return $user;
        } catch (ModelNotFoundException $e) {
            $uid = User::insertGetId([
                'firstname' => $this->getFirstname($info->getName()),
                'lastname' => $this->getLastname($info->getName()),
                'email' => $info->getEmail(),
                'gmail_account_id' => $info->getId(),
                'gmail_avatar' => $info->getAvatar(),
                'gmail_access_token' => $info->token,
                'gmail_refresh_token' => $info->refreshToken,
                'gmail_expires_at' => $info->expiresIn + time(),
            ], 'id');
            $user = User::where('id', '=', $uid)->firstOrFail();

            return $user;
        }
    }
}
