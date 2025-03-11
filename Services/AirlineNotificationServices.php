<?php

namespace Modules\SPTransfer\Services;

use App\Models\Airline;
use Illuminate\Support\Facades\Log;
use Modules\SPTransfer\Models\DB_SPSettings;

class AirlineNotificationServices
{
    public function TransferMessage($transfer)
    {
        $setting = DB_SPSettings::where('id', 2)->first();
        $wh_url = filled($setting) ? $setting->discord_url : null;
        $airlines = Airline::where('active', 1)->orderBy('icao')->select('id', 'icao', 'name')->get();

        $airlineNameinitial = $airlines->firstWhere('id', $transfer->hub_initial_id);
        $airlineNameRequest = $airlines->firstWhere('id', $transfer->hub_request_id);

        $json_data = json_encode([
            // Plain text message
            'username' => 'Notification for '.config('app.name'),
            'tts'      => false,
            'embeds'   => [
                // Embed content
                [
                    'title'     => '**New Airline Change Request**',
                    'type'      => 'rich',
                    'timestamp' => date('c', strtotime($transfer->created_at)),
                    'color'     => hexdec('2980B9'),
                    'thumbnail' => [
                        'url' => !empty($transfer->user->avatar) ? $transfer->user->avatar->url : $transfer->user->gravatar(256),
                    ],
                    'author'    => [
                        'name' => $transfer->user->name_private,
                        'url'  => route('frontend.profile.show', [$transfer->user_id]),
                    ],
                    // Additional embed fields (Discord displays max 3 items per row)
                    'fields' => [
                        [
                            'name'   => '__Current__',
                            'value'  => $airlineNameinitial->icao,
                            'inline' => true,
                        ], [
                            'name'   => '__Requested__',
                            'value'  => $airlineNameRequest->icao,
                            'inline' => true,
                        ], [
                            'name'   => '__Reason__',
                            'value'  => $transfer->reason,
                            'inline' => true,
                        ],
                    ],
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $this->DiscordNotification($wh_url, $json_data);
    }

    public function DiscordNotification($webhook_url, $json_data)
    {
        $ch = curl_init($webhook_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if ($response) {
            Log::debug('SPTransfer | Discord WebHook Msg Response: '.$response);
        }
        curl_close($ch);
    }
}
