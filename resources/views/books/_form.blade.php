@php
    $bookGenreIds = isset($book) ? $book->genres->pluck('id')->toArray() : [];
@endphp

@csrf
<div class="space-y-4">
    <div>
        <label for="title" class="block font-medium text-sm text-gray-700">タイトル</label>
        <input type="text" name="title" id="title" value="{{ old('title', $book->title ?? '') }}" required
            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
        @error('title')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="author" class="block font-medium text-sm text-gray-700">著者</label>
        <input type="text" name="author" id="author" value="{{ old('author', $book->author ?? '') }}" required
            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
        @error('author')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="isbn" class="block font-medium text-sm text-gray-700">ISBN-13</label>
        <input type="text" name="isbn" id="isbn" value="{{ old('isbn', $book->isbn ?? '') }}" required
            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
        @error('isbn')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="published_date" class="block font-medium text-sm text-gray-700">出版日</label>
        <input type="date" name="published_date" id="published_date" value="{{ old('published_date', $book->published_date ?? '') }}" required
            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
        @error('published_date')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="description" class="block font-medium text-sm text-gray-700">概要</label>
        <textarea name="description" id="description" rows="4"
            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">{{ old('description', $book->description ?? '') }}</textarea>
        @error('description')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="image_url" class="block font-medium text-sm text-gray-700">画像URL</label>
        <input type="text" name="image_url" id="image_url" value="{{ old('image_url', $book->image_url ?? '') }}"
            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
        @error('image_url')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block font-medium text-sm text-gray-700 mb-2">ジャンル</label>
        <div class="flex flex-wrap gap-4">
            @foreach($genres as $genre)
                <label class="inline-flex items-center">
                    <input type="checkbox" name="genres[]" value="{{ $genre->id }}"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        @if(in_array($genre->id, old('genres', $bookGenreIds))) checked @endif>
                    <span class="ml-2 text-sm text-gray-600">{{ $genre->name }}</span>
                </label>
            @endforeach
        </div>
        @error('genres')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
</div>