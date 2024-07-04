<?php

namespace Modules\SPTransfer\Models;

use App\Contracts\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DB_SPTransfer extends Model
{
    use HasFactory;

    protected $table = 'sptransfer';

    protected $fillable = [
        'user_id',
        'hub_initial',
        'hub_request',
        'reason',
        'state',
    ];
}
