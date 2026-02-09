{{-- Сообщение об успешном завершении --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>{{ __('Success') }}!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Предупреждение, если часть данных не загрузилась --}}
@if(session('warning'))
    <div class="alert alert-warning" role="alert">
        {{ session('warning') }}
    </div>
@endif

{{-- Детальный список ошибок из файла --}}
@if(session()->has('import_failures'))
    <div class="alert alert-danger shadow-sm">
        <h5 class="alert-heading">Ошибки в строках:</h5>
        <hr>
        <div style="max-height: 200px; overflow-y: auto;">
            <ul class="mb-0">
                @foreach(session()->get('import_failures') as $failure)
                    <li>
                        <strong>Строка {{ $failure->row() }}:</strong>
                        @foreach($failure->errors() as $error)
                            <span>{{ $error }}</span>
                        @endforeach
                        <small class="text-muted">(Данные: {{ implode(', ', $failure->values()) }})</small>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div x-data="{ open: false }" class="flex items-center gap-3">
    <!-- Кнопка-триггер -->
    <button @click="open = !open"
            class="flex items-center gap-2 px-4 py-3 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-all focus:outline-none focus:ring-2 focus:ring-gray-400">
        <svg xmlns="http://www.w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-300">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
        </svg>
        <span>{{ __('Import') }}</span>
    </button>

    <!-- Выезжающий блок -->
    <div x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-x-4"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-cloak
         class="flex items-center gap-2 bg-white p-1 border border-gray-200 rounded-xl shadow-sm">

        <form action="{{ route('events.importByXlsx') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-1">
            @csrf
            <input type="file" name="file" required
                   class="block w-60 text-xs text-gray-500
                file:mr-3 file:py-3 file:px-3
                file:rounded-md file:border-0
                file:text-xs file:font-semibold
                file:bg-blue-50 file:text-blue-700
                hover:file:bg-blue-100 cursor-pointer">

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 text-xs font-medium rounded-md transition-colors whitespace-nowrap">
                {{ __('Load') }}
            </button>
        </form>

        <div class="h-6 w-px bg-gray-200 mx-1"></div>

        <!-- Кнопка шаблона -->
        <a href="{{ route('events.template') }}"
           class="text-xs text-gray-500 hover:text-blue-600 px-2 py-1.5 flex items-center gap-1.5 transition-colors"
           title="Скачать образец .xlsx">
            <svg xmlns="http://www.w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            <span class="whitespace-nowrap">{{ __('Template') }}</span>
        </a>
    </div>
</div>



