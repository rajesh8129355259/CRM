<div class="sm:col-span-1">
    <label for="category_id" class="block text-sm font-medium text-gray-700">{{ __('Category') }}</label>
    <select id="category_id" name="category_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        <option value="">{{ __('Select Category') }}</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ old('category_id', $lead->category_id ?? '') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div> 