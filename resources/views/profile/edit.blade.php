<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <!-- TODO: подключать через include(), если телега уже привязана, то отображать это -->
        <div id="telegram-link-section">
            <button onclick="generateTelegramToken()" class="btn btn-primary shadow">
                Привязать Telegram аккаунт
            </button>

            <div id="token-display" class="hidden mt-3 p-3 bg-gray-100 rounded">
                <p>Отправьте эту команду боту:</p>
                <code class="bg-white p-2 rounded" id="telegram-command"></code>
                <p class="text-sm text-gray-600 mt-2">Токен действителен 30 минут</p>
                <button onclick="copyToken()" class="btn btn-secondary mt-2">Копировать</button>
            </div>
        </div>


        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>



    <script>
        async function generateTelegramToken() {
            try {
                const response = await fetch('/experience-book/public/api/telegram/generate-token', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Authorization': 'Bearer {{ auth()->user()->createToken('web')->plainTextToken }}',
                        'Content-Type': 'application/json',
                    },
                });

                const data = await response.json();

                if (response.ok) {
                    document.getElementById('telegram-command').textContent = `/start link_${data.token}`;
                    document.getElementById('token-display').classList.remove('hidden');
                } else {
                    alert('Ошибка генерации токена');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function copyToken() {
            const command = document.getElementById('telegram-command').textContent;
            navigator.clipboard.writeText(command);
        }
    </script>
</x-app-layout>
