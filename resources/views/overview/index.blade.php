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

    <div class="m-5">
        <p>{{ __('overview.total_list') }}</p>

        <ul>
            @foreach($stats as $item)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="badge bg-primary rounded-pill">
                        {{ $item->amount }}
                    </span>
                    {{ trans_choice('overview.' . $item->slug, $item->amount) }}
                </li>
            @endforeach
        </ul>
    </div>


</x-app-layout>
