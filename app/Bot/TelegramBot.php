<?php

namespace App\Bot;

use App\Models\User;
use App\Telegram\Conversations\AddNewEvent;
use App\Telegram\Conversations\LinkTelegramAccount;
use App\Telegram\Conversations\FindEvents;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;


class TelegramBot
{
    private Nutgram $bot;
    private int $allowedUserId;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    public function __construct()
    {
        if (empty(env('TELEGRAM_TEST_USER'))) {
            throw new \Exception('Не задан id тестового пользователя');
        }


        $this->bot = new Nutgram(env('TELEGRAM_TOKEN'));
        $this->allowedUserId = env('TELEGRAM_TEST_USER');

        $this->registerHandlers();
    }

    private function registerHandlers(): void
    {
        $this->bot->middleware(function (Nutgram $bot, $next) {
            $userId = $bot->userId();

            if ($userId !== $this->allowedUserId) {
                $bot->sendMessage('...');
                return;
            }

            $next($bot);
        });

        $this->bot->onText('/start.*', function (Nutgram $bot) {
            $user = User::where('telegram_id', $bot->userId())->first();

            if (!$user) {
                LinkTelegramAccount::begin($bot);
                return;
            }

            $bot->sendMessage("Привет, $user->name!");
            $bot->sendMessage('Выберите действие', reply_markup: self::getMainMenuKeyboard());
        });

        $this->bot->onText('Добавить мероприятие', AddNewEvent::class);
        $this->bot->onText('Найти', FindEvents::class);
    }

    public function addEvent(Nutgram $bot)
    {
        $bot->sendMessage('Вы выбрали кнопку 1');
    }

    public static function getMainMenuKeyboard(): ReplyKeyboardMarkup
    {
        return ReplyKeyboardMarkup::make(
            resize_keyboard: true,
            one_time_keyboard: true
        )
            ->addRow(
                KeyboardButton::make('Добавить мероприятие'),
                KeyboardButton::make('Найти')
            );
    }

    public static function getRatingsKeyboard(): ReplyKeyboardMarkup
    {
        $keyboard = ReplyKeyboardMarkup::make(
            resize_keyboard: true,
            one_time_keyboard: true
        );

        for ($i = 10; $i >= 0; $i--) {
            $keyboard->addRow(KeyboardButton::make($i));
        }

        return $keyboard;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function run(): void
    {
        $this->bot->run();
    }

}
