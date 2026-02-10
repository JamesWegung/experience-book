<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Overview') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <h3 class="text-2xl font-semibold text-gray-800 mb-6 px-4">
                {{ __('overview.total_list') }}
            </h3>

            <!-- Сетка карточек статистики -->
            @php
                $icons = [
                    'book' => 'M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25',
                    'movie' => 'M6 20.25h12m-12 0a2.25 2.25 0 0 1-2.25-2.25V6.75A2.25 2.25 0 0 1 6 4.5h12a2.25 2.25 0 0 1 2.25 2.25v11.25a2.25 2.25 0 0 1-2.25 2.25m-12 0V6.75m12 13.5V6.75m-6.75-6.75v13.5m-3-13.5v13.5m9-13.5v13.5',
                    'tv-series' => 'M6 20.25h12m-12 0a2.25 2.25 0 0 1-2.25-2.25V6.75A2.25 2.25 0 0 1 6 4.5h12a2.25 2.25 0 0 1 2.25 2.25v11.25a2.25 2.25 0 0 1-2.25 2.25m-12 0V6.75m12 13.5V6.75',
                    'pc-game' => 'M16.5 12h-6m6 0a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM2.25 12a9 9 0 1 1 18 0 9 9 0 0 1-18 0Z',
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 pb-4">
                @foreach($stats as $item)
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-4">
                        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg">
                            <svg xmlns="http://www.w3.org" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$item->slug] ?? 'M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z' }}" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900 leading-none">{{ $item->amount }}</p>
                            <p class="text-sm text-gray-500 font-medium uppercase tracking-tight">
                                {{ trans_choice('overview.' . $item->slug, $item->amount) }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Последние добавленные -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 px-4">
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">
                        {{ __('overview.recent-added') }}
                    </h4>
                    <ul class="divide-y divide-gray-200">
                        @foreach($recentItems as $recent)
                            <li class="py-3 flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $recent->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $recent->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-600">
                                {{ $recent->type_label }}
                            </span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4">
                        <a href="{{ route('events.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">{{ __('overview.show-all') }} →</a>
                    </div>
                </div>

            </div>

        </div>
    </div>



</x-app-layout>
