<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
    <div class="flex justify-between items-start">
        <div class="flex-1">
            <div class="flex items-center space-x-3 mb-4">
                <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1.5 rounded">
                    {{ $event->completed_at ? $event->completed_at->format('d-m-Y') : 'Запланировано' }}
                </span>
                <a href="?categories[]={{ $event->content->contentType->id }}" class="text-sm text-green-800 bg-green-100 px-3 py-1.5 rounded border border-green-200">
                    {{ $event->content->contentType->name }}
                </a>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-3">
                {{ $event->content->name }}
            </h3>
            <div class="space-y-3">
                <div class="flex items-center space-x-1">
                    <span class="text-yellow-500">★</span>
                    <span class="text-gray-700 font-medium">Оценка: {{ $event->rating }}</span>
                </div>
                @if(isset($event->review))
                    <div class="text-gray-700">
                        <span class="font-medium block mb-1">Отзыв:</span>
                        <p class="text-gray-600 bg-gray-50 rounded-lg px-3 py-2 border border-gray-200">
                            {{ $event->review }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <!-- Ссылка редактирования -->
            <a
                href="{{ route('events.edit', ['event' => $event]) }}"
                class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                title="Редактировать"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>

            <!-- Кнопка удаления -->
            <form action="{{ route('events.destroy', ['event' => $event]) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors duration-200"
                    title="Удалить"
                    onclick="return confirm('Вы уверены, что хотите удалить эту запись?')"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
