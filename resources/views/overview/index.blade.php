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
                    'movie' => 'M7 4h10l2 4h-2l-2-4h-2l2 4h-2l-2-4h-2l2 4H5L7 4zM3 8h18v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8z',
                    'tv-series' => 'M6 20.25h12m-12 0a2.25 2.25 0 0 1-2.25-2.25V6.75A2.25 2.25 0 0 1 6 4.5h12a2.25 2.25 0 0 1 2.25 2.25v11.25a2.25 2.25 0 0 1-2.25 2.25m-12 0V6.75m12 13.5V6.75',
                    'pc-game' => 'M15.5 8.5h.01m2.49 3h.01M12 12h.01M8 12H3.75a1.5 1.5 0 0 1-1.5-1.5v-3a1.5 1.5 0 0 1 1.5-1.5h16.5a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5H16M9.5 15.5v3a1.5 1.5 0 0 1-1.5 1.5h-1a1.5 1.5 0 0 1-1.5-1.5v-3m9 0v3a1.5 1.5 0 0 0 1.5 1.5h1a1.5 1.5 0 0 0 1.5-1.5v-3',
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
            {{--<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 px-4">
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
            </div>--}}

        </div>
    </div>



</x-app-layout>
