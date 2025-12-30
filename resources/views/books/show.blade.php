<x-app-layout>
    <x-slot name="header">{{ $book->title }}</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="md:w-1/3">
                            @if($book->image_url)
                                <img src="{{ $book->image_url }}" alt="{{ $book->title }}" class="w-full rounded shadow">
                            @else
                                <div class="w-full h-64 bg-gray-200 flex items-center justify-center rounded">
                                    <span class="text-gray-500">画像なし</span>
                                </div>
                            @endif
                        </div>
                        <div class="md:w-2/3">
                            <h1 class="text-2xl font-bold mb-4">{{ $book->title }}</h1>
                            <p class="text-gray-600 mb-2"><strong>著者:</strong> {{ $book->author }}</p>
                            <p class="text-gray-600 mb-2"><strong>ISBN:</strong> {{ $book->isbn }}</p>
                            <p class="text-gray-600 mb-2"><strong>出版日:</strong> {{ $book->published_date }}</p>
                            <div class="mb-4">
                                <strong>ジャンル:</strong>
                                @foreach($book->genres as $genre)
                                    <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded">{{ $genre->name }}</span>
                                @endforeach
                            </div>
                            @if($book->description)
                                <div class="mb-4">
                                    <strong>概要:</strong>
                                    <p class="mt-2 text-gray-700">{{ $book->description }}</p>
                                </div>
                            @endif

                            @can('update', $book)
                                <div class="flex gap-2 mt-4">
                                    <a href="{{ route('books.edit', $book) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                        編集
                                    </a>
                                    <form action="{{ route('books.destroy', $book) }}" method="POST" onsubmit="return confirm('本当に削除しますか？')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                            削除
                                        </button>
                                    </form>
                                </div>
                            @endcan
                        </div>
                    </div>

                    {{-- レビューセクション（Step 6で実装） --}}
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('books.index') }}" class="text-blue-600 hover:underline">← 一覧に戻る</a>
            </div>
        </div>
    </div>
</x-app-layout>