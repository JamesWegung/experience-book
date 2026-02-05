<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Telegram') }}
        </h2>
    </header>

    <div class="mt-1 mb-4">
        @empty($user->telegram_id)
            <p>{{ __('Not linked') }}</p>
        @else
            <p>{{ __('Linked') }}</p>
        @endempty
    </div>

    <div id="telegram-link-section">
        <button onclick="generateTelegramToken()" class="btn btn-primary shadow">
            Привязать
        </button>

        <div id="token-display" class="hidden mt-3 p-3 bg-gray-100 rounded">
            <p>Отправьте эту команду боту:</p>
            <code class="bg-white p-2 rounded" id="telegram-command"></code>
            <p class="text-sm text-gray-600 mt-2">Токен действителен 30 минут</p>
            <button onclick="copyToken()" class="btn btn-secondary mt-2">Копировать</button>
        </div>
    </div>
</section>
