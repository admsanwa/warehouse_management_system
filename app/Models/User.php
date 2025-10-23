<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Request;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'fullname',
        'department',
        'email',
        'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    static public function getRecord()
    {
        $return = self::select('users.*');

        // search box start
        if (!empty(Request::get('fullname'))) {
            $return = $return->where('fullname', '=', Request::get('fullname'));
        }
        if (!empty(Request::get('nik'))) {
            $return = $return->where('nik', '=', Request::get('nik'));
        }
        if (!empty(Request::get('department'))) {
            $return = $return->where('department', '=', Request::get('department'));
        }
        if (!empty(Request::get('email'))) {
            $return = $return->where('email', '=', Request::get('email'));
        }

        $return = $return->orderBy('id', 'desc')->paginate(10);
        return $return;
    }

    public function get_job_single()
    {
        return $this->belongsTo(JobsModel::class, 'job_id');
    }
}
