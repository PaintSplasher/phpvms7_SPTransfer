<?php

namespace Modules\SPTransfer\Models;

use App\Contracts\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DB_SPSettings extends Model
{
    use HasFactory;

    protected $table = 'sptransfer_settings';

    protected $fillable = [
        'sp_price',
        'sp_days',
        'discord_url',
        'charge_type',
    ];

    // Better to define them here too, should follow the DB structure and code, gives an insight about stuff easily
    public static $rules = [
        'sp_price'    => 'optional|numeric',
        'sp_days'     => 'optional|numeric',
        'discord_url' => 'nullable|string|max:191',
        'charge_type' => 'optional|numeric',
    ];

    // Technically not needed for default fields but here for exampling purposes
    protected $casts = [
        'sp_price'    => 'float',
        'sp_days'     => 'integer',
        'discord_url' => 'string',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'charge_type' => 'integer',
    ];
}
