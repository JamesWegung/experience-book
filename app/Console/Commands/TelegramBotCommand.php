<?php

namespace App\Console\Commands;

use App\Bot\TelegramBot;
use Illuminate\Console\Command;

class TelegramBotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegramBot:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Telegram Bot';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bot = new TelegramBot();

        $bot->run();
    }
}
