<?php

namespace App\Telegram\Conversations;

use App\Bot\TelegramBot;
use App\Models\Content;
use App\Models\ContentType;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Conversations\InlineMenu;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

class AddNewEvent extends InlineMenu
{
    /**
     * @throws InvalidArgumentException
     */
    public function start(Nutgram $bot): void
    {
        $contentTypes = ContentType::take(10)->get();

        $this->menuText('Выберите категорию:');

        foreach ($contentTypes as $contentType) {
            $this->addButtonRow(InlineKeyboardButton::make($contentType->name, callback_data: "$contentType->id,$contentType->name@handleContentType"));
        }

        $this->orNext('none')
            ->showMenu();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function handleContentType(Nutgram $bot): void
    {
        if (!$bot->isCallbackQuery()) {
            $this->start($bot);
            return;
        }

        $this->closeMenu();

        [$contentTypeId, $contentTypeName] = explode(',', $bot->callbackQuery()->data);
        $bot->setUserData('contentTypeId', $contentTypeId);
        $bot->setUserData('contentTypeName', $contentTypeName);

        $bot->sendMessage("[$contentTypeName] Введите название:");
        $this->next('getContentName');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getContentName(Nutgram $bot): void
    {
        try {
            $contentTypeName = $bot->getUserData('contentTypeName');
        } catch (InvalidArgumentException $e) {
            $bot->sendMessage('Упс! Что-то не нашлось.');
            Log::error($e->getMessage());
            return;
        }

        $contentName = $bot->message()->text;
        $bot->setUserData('contentName', $contentName);

        $bot->sendMessage('Поставьте оценку', reply_markup: TelegramBot::getRatingsKeyboard());
        $this->next('getRating');
    }

    public function getRating(Nutgram $bot): void
    {
        $rating = intval($bot->message()->text);
        $bot->setUserData('rating', $rating);

        try {
            $contentTypeName = $bot->getUserData('contentTypeName');
            $contentName = $bot->getUserData('contentName');
        } catch (\Exception $e) {
            $bot->sendMessage('Упс! Что-то не нашлось.');
            Log::error($e->getMessage());
            return;
        }

        $this->menuText("Будет сохранено: $contentName ($contentTypeName) с оценкой $rating. Добавить отзыв?")
            ->clearButtons()
            ->addButtonRow(
                InlineKeyboardButton::make("Сохранить", callback_data: '@handleSave'),
                InlineKeyboardButton::make("Добавить отзыв", callback_data: '@handleAddReview')
            )
            ->orNext('none')
            ->showMenu();
    }

    public function handleSave(Nutgram $bot): void
    {
        $this->closeMenu();
        try {
            $contentTypeId = $bot->getUserData('contentTypeId');
            $contentName = $bot->getUserData('contentName');
            $rating = $bot->getUserData('rating');
            $review = $bot->getUserData('review');
            $user = User::where('telegram_id', $bot->userId())->first();
        } catch (\Exception $e) {
            $bot->sendMessage('Упс! Что-то не нашлось.');
            Log::error($e->getMessage());
            return;
        }

        $contentData = [
            'name' => $contentName,
            'content_type_id' => $contentTypeId,
        ];

        $eventData = [
            'user_id' => $user->id,
            'rating' => $rating,
            'review' => $review,
            'completed_at' => now()->timestamp,
        ];


        try {
            DB::transaction(function () use ($eventData, $contentData) {
                $content = Content::where('name', $contentData['name'])->first();

                if (!$content) {
                    $content = Content::create($contentData);
                }

                $eventData['content_id'] = $content->id;

                Event::create($eventData);
            });

//            $removeKeyboard = ReplyKeyboardRemove::make(remove_keyboard: true);
            $bot->sendMessage("Успешно добавлено!", reply_markup: TelegramBot::getMainMenuKeyboard());
            $this->end();
        } catch (\Exception $e) {
            $bot->sendMessage('Упс! Не записалось.');
            Log::error($e->getMessage());
            return;
        }


    }

    /**
     * @throws InvalidArgumentException
     */
    public function handleAddReview(Nutgram $bot): void
    {
        $this->closeMenu();
        $bot->sendMessage('Введите текст отзыва:');
        $this->next('saveReview');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function saveReview(Nutgram $bot): void
    {
        $review = $bot->message()->text;
        $bot->setUserData('review', $review);
        $this->handleSave($bot);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function none(Nutgram $bot): void
    {
        $bot->sendMessage('Bye!');
        $this->end();
    }
}
