@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-success">Your Cart</h2>

    <div id="toast-area" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($cartItems->isEmpty())
        <p class="text-muted">Your cart is empty.</p>
    @else
        <form id="cartForm" action="{{ route('buyer.cart.checkout') }}" method="POST">
            @csrf
            <div class="cart-table-wrapper table-responsive">
                <table class="table align-middle cart-table">
                    <thead class="table-light text-center">
                        <tr>
                            <th>
                                <input type="checkbox" id="select-all">
                                <label for="select-all" class="ms-1 small">All</label>
                            </th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cartItems as $item)
                            @php $product = $item->product; @endphp
                            <tr id="cart-row-{{ $item->id }}">
                                <td>
                                    @if ($product)
                                        <input type="checkbox" name="cart_ids[]" class="form-check-input cart-checkbox" value="{{ $item->id }}">
                                    @endif
                                </td>
                                <td class="d-flex align-items-center gap-2 text-start">
                                    @if ($product)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="rounded" alt="{{ $product->name }}" style="width: 60px; height: 60px; object-fit: cover;">
                                        <div>
                                            <div class="fw-semibold text-uppercase">{{ $product->name }}</div>
                                            <div class="text-muted small">Stock: {{ $product->stock }}</div>
                                        </div>
                                    @else
                                        <s class="text-muted">Unavailable</s>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($product)
                                        ₱{{ number_format($product->price, 2) }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($product)
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            <button type="button" class="btn btn-outline-secondary btn-sm qty-btn" data-action="decrease" data-id="{{ $item->id }}">−</button>
                                            <input type="number" name="quantities[{{ $item->id }}]"
                                            class="form-control form-control-sm text-center quantity-input"
                                            min="1"
                                            max="{{ $product->stock }}"
                                            value="{{ $item->quantity }}"
                                            data-price="{{ $product->price }}"
                                            data-row="{{ $item->id }}"
                                            style="width: 60px;"
                                            {{ old('cart_ids') && !in_array($item->id, old('cart_ids', [])) ? 'disabled' : '' }}>
                                            <button type="button" class="btn btn-outline-secondary btn-sm qty-btn" data-action="increase" data-id="{{ $item->id }}">+</button>
                                        </div>
                                        <small class="text-muted d-block mt-1">Max: {{ $product->stock }}</small>
                                    @endif
                                </td>
                                <td class="text-center product-total" id="total-{{ $item->id }}">
                                    @if ($product)
                                        ₱{{ number_format($product->price * $item->quantity, 2) }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove" data-id="{{ $item->id }}">
                                        <i class="bi bi-trash"></i> Remove
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        <div class="cart-summary">
            <h5>Subtotal: <span id="subtotal-display">₱0.00</span></h5>
            <button type="submit" class="btn btn-primary" id="checkoutBtn" disabled>Checkout Selected</button>
        </div>

        </form>
    @endif
</div>
@endsection


@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    function showToast(message, type = 'danger') {
        const toastId = 'toast-' + Date.now();
        const toast = `
            <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0 show mb-2" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`;
        $('#toast-area').append(toast);
        setTimeout(() => $(`#${toastId}`).fadeOut(400, function () { $(this).remove(); }), 4000);
    }

    $('.btn-remove').click(function () {
        const cartId = $(this).data('id');

        $.ajax({
            url: `/buyer/cart/${cartId}`,
            type: 'DELETE',
            success: function () {
                $(`#cart-row-${cartId}`).remove();
                showToast("Item removed successfully", 'success');
                updateCheckoutButton();
                updateSubtotal();
            },
            error: function (xhr) {
                let message = 'Could not remove item.';
                if (xhr.responseJSON?.error) message += ' ' + xhr.responseJSON.error;
                showToast(message);
            }
        });
    });

    $('#select-all').on('change', function () {
        $('.cart-checkbox').prop('checked', this.checked).trigger('change');
    });

    $('.cart-checkbox').on('change', function () {
        const rowId = $(this).val();
        const isChecked = $(this).is(':checked');
        const input = $(`input[name="quantities[${rowId}]"]`);
        input.prop('disabled', !isChecked);
        updateCheckoutButton();
        updateSubtotal();
    });

    $('.quantity-input').on('input', function () {
        const input = $(this);
        const cartId = input.data('row');
        const qty = parseInt(input.val()) || 1;
        const max = parseInt(input.attr('max'));

        if (qty >= max) {
            input.addClass('border-danger');
            showToast('Exceeds available stock. Adjusted to max.', 'warning');
            input.val(max);
        } else {
            input.removeClass('border-danger');
        }

        $.post("{{ route('buyer.cart.updateQuantity') }}", {
            cart_id: cartId,
            quantity: input.val()
        })
        .done(function (response) {
            $('#total-' + cartId).text('₱' + response.new_total);
            updateSubtotal();
        })
        .fail(function (xhr) {
            const error = xhr.responseJSON?.error || 'Failed to update.';
            showToast(error, 'danger');
        });
    });

    // ✅ FIX: Bind + / - click functionality
    $('.qty-btn').on('click', function () {
        const action = $(this).data('action');
        const rowId = $(this).data('id');

        // ✅ Only allow changing quantity if checkbox is checked
        const isChecked = $(`.cart-checkbox[value="${rowId}"]`).is(':checked');
        if (!isChecked) return;

        const input = $(`input[name="quantities[${rowId}]"]`);
        let value = parseInt(input.val()) || 1;
        const max = parseInt(input.attr('max')) || 999;

        if (action === 'increase' && value < max) value++;
        if (action === 'decrease' && value > 1) value--;

        input.val(value).trigger('input');
    });

    $('.cart-checkbox').each(function () {
        const rowId = $(this).val();
        const isChecked = $(this).is(':checked');
        $(`input[name="quantities[${rowId}]"]`).prop('disabled', !isChecked);
    });

    function updateCheckoutButton() {
        const hasChecked = $('.cart-checkbox:checked').length > 0;
        $('#checkoutBtn').prop('disabled', !hasChecked);
    }

    $('#cartForm').on('submit', function (e) {
        const hasChecked = $('.cart-checkbox:checked').length > 0;
        if (!hasChecked) {
            e.preventDefault();
            showToast('Please select at least one product to checkout.');
        }
    });

    function updateSubtotal() {
        let subtotal = 0;
        $('.cart-checkbox:checked').each(function () {
            const rowId = $(this).val();
            const qty = parseInt($(`input[name="quantities[${rowId}]"]`).val()) || 0;
            const price = parseFloat($(`input[name="quantities[${rowId}]"]`).data('price')) || 0;
            subtotal += qty * price;
        });
        $('#subtotal-display').text('₱' + subtotal.toFixed(2));
    }

    updateCheckoutButton();
    $('.quantity-input').trigger('input');
    updateSubtotal();
});
</script>
@endpush