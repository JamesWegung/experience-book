<?php

namespace App\Telegram\Conversations;

use App\Bot\TelegramBot;
use App\Models\ContentType;
use App\Models\Event;
use Illuminate\Support\Facades\Log;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Conversations\InlineMenu;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use function PHPUnit\Framework\matches;

class FindEvents extends InlineMenu
{
    public function start(Nutgram $bot)
    {
        $contentTypes = ContentType::take(10)->get();

        $this->menuText('Выберите категорию для поиска:');

        foreach ($contentTypes as $contentType) {
            $this->addButtonRow(InlineKeyboardButton::make($contentType->name, callback_data: "$contentType->id,$contentType->name@handleContentType"));
        }

        $this->orNext('none')
            ->showMenu();
    }

    public function handleContentType(Nutgram $bot)
    {
        if (!$bot->isCallbackQuery()) {
            $this->start($bot);
            return;
        }

        [$contentTypeId, $contentTypeName] = explode(',', $bot->callbackQuery()->data);
        $bot->setUserData('contentTypeId', $contentTypeId);
        $bot->setUserData('contentTypeName', $contentTypeName);

        $this->menuText('Выберите условие для поиска:')
            ->clearButtons()
            ->addButtonRow(InlineKeyboardButton::make('Больше или равно...', callback_data: "bigger@handleSearchComparisonType"))
            ->addButtonRow(InlineKeyboardButton::make('Меньше или равно...', callback_data: "smaller@handleSearchComparisonType"))
            ->addButtonRow(InlineKeyboardButton::make('Равно...', callback_data: "exact@handleSearchComparisonType"))
            ->orNext('none')
            ->showMenu();
    }

    public function handleSearchComparisonType(Nutgram $bot)
    {
        if (!$bot->isCallbackQuery()) {
            $this->start($bot);
            return;
        }

        $this->closeMenu();

        $bot->setUserData('searchComparisonType', $bot->callbackQuery()->data);

        $bot->sendMessage('Введите оценку', reply_markup: TelegramBot::getRatingsKeyboard());
        $this->next('findByRating');
    }

    public function findByRating(Nutgram $bot)
    {
        try {
            $contentTypeId = $bot->getUserData('contentTypeId');
            $contentTypeName = $bot->getUserData('contentTypeName');
            $searchComparisonType = $bot->getUserData('searchComparisonType');
        } catch (InvalidArgumentException $e) {
            $bot->sendMessage('Упс! Что-то не нашлось.');
            Log::error($e->getMessage());
            return;
        }

        $rating = intval($bot->message()->text);
        $comparisonSign = match ($searchComparisonType) {
            'bigger' => '>=',
            'smaller' => '<=',
            default => '='
        };

        $bot->sendMessage("Поиск в категории $contentTypeName, где оценка $comparisonSign $rating");
        $events = Event::join('contents', 'contents.id', '=', 'events.content_id')
            ->where('contents.content_type_id', $contentTypeId)
            ->where('events.rating', $comparisonSign, $rating)->limit(5)->get();

        $result = '';

        foreach ($events as $event) {
            $result .= "$event->name (Оценка: $event->rating)";
        }

        $bot->sendMessage($result);
    }

    public function secondStep(Nutgram $bot)
    {
        $bot->sendMessage('Bye!');
        $this->end();
    }
}
