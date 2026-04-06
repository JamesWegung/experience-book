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

                                <div id="dynamic-attributes" class="mt-4 space-y-4">
                                    @if(isset($event) && $event->content->attributeValues)
                                        @foreach($event->content->attributeValues as $val)
                                            <div class="mb-4">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $val->attribute->name }}</label>
                                                <input type="text"
                                                       name="custom_attributes[{{ $val->attribute_id }}]"
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                                       value="{{ $val->value }}"
                                                       placeholder="Введите значение"
                                                       autocomplete="off">
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="relative">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Название контента *
                                </label>
                                <input
                                    type="text"
                                    name="name"
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
                        <label for="completed_at" class="block text-sm font-medium text-gray-700 mb-2">
                            Дата выполнения
                        </label>
                        <input
                            type="date"
                            name="completed_at"
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


    @push('scripts')
        @vite('resources/js/events/form.js')
    @endpush
</x-app-layout>


