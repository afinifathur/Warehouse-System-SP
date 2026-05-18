<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WmsTerminalTakeoverLog extends Model
{
    public $timestamps = false; // Manually populated or DB useCurrent()

    protected $table = 'wms_terminal_takeover_logs';

    protected $fillable = [
        'workflow',
        'terminal_id',
        'previous_owner',
        'new_owner',
        'takeover_reason',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
