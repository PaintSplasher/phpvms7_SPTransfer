<?php

namespace Modules\SPTransfer\Widgets;

use App\Contracts\Widget;
use Modules\SPTransfer\Models\DB_SPTransfer;

class InfoBox extends Widget
{
    public function run()
    {
        $pending = DB_SPTransfer::where('state', '0')->count();

        return view('SPTransfer::widgets.infobox', [
            'pending' => $pending ?? '0',
        ]);
    }
}
