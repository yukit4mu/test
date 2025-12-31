<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('書籍の登録') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- ISBN自動取得セクション -->
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-800 mb-3">ISBNから書籍情報を自動取得</h3>
                        <p class="text-sm text-blue-600 mb-3">ISBNを入力して「取得」ボタンを押すと、Google Books APIから書籍情報を自動入力します。</p>
                        <div class="flex gap-2">
                            <input type="text" id="isbn-search" placeholder="ISBN（13桁）を入力" 
                                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <button type="button" id="fetch-book-btn" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                取得
                            </button>
                        </div>
                        <p id="fetch-status" class="mt-2 text-sm hidden"></p>
                    </div>

                    <form action="{{ route('books.store') }}" method="POST" id="book-form">
                        @include('books._form')
                        
                        <div class="flex items-center justify-end mt-6 pt-6 border-t border-gray-200">
                            <a href="{{ route('books.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                                キャンセル
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                                登録する
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('fetch-book-btn').addEventListener('click', async function() {
            const isbn = document.getElementById('isbn-search').value.trim();
            const statusEl = document.getElementById('fetch-status');
            
            if (!isbn) {
                statusEl.textContent = 'ISBNを入力してください。';
                statusEl.className = 'mt-2 text-sm text-red-600';
                statusEl.classList.remove('hidden');
                return;
            }

            statusEl.textContent = '取得中...';
            statusEl.className = 'mt-2 text-sm text-blue-600';
            statusEl.classList.remove('hidden');

            try {
                const response = await fetch(`{{ route('books.fetch') }}?isbn=${isbn}`);
                const data = await response.json();

                if (response.ok) {
                    // フォームに値を設定
                    document.querySelector('input[name="title"]').value = data.title || '';
                    document.querySelector('input[name="author"]').value = data.author || '';
                    document.querySelector('input[name="isbn"]').value = isbn;
                    document.querySelector('input[name="published_date"]').value = data.published_date || '';
                    document.querySelector('textarea[name="description"]').value = data.description || '';
                    document.querySelector('input[name="image_url"]').value = data.image_url || '';

                    statusEl.textContent = '書籍情報を取得しました。';
                    statusEl.className = 'mt-2 text-sm text-green-600';
                } else {
                    statusEl.textContent = data.error || '書籍が見つかりませんでした。';
                    statusEl.className = 'mt-2 text-sm text-red-600';
                }
            } catch (error) {
                statusEl.textContent = '取得に失敗しました。';
                statusEl.className = 'mt-2 text-sm text-red-600';
            }
        });
    </script>
    @endpush
</x-app-layout>
