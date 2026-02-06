@extends('layouts.app')

@section('title', 'Finalizar Compra - Maye Shop')

@section('meta_description', 'Completa tu compra de forma segura en Maye Shop. Ingresa tus datos de envío y pago para recibir tus productos.')
@section('meta_keywords', 'finalizar compra, checkout, pago, envío, Maye Shop')

@section('meta_tags')
    <meta property="og:title" content="@yield('title')">
    <meta property="og:description" content="@yield('meta_description')">
    <meta property="og:url" content="{{ route('checkout.index') }}">
    <meta property="og:type" content="website">
@endsection

@section('content')
    <x-breadcrumb :title="'Pagar'" :crumbs="$crumbs" />

    <main>
        <!-- CHEKOUT SECTION START -->
        <div class="ul-cart-container">
            <h3 class="ul-checkout-title">Detalles de Facturación</h3>
            <form action="{{ route('checkout.store') }}" method="POST" class="ul-checkout-form">
                @csrf {{-- Add CSRF token for security --}}
                <div class="row ul-bs-row row-cols-2 row-cols-xxs-1">
                    <!-- left side / checkout form -->
                    <div class="col">
                        <div class="row row-cols-lg-2 row-cols-1 ul-bs-row">
                            <!-- first name -->
                            <div class="form-group">
                                <label for="firstname">Nombre*</label>
                                <input type="text" name="firstname" id="firstname" placeholder="Ingresa tu Nombre">
                            </div>

                            <!-- last name -->
                            <div class="form-group">
                                <label for="lastname">Apellido*</label>
                                <input type="text" name="lastname" id="lastname" placeholder="Ingresa tu Apellido">
                            </div>

                            <!-- company name -->
                            <div class="form-group">
                                <label for="companyname">Nombre de la Empresa</label>
                                <input type="text" name="companyname" id="companyname" placeholder="Ingresa el Nombre de tu Empresa">
                            </div>

                            <!-- country -->
                            <div class="form-group ul-checkout-country-wrapper">
                                <label for="ul-checkout-country">País*</label>
                                <select name="country" id="ul-checkout-country">
                                    <option value="CO" selected>Colombia</option>
                                </select>
                            </div>

                            <!-- address 1 -->
                            <div class="form-group">
                                <label for="address1">Dirección*</label>
                                <input type="text" name="address1" id="address1" placeholder="Ej: Calle 10 # 20-30">
                            </div>



                            <!-- city -->
                            <div class="form-group">
                                <label for="city">Ciudad o Pueblo*</label>
                                <input type="text" name="city" id="city" placeholder="Ingresa tu Ciudad o Pueblo">
                            </div>

                            <!-- state -->
                            <div class="form-group">
                                <label for="state">Departamento*</label>
                                <input type="text" name="state" id="state" placeholder="Ingresa tu Departamento">
                            </div>



                            <!-- phone -->
                            <div class="form-group">
                                <label for="phone">Teléfono*</label>
                                <input type="text" name="phone" id="phone" placeholder="Ingresa tu Número de Teléfono">
                            </div>

                            <!-- email -->
                            <div class="form-group col-lg-12">
                                <label for="email">Correo Electrónico*</label>
                                <input type="email" name="email" id="email" placeholder="Ingresa tu Correo Electrónico">
                            </div>
                        </div>

                    </div>

                    <!-- right side / payment methods -->
                    <div class="col">
                        <div class="ul-checkout-payment-methods">
                            <div class="form-group">
                                <label for="payment-option-1">
                                    <input type="radio" name="payment-methods" id="payment-option-1" hidden checked>
                                    <span class="ul-radio-wrapper"></span>
                                    <span class="ul-checkout-payment-method">
                                        <span class="title">Transferencia Bancaria Directa</span>
                                        <span class="descr">Realiza tu pago directamente en nuestra cuenta bancaria. Por favor, usa el ID de tu pedido como referencia de pago. Tu pedido no se enviará hasta que los fondos se hayan liquidado en nuestra cuenta.</span>
                                    </span>
                                </label>
                            </div>

                            <div class="form-group">
                                <label for="payment-option-2">
                                    <input type="radio" name="payment-methods" id="payment-option-2" hidden>
                                    <span class="ul-radio-wrapper"></span>
                                    <span class="ul-checkout-payment-method">
                                        <span class="title">Pago Contra Entrega</span>
                                        <span class="descr">Paga en efectivo cuando recibas tu pedido en la puerta de tu casa.</span>
                                    </span>
                                </label>
                            </div>
                            <button type="submit" class="ul-checkout-form-btn">Realizar Pedido</button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- bill summary -->
            <div class="row ul-bs-row row-cols-2 row-cols-xxs-1">
                <div class="ul-checkout-bill-summary">
                    <h4 class="ul-checkout-bill-summary-title">Tu Pedido</h4>

                    <div>
                        <div class="ul-checkout-bill-summary-header">
                            <span class="left">Producto</span>
                            <span class="right">Subtotal</span>
                        </div>


                        <div class="ul-checkout-bill-summary-body">
                            @forelse ($cartItems as $item)
                                <div class="single-row">
                                    <span class="left">{{ $item['product']->name }} x {{ $item['quantity'] }}</span>
                                    <span class="right">${{ number_format($item['subtotal'], 2, ',', '.') }}</span>
                                </div>
                            @empty
                                <div class="single-row">
                                    <span class="left">No hay productos en el carrito.</span>
                                    <span class="right"></span>
                                </div>
                            @endforelse
                            <div class="single-row">
                                <span class="left">Subtotal</span>
                                <span class="right">${{ number_format($subtotal, 2, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="ul-checkout-bill-summary-footer ul-checkout-bill-summary-header">
                            <span class="left">Total</span>
                            <span class="right">${{ number_format($total, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- CHEKOUT SECTION END -->
    </main>

    @push('scripts')
    <script>
        function showCheckoutErrorModal(title, message) {
            const modalElement = document.getElementById('checkoutErrorModal');
            const modalTitle = document.getElementById('checkoutErrorModalLabel');
            const modalBody = document.getElementById('checkoutErrorModalBody');

            if (!modalElement) {
                console.error('Checkout error modal element not found.');
                alert(title + '\n' + message); // Fallback to alert
                return;
            }

            modalTitle.textContent = title;
            modalBody.innerHTML = `<p>${message}</p>`;

            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        }

        function showWhatsAppConfirmationModal(data) {
            const modalElement = document.getElementById('whatsAppConfirmationModal');
            const modalBody = document.getElementById('whatsAppConfirmationModalBody');
            const whatsappLink = document.getElementById('whatsappLink');

            if (!modalElement || !data.order || !data.whatsappNumber) {
                console.error('WhatsApp confirmation modal element, order data, or WhatsApp number not found.');
                // Fallback to a simple success message if something is missing
                alert('¡Tu pedido ha sido realizado con éxito! ID del Pedido: ' + data.orderId);
                window.location.href = '/';
                return;
            }

            // Construct the WhatsApp message
            let message = `¡Hola! 👋 Acabo de realizar un pedido en Maye Shop.\n\n`;
            message += `*Número de Pedido:* ${data.orderId}\n\n`;
            message += `*Resumen del Pedido:*\n`;
            data.order.items.forEach(item => {
                message += `- ${item.product_name} (x${item.quantity}) - $${parseFloat(item.price * item.quantity).toFixed(2)}\n`;
            });
            message += `\n*Total:* $${parseFloat(data.order.total_amount).toFixed(2)}\n\n`;
            message += `¡Gracias!`;

            const whatsappUrl = `https://wa.me/${data.whatsappNumber}?text=${encodeURIComponent(message)}`;

            // Set the link for the WhatsApp button
            whatsappLink.href = whatsappUrl;

            // Customize modal body message
            modalBody.innerHTML = `<p>¡Tu pedido ha sido recibido con éxito!</p><p>Para completar tu compra, por favor contáctanos por WhatsApp para coordinar el pago y el envío.</p><p>Tu número de pedido es: <strong>#${data.orderId}</strong></p>`;
            
            const modal = new bootstrap.Modal(modalElement);
            modal.show();

            // Redirect to homepage when the modal is closed
            modalElement.addEventListener('hidden.bs.modal', function () {
                window.location.href = '/';
            }, { once: true });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Check for Laravel validation errors on page load
            @if ($errors->any())
                let errorMessage = '';
                @foreach ($errors->all() as $error)
                    errorMessage += '{{ $error }}<br>';
                @endforeach
                showCheckoutErrorModal('Error de Validación', errorMessage);
            @endif

            const checkoutForm = document.querySelector('.ul-checkout-form');
            const submitButton = checkoutForm.querySelector('button[type="submit"]');

            checkoutForm.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent default form submission

                submitButton.disabled = true;
                submitButton.textContent = 'Procesando...';

                const formData = new FormData(checkoutForm);
                const jsonData = {};
                formData.forEach((value, key) => {
                    jsonData[key] = value;
                });

                // Manually get the selected payment method value
                const selectedPaymentMethod = checkoutForm.querySelector('input[name="payment-methods"]:checked');
                if (selectedPaymentMethod) {
                    jsonData['payment-methods'] = selectedPaymentMethod.id; // Use the ID as the value
                }

                fetch(checkoutForm.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(jsonData)
                })
                .then(response => {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Realizar Pedido';

                    if (response.ok) {
                        return response.json();
                    } else if (response.status === 422) { // Laravel validation error
                        return response.json().then(data => {
                            let errorMessage = '';
                            for (const key in data.errors) {
                                errorMessage += data.errors[key].join('<br>') + '<br>';
                            }
                            showCheckoutErrorModal('Error de Validación', errorMessage);
                            return Promise.reject('Validation Error');
                        });
                    } else {
                        return response.json().then(data => {
                            showCheckoutErrorModal('Error', data.message || 'Hubo un error al procesar tu pedido.');
                            return Promise.reject('Server Error');
                        });
                    }
                })
                .then(data => {
                    if (data.success) {
                        showWhatsAppConfirmationModal(data);
                    } else {
                        showCheckoutErrorModal('Error', data.message || 'Hubo un error al procesar tu pedido.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (error !== 'Validation Error' && error !== 'Server Error') {
                        showCheckoutErrorModal('Error', 'Hubo un error de red o inesperado.');
                    }
                });
            });
        });
    </script>
    @endpush

    <!-- Checkout Error Modal Structure -->
    <div class="modal fade" id="checkoutErrorModal" tabindex="-1" aria-labelledby="checkoutErrorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="checkoutErrorModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="checkoutErrorModalBody">
                    <!-- Error messages will be injected here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="ul-btn" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- WhatsApp Confirmation Modal Structure -->
    <div class="modal fade" id="whatsAppConfirmationModal" tabindex="-1" aria-labelledby="whatsAppConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="whatsAppConfirmationModalLabel">¡Pedido Realizado!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="whatsAppConfirmationModalBody">
                    <!-- Message will be injected here -->
                </div>
                <div class="modal-footer">
                    <a href="#" id="whatsappLink" target="_blank" class="ul-btn btn-success">Contactar por WhatsApp</a>
                    <button type="button" class="ul-btn" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection