<?php

namespace App\Http\Controllers\Auth;

use Config;
use Validator;
use App\Models\User;
use App\Models\SlackOrgs;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login');
    }
}
