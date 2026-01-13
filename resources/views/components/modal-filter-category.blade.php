<div id="categoryModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Выберите категории</h3>

            <form id="categoryForm" method="GET" action="{{ request()->url() }}">
                <!-- Сохраняем другие GET параметры -->
                @foreach(request()->except('categories', 'page') as $key => $value)
                    @if(is_array($value))
                        @foreach($value as $arrayValue)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $arrayValue }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach

                <div class="max-h-60 overflow-y-auto mb-4">
                    @foreach($contentTypes as $contentType)
                        <div class="flex items-center mb-2">
                            <input
                                type="checkbox"
                                id="category_{{ $contentType->id }}"
                                name="categories[]"
                                value="{{ $contentType->id }}"
                                {{ is_array(request('categories', [])) && in_array($contentType->id, request('categories', [])) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            >
                            <label for="category_{{ $contentType->id }}" class="ml-2 text-sm text-gray-700">
                                {{ $contentType->name }}
                            </label>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-between space-x-2">
                    <button
                        type="button"
                        onclick="selectAllCategories()"
                        class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800"
                    >
                        Выбрать все
                    </button>
                    <button
                        type="button"
                        onclick="clearAllCategories()"
                        class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800"
                    >
                        Очистить
                    </button>
                </div>

                <div class="flex justify-end space-x-2 mt-4">
                    <button
                        type="button"
                        onclick="closeCategoryModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Отмена
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Применить
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openCategoryModal() {
        document.getElementById('categoryModal').classList.remove('hidden');
    }

    function closeCategoryModal() {
        document.getElementById('categoryModal').classList.add('hidden');
    }

    function selectAllCategories() {
        const checkboxes = document.querySelectorAll('input[name="categories[]"]');
        checkboxes.forEach(checkbox => checkbox.checked = true);
    }

    function clearAllCategories() {
        const checkboxes = document.querySelectorAll('input[name="categories[]"]');
        checkboxes.forEach(checkbox => checkbox.checked = false);
    }

    // Закрытие модального окна при клике вне его
    document.getElementById('categoryModal').addEventListener('click', function(e) {
        if (e.target.id === 'categoryModal') {
            closeCategoryModal();
        }
    });
</script>
