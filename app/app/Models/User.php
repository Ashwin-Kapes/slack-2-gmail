<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    public $incrementing = false;
    public $timestamps = false;      // created_at, updated_at

    protected $primaryKey = "id";

    protected $dateFormat = 'Y-m-d H:i:s.u T';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'firstname',
        'lastname',
        'email',
        'gmail_account_id',
        'gmail_avatar',
        'gmail_access_token',
        'gmail_refresh_token',
        'gmail_expires_at',
        'flags',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'flags',
        'gmail_access_token',
        'gmail_refresh_token',
        'gmail_expires_at',
    ];

    protected $cast = [
        'id' => 'string',
        'gmail_expires_at' => 'integer',
        'flags' => 'object',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function full_name() {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getRememberTokenName() {
      return null; // not supported
    }

}
