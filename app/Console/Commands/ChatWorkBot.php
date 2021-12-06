<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ChatworkSchedule;
use App\Models\Chatwork;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Arr;

class ChatworkSchedules extends Command
{
    protected $signature = 'chatwork-bot:run';

    protected $description = 'Bot send message to room chatwork';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $token = config('chatwork.chatwork_api_key');
        $groupId = config('chatwork.chatwork_room_id');
        $message = $this->_getRandomMaxim();
        $this->_sendMessageToChatwork($token, $groupId, $message);
    }

    protected function _getClientConnect($token = '')
    {
        return new Client([
            'base_uri' => 'https://api.chatwork.com/v2/',
            'headers' => [
                'X-ChatWorkToken' => $token
            ]
        ]); 
    }

    protected function _sendMessageToChatwork($token, $groupId, $message)
    {
        try {
            $this->_getClientConnect($token)->post("rooms/{$groupId}/messages", [
                'form_params' => [
                    'body' => $message
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }

    protected function _getRandomMaxim()
    {
        $title = config('chatwork.maxim.title');
        $content = Arr::get(config('chatwork.maxim.content'), array_rand(config('chatwork.maxim.content')));

        return $title . PHP_EOL . '[info]' . $content . '[/info]';
    }
}
