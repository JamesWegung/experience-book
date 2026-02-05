<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('My experience') }}
                </h2>
                <a
                    href="{{ route('events.create') }}"
                    class="inline-flex items-center p-3 border border-transparent rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                    title="Добавить новое мероприятие"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </a>

                <form action="{{ route('events.importByXlsx') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" class="form-control">
                    <button type="submit" class="btn btn-primary mt-2">Загрузить Excel</button>
                </form>
            </div>

            <div class="relative">
                <div class="flex items-center space-x-2">
                    <div class="relative">
                        <input
                            type="text"
                            id="searchInput"
                            placeholder="Поиск мероприятий..."
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-64 transition-colors duration-200"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <button
                        id="clearSearch"
                        type="button"
                        class="hidden p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200"
                        title="Очистить поиск"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const searchInput = document.getElementById('searchInput');
                    const clearSearch = document.getElementById('clearSearch');

                    const urlParams = new URLSearchParams(window.location.search);
                    const searchQuery = urlParams.get('search');
                    if (searchQuery) {
                        searchInput.value = searchQuery;
                        clearSearch.classList.remove('hidden');
                    }

                    searchInput.addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') {
                            performSearch(e.target.value.trim());
                        }
                    });

                    clearSearch.addEventListener('click', function() {
                        searchInput.value = '';
                        clearSearch.classList.add('hidden');
                        performSearch('');
                    });

                    function performSearch(query) {
                        const url = new URL(window.location.href);

                        if (query) {
                            url.searchParams.set('search', query);
                        } else {
                            url.searchParams.delete('search');
                        }

                        url.searchParams.delete('page');

                        window.location.href = url.toString();
                    }

                    document.addEventListener('keydown', function(e) {
                        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                            e.preventDefault();
                            searchInput.focus();
                        }
                    });
                });
            </script>
        </div>
    </x-slot>

    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center gap-4">

                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-gray-700">Оценка:</span>
                    <a
                        href="{{ request()->fullUrlWithQuery(['sort' => 'rating', 'order' => request('sort') == 'rating' && request('order') == 'desc' ? 'asc' : 'desc']) }}"
                        class="inline-flex items-center px-3 py-2 border rounded-md text-sm font-medium transition-colors duration-200
                            {{ request('sort') == 'rating'
                                ? 'border-blue-500 bg-blue-50 text-blue-700 hover:bg-blue-100 focus:ring-blue-500'
                                : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:ring-blue-500' }}"
                    >
                        <span>{{ request('sort') == 'rating' && request('order') == 'asc' ? 'Сначала низкие' : 'Сначала высокие' }}</span>
                        <svg class="ml-1 h-4 w-4 {{ request('sort') == 'rating' && request('order') == 'asc' ? 'transform rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </a>
                </div>

                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-gray-700">Дата создания:</span>
                    <a
                        href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => request('sort') == 'created_at' && request('order') == 'asc' ? 'desc' : 'asc']) }}"
                        class="inline-flex items-center px-3 py-2 border rounded-md text-sm font-medium transition-colors duration-200
                            {{ request('sort') == 'created_at'
                                ? 'border-blue-500 bg-blue-50 text-blue-700 hover:bg-blue-100 focus:ring-blue-500'
                                : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:ring-blue-500' }}"
                    >
                        <span>{{ request('sort') == 'created_at' && request('order') == 'desc' ? 'Сначала новые' : 'Сначала старые' }}</span>
                        <svg class="ml-1 h-4 w-4 {{ request('sort') == 'created_at' && request('order') == 'desc' ? 'transform rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </a>
                </div>

                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-gray-700">Дата выполнения:</span>
                    <a
                        href="{{ request()->fullUrlWithQuery(['sort' => 'completed_at', 'order' => request('sort') == 'completed_at' && request('order') == 'asc' ? 'desc' : 'asc']) }}"
                        class="inline-flex items-center px-3 py-2 border rounded-md text-sm font-medium transition-colors duration-200
                            {{ request('sort') == 'completed_at'
                                ? 'border-blue-500 bg-blue-50 text-blue-700 hover:bg-blue-100 focus:ring-blue-500'
                                : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:ring-blue-500' }}"
                    >
                        <span>{{ request('sort') == 'completed_at' && request('order') == 'desc' ? 'Сначала новые' : 'Сначала старые' }}</span>
                        <svg class="ml-1 h-4 w-4 {{ request('sort') == 'completed_at' && request('order') == 'desc' ? 'transform rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </a>
                </div>

                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-gray-700">Категория:</span>

                    <button
                        type="button"
                        onclick="openCategoryModal()"
                        class="inline-flex items-center px-3 py-2 border rounded-md text-sm font-medium transition-colors duration-200
            {{ request('categories')
                ? 'border-blue-500 bg-blue-50 text-blue-700 hover:bg-blue-100 focus:ring-blue-500'
                : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:ring-blue-500' }}"
                    >
                        <span>
                            {{ request('categories')
                                ? 'Выбрано: ' . (is_array(request('categories')) ? count(request('categories')) : count(explode(',', request('categories'))))
                                : 'Все категории' }}
                        </span>
                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>

                <x-modal-filter-category :contentTypes="$contentTypes" />

                @if(request()->has('sort') || request()->has('order') || request()->has('categories'))
                    <a href="{{ route('events.index') }}"
                        class="inline-flex items-center px-3 py-2 border rounded-md text-sm font-medium hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                    >
                        Сбросить фильтры
                    </a>
                @endif
            </div>
        </div>
    </div>

    @if(!$events->isEmpty())
        @foreach($events as $event)
            <x-event :event="$event" />
        @endforeach
    @else
        <p>Пусто</p>
    @endif


</x-app-layout>
