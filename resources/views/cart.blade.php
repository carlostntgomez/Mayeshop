@extends('layouts.app')

@section('title', 'Tu Carrito de Compras - Maye Shop')

@section('meta_description', 'Revisa los artículos en tu carrito de compras en Maye Shop. Completa tu pedido de forma segura y rápida.')
@section('meta_keywords', 'carrito de compras, mi carrito, checkout, pagar, Maye Shop')

@section('meta_tags')
    <meta property="og:title" content="@yield('title')">
    <meta property="og:description" content="@yield('meta_description')">
    <meta property="og:url" content="{{ route('cart.index') }}">
    <meta property="og:type" content="website">
@endsection

@section('content')
    <x-breadcrumb :title="'Lista de Carrito'" :crumbs="$crumbs" />


    <main>
        <div class="ul-cart-container">
            <div class="cart-top">
                <div class="table-responsive">
                    <table class="ul-cart-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($cartItems as $item)
                                <tr>
                                    <td>
                                        <div class="ul-cart-product">
                                            <a href="{{ route('product.show', ['slug' => $item['product']->slug]) }}" class="ul-cart-product-img"><img src="{{ asset('storage/' . $item['product']->main_image) }}" alt="{{ $item['product']->name }}"></a>
                                            <a href="{{ route('product.show', ['slug' => $item['product']->slug]) }}" class="ul-cart-product-title">{{ $item['product']->name }}</a>
                                        </div>
                                    </td>
                                    <td><span class="ul-cart-item-price">${{ number_format($item['price'], 2, ',', '.') }}</span></td>
                                    <td>
                                        <div class="ul-product-details-quantity mt-0">
                                            <form action="#" class="ul-product-quantity-wrapper">
                                                <input type="number" name="product-quantity" class="ul-product-quantity" value="{{ $item['quantity'] }}" min="1" readonly="">
                                                {{-- Quantity buttons are removed as per previous instruction to only allow 1 unit per product --}}
                                            </form>
                                        </div>
                                    </td>
                                    <td><span class="ul-cart-item-subtotal">${{ number_format($item['subtotal'], 2, ',', '.') }}</span></td>
                                    <td>
                                        <div class="ul-cart-item-remove">
                                            <button class="remove-from-cart-btn" data-product-id="{{ $item['product']->id }}"><i class="fas fa-times"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="empty-cart-row">
                                    <td colspan="5" class="text-center">Tu carrito está vacío.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Coupon section removed as it's not implemented in backend logic --}}
                {{-- Update Cart button is removed as quantity is fixed at 1 --}}
            </div>

            <div class="cart-bottom">
                <div class="ul-cart-expense-overview">
                    <h3 class="ul-cart-expense-expense-overview-title">Total</h3>
                    <div class="middle">
                        <div class="single-row">
                            <span class="inner-title">Subtotal</span>
                            <span class="number" id="cart-subtotal">${{ number_format($subtotal, 2, ',', '.') }}</span>
                        </div>


                    </div>
                    <div class="bottom">
                        <div class="single-row">
                            <span class="inner-title">Total</span>
                            <span class="number" id="cart-total">${{ number_format($total, 2, ',', '.') }}</span>
                        </div>

                        <a href="{{ route('checkout.index') }}" class="ul-cart-checkout-direct-btn">PAGAR</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que quieres eliminar este producto de tu carrito?
                </div>
                <div class="modal-footer">
                    <button type="button" class="ul-btn" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="ul-btn btn-danger" id="confirmDeleteBtn">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let productIdToDelete = null;

        document.querySelectorAll('.remove-from-cart-btn').forEach(button => {
            button.addEventListener('click', function() {
                productIdToDelete = this.dataset.productId;
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
                deleteModal.show();
            });
        });

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (productIdToDelete) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch('{{ route('cart.remove') }}', { // We will define this route
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        product_id: productIdToDelete
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the product row from the table
                        const rowToRemove = document.querySelector(`.remove-from-cart-btn[data-product-id="${productIdToDelete}"]`).closest('tr');
                        if (rowToRemove) {
                            rowToRemove.remove();
                        }

                        // Update totals
                        document.getElementById('cart-subtotal').textContent = '$' + parseFloat(data.subtotal).toFixed(2).replace('.', ',');
                        document.getElementById('cart-total').textContent = '$' + parseFloat(data.total).toFixed(2).replace('.', ',');

                        // Update cart count in header if it exists
                        const cartCountElement = document.getElementById('cart-count'); // Assuming cart-count is still in header
                        if (cartCountElement) {
                            cartCountElement.textContent = data.cartCount;
                        }

                        // Check if cart is now empty and display appropriate message
                        const tbody = document.querySelector('.ul-cart-table tbody');
                        if (tbody && tbody.children.length === 0) {
                            const emptyRow = document.getElementById('empty-cart-row');
                            if (emptyRow) {
                                emptyRow.style.display = 'table-row'; // Make sure it's visible if it was hidden
                            } else {
                                // If the empty-cart-row was never rendered (because items were present initially)
                                const newEmptyRow = document.createElement('tr');
                                newEmptyRow.id = 'empty-cart-row';
                                newEmptyRow.innerHTML = '<td colspan="5" class="text-center">Tu carrito está vacío.</td>';
                                tbody.appendChild(newEmptyRow);
                            }
                            // Optionally hide the cart-bottom totals section
                             document.querySelector('.cart-bottom').style.display = 'none';
                        }


                        // Close the modal
                        const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmationModal'));
                        deleteModal.hide();
                    } else {
                        alert('Error al eliminar el producto: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Hubo un error al comunicarse con el servidor.');
                });
            }
        });
    });
</script>
@endpush
@endsection