<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.to-link-telegram')
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
                const response = await fetch('/api/telegram/generate-token', {
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
