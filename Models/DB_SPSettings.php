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
    ];

    // Technically not needed for default fields but here for exampling purposes
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
