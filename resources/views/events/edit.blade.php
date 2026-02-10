<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if(isset($event))
                Редактирование мероприятия
            @else
                Создание мероприятия
            @endif
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ isset($event) ? route('events.update', $event) : route('events.store') }}" method="POST">
                @csrf
                @if(isset($event))
                    @method('PUT')
                @endif

                    <div class="space-y-6">
                        <div class="border border-gray-400 p-3 rounded-xl">
                            <div>
                                <x-select :options="$contentTypeOptions">
                                    <x-slot name="label">Тип контента</x-slot>
                                    <x-slot name="name">content_type_id</x-slot>
                                    <x-slot name="selected">{{ $event->Content->ContentType->name ?? '' }}</x-slot>
                                </x-select>
                            </div>

                            <div class="relative">
                                <label for="content_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Название контента *
                                </label>
                                <input
                                    type="text"
                                    name="content_name"
                                    id="content_name"
                                    value="{{ old('content-name', $event->Content->name ?? '') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Введите название мероприятия"
                                    required
                                    autocomplete="off"
                                >
                                <div id="autocomplete-results" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto"></div>
                                @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <label for="rating" class="block text-sm font-medium text-gray-700 mb-2">
                            Оценка (1-10) *
                        </label>
                        <select
                            name="rating"
                            id="rating"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            required
                        >
                            <option value="">Выберите оценку</option>
                            @for($i = 10; $i >= 1; $i--)
                                <option value="{{ $i }}" {{ old('rating', $event->rating ?? '') == $i ? 'selected' : '' }}>
                                    {{ $i }} {{ $i == 10 ? '★' : '' }}
                                </option>
                            @endfor
                        </select>
                        @error('rating')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                    <div>
                        <label for="review" class="block text-sm font-medium text-gray-700 mb-2">
                            Отзыв
                        </label>
                        <textarea
                            name="review"
                            id="review"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Поделитесь своими впечатлениями..."
                        >{{ old('review', $event->review ?? '') }}</textarea>
                        @error('review')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="completed_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Дата выполнения
                        </label>
                        <input
                            type="date"
                            name="completed_date"
                            id="completed_date"
                            value="{{ old('event_date', isset($event->completed_at) ? $event->completed_at->format('Y-m-d') : date('Y-m-d')) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        >
                        @error('event_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a
                            href="{{ route('events.index') }}"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                        >
                            ← Назад к списку
                        </a>
                        <button
                            type="submit"
                            class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                        >
                            @if(isset($event))
                                Сохранить изменения
                            @else
                                Создать мероприятие
                            @endif
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <x-slot name="scripts">
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const contentInput = document.getElementById('content_name');
                const resultsContainer = document.getElementById('autocomplete-results');
                let timeoutId;

                contentInput.addEventListener('input', function(e) {
                    const query = e.target.value.trim();
                    const content_type_id = document.getElementById('content_type_id').value;

                    clearTimeout(timeoutId);

                    if (query.length < 2) {
                        hideResults();
                        return;
                    }

                    timeoutId = setTimeout(() => {
                        fetchSuggestions(query, content_type_id);
                    }, 300);
                });

                document.addEventListener('click', function(e) {
                    if (!contentInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                        hideResults();
                    }
                });

                contentInput.addEventListener('keydown', function(e) {
                    const items = resultsContainer.querySelectorAll('.autocomplete-item');
                    const activeItem = resultsContainer.querySelector('.autocomplete-item.active');

                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        navigateResults(1, items, activeItem);
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        navigateResults(-1, items, activeItem);
                    } else if (e.key === 'Enter' && activeItem) {
                        e.preventDefault();
                        selectSuggestion(activeItem);
                    } else if (e.key === 'Escape') {
                        hideResults();
                    }
                });

                function fetchSuggestions(query, content_type_id) {
                    fetch(`/experience-book/public/api/events/autocomplete?query=${encodeURIComponent(query)}&content_type_id=${encodeURIComponent(content_type_id)}`)
                        .then(response => response.json())
                        .then(suggestions => {
                            displayResults(suggestions);
                        })
                        .catch(error => {
                            console.error('Error fetching suggestions:', error);
                        });
                }

                function displayResults(suggestions) {
                    if (suggestions.length === 0) {
                        hideResults();
                        return;
                    }

                    resultsContainer.innerHTML = '';
                    suggestions.forEach((suggestion, index) => {
                        const item = document.createElement('div');
                        item.className = `autocomplete-item px-3 py-2 cursor-pointer hover:bg-blue-50 ${index === 0 ? 'active' : ''}`;
                        item.textContent = suggestion;
                        item.setAttribute('data-value', suggestion);

                        item.addEventListener('click', function() {
                            selectSuggestion(this);
                        });

                        item.addEventListener('mouseenter', function() {
                            resultsContainer.querySelectorAll('.autocomplete-item').forEach(el => {
                                el.classList.remove('active');
                            });

                            this.classList.add('active');
                        });

                        resultsContainer.appendChild(item);
                    });

                    resultsContainer.classList.remove('hidden');
                }

                function hideResults() {
                    resultsContainer.classList.add('hidden');
                    resultsContainer.innerHTML = '';
                }

                function navigateResults(direction, items, activeItem) {
                    if (items.length === 0) return;

                    let nextIndex = 0;

                    if (activeItem) {
                        const currentIndex = Array.from(items).indexOf(activeItem);
                        nextIndex = currentIndex + direction;

                        if (nextIndex < 0) nextIndex = items.length - 1;
                        if (nextIndex >= items.length) nextIndex = 0;

                        activeItem.classList.remove('active');
                    }

                    items[nextIndex].classList.add('active');
                    items[nextIndex].scrollIntoView({ block: 'nearest' });
                }

                function selectSuggestion(item) {
                    contentInput.value = item.getAttribute('data-value');
                    hideResults();
                    console.log('Selected:', contentInput.value);
                }
            });
        </script>
    </x-slot>

    <x-slot name="style">
        <style>
            .autocomplete-item.active {
                background-color: #3b82f6;
                color: white;
            }

            .autocomplete-item {
                transition: background-color 0.15s ease;
            }

            #autocomplete-results {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }
        </style>
    </x-slot>
</x-app-layout>


