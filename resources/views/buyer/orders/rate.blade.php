<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Rate Product</h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto">
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-semibold mb-4">You're rating: {{ $order->product->name }}</h3>

            <form action="{{ route('buyer.orders.rate.store', $order->id) }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block font-bold text-gray-700 mb-2">Rating (1 to 5):</label>
                    <input type="number" name="rating" min="1" max="5"
                           class="w-full border rounded px-3 py-2 text-black" required>
                </div>

                <div class="mb-4">
                    <label class="block font-bold text-gray-700 mb-2">Comment (optional):</label>
                    <textarea name="comment" rows="4" class="w-full border rounded px-3 py-2 text-black"
                              placeholder="Share your feedback..."></textarea>
                </div>

                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                    Submit Rating
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
