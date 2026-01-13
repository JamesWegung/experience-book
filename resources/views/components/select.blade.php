<label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">
    {{ $label }}
</label>
<select
    name="{{ $name }}"
    id="{{ $name }}"
    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
    required>

    <option value="">Выберите тип</option>

    @foreach($options as $option)
        <!-- TODO: Проверить работу old(), возможно оно передает не name, а value -->
        <option value="{{ $option->value }}" {{ old('content-type', $selected ?? '') == $option->name ? 'selected' : '' }}>{{ $option->name }}</option>
    @endforeach

</select>
@error('type')
<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
@enderror
