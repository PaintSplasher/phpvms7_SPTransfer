<?php

namespace Modules\SPTransfer\Models;

use App\Contracts\Model;
use App\Models\Airport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Kyslik\ColumnSortable\Sortable;

class DB_SPTransfer extends Model
{
    use HasFactory;
    use Sortable;

    protected $table = 'sptransfer';

    protected $fillable = [
        'user_id',
        'hub_initial_id',
        'hub_request_id',
        'reason',
        'reject_reason',
        'state',
        'airline',
    ];

    // Technically not needed for default fields but here for exampling purposes
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        // 'deleted_at' => 'datetime',
    ];

    // Better to define them here too, should follow the DB structure and code, gives an insight about stuff easily
    public static $rules = [
        'user_id'        => 'required',
        'hub_initial_id' => 'optional|max:5',
        'hub_request_id' => 'required|max:5',
        'reason'         => 'nullable',
        'reject_reason'  => 'nullable',
        'state'          => 'nullable',
        'airline'        => 'nullable',
    ];

    // Allows sortable columns in views without performance loss
    public $sortable = [
        'id',
        'user_id',
        'hub_initial_id',
        'hub_request_id',
        'reason',
        'state',
        'airline',
        'created_at',
        'updated_at',
    ];

    // User Model Relationship
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    // Airport Model Relationship - Current Hub
    public function hub_initial(): HasOne
    {
        return $this->hasOne(Airport::class, 'id', 'hub_initial_id');
    }

    // Airport Model Relationship - Requested Hub
    public function hub_request(): HasOne
    {
        return $this->hasOne(Airport::class, 'id', 'hub_request_id');
    }
}
