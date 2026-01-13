<?php

namespace App\Telegram\Conversations;

use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;

class LinkTelegramAccount extends Conversation
{
    public function start(Nutgram $bot)
    {
        $payload = str_replace('/start ', '', $bot->message()->text);

        if (str_starts_with($payload, 'link_')) {
            $token = str_replace('link_', '', $payload);
            $this->handleToken($bot, $token);
            return;
        }

        $bot->sendMessage('Для привязки аккаунта получите токен на сайте.');
    }

    public function handleToken(Nutgram $bot, string $token)
    {
        try {
            $accessToken = PersonalAccessToken::findToken($token);

            if (!$accessToken || !$accessToken->can('telegram:link')) {
                $bot->sendMessage('Токен просрочен');
                return;
            }

            if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
                $accessToken->delete();
                $bot->sendMessage('Токен просрочен');
                return;
            }

            $user = $accessToken->tokenable;

            $user->update([
                'telegram_id' => $bot->userId(),
                'telegram_username' => $bot->user()->username
            ]);

            $accessToken->delete();

            $bot->sendMessage("Аккаунт успешно привязан! Добро пожаловать, $user->name!");

        } catch (\Exception $e) {
            Log::error("Telegram link error: " . $e->getMessage());
            $bot->sendMessage("Ошибка привязки аккаунта");
        }

        $this->end();
    }
}
