<?php

namespace Modules\SPTransfer\Services;

use Illuminate\Support\Facades\Log;
use League\HTMLToMarkdown\HtmlConverter;
use Modules\SPTransfer\Models\DB_SPSettings;

class NotificationServices
{

    public function TransferMessage($transfer)
    {
        $setting = DB_SPSettings::first();
        $wh_url = $setting ? $setting->discord_url : null;

        $json_data = json_encode([
            // Plain text message
            'username' => 'SPTransfer' : config('app.name'),
            'tts'      => false,
            'embeds'   => [
                // Embed content
                [
                    'type'        => 'rich',
                    'color'       => hexdec('2980B9'),
                    'title'       => 'HUB Transfer Request',
                    'thumbnail'   => [
                        'url' => !empty($transfer->user->avatar) ? $transfer->user->avatar->url : $transfer->user->gravatar(256),
                    ],
                    'description' => (new HtmlConverter(['header_style' => 'atx']))->convert($transfer->body),
                    'timestamp'   => date('c', strtotime($transfer->created_at)),
                    'author'      => [
                        'name' => 'Published By: ' . $transfer->user->name_private,
                        'url'  => route('frontend.profile.show', [$transfer->user->id]),
                    ],
                ],
            ]
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $this->DiscordNotification($wh_url, $json_data);
    }

    public function DiscordNotification($webhook_url, $json_data)
    {
        $ch = curl_init($webhook_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if ($response) {
            Log::debug('SPTransfer | Discord WebHook Msg Response: ' . $response);
        }
        curl_close($ch);
    }

}