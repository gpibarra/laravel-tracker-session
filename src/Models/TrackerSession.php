<?php

namespace gpibarra\TrackerSession\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TrackerSession extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'sessionKey', 'ip_address', 'user_agent', 'login_at', 'see_at', 'logout_at', 'failed_at'
    ];

    public static $enumType = ['auth','failed_auth','oauth','failed_oauth'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'login_at' => 'datetime',
        'see_at' => 'datetime',
        'logout_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        if (! isset($this->table)) {
            $this->setTable(config('tracker-session.table_name'));
        }

        parent::__construct($attributes);
    }

    /**
     * Get the authenticatable entity that the authentication log belongs to.
     */
    public function authenticatable() :MorphTo
    {
        return $this->morphTo();
    }

}
