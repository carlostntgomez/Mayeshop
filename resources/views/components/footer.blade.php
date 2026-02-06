<!-- FOOTER SECTION START -->
    <footer class="ul-footer">
        <div class="ul-inner-container">
            <div class="ul-footer-top">
                <!-- Marcas (Tipos de Producto) -->
                <div class="ul-footer-top-widget">
                    <h3 class="ul-footer-top-widget-title">Tipos de Producto</h3>
                    <div class="ul-footer-top-widget-links">
                        @foreach ($footerProductTypes as $productType)
                            <a href="{{ route('product_type.show', $productType->slug) }}">{{ $productType->name }}</a>
                        @endforeach
                    </div>
                </div>

                <!-- Categorías -->
                <div class="ul-footer-top-widget">
                    <h3 class="ul-footer-top-widget-title">Categorías</h3>
                    <div class="ul-footer-top-widget-links">
                        @foreach ($footerCategories as $category)
                            <a href="{{ route('category.show', ['productType' => $category->productType->slug, 'category' => $category->slug]) }}">{{ $category->name }}</a>
                        @endforeach
                    </div>
                </div>

                <!-- Información -->
                <div class="ul-footer-top-widget">
                    <h3 class="ul-footer-top-widget-title">Información</h3>
                    <div class="ul-footer-top-widget-links">
                        <a href="{{ route('about') }}">Acerca de Nosotros</a>
                        <a href="{{ route('contact') }}">Contacto</a>
                        <a href="{{ route('faq') }}">Preguntas Frecuentes</a>
                        <a href="{{ route('blog.index') }}">Blog</a>
                    </div>
                </div>

                <!-- Ayuda -->
                <div class="ul-footer-top-widget">
                    <h3 class="ul-footer-top-widget-title">Ayuda</h3>
                    <div class="ul-footer-top-widget-links">
                        <a href="{{ route('terms') }}">Términos y Condiciones</a>
                        <a href="{{ route('privacy') }}">Política de Privacidad</a>
                        <a href="{{ route('refund') }}">Política de Reembolso</a>
                    </div>
                </div>
            </div>

            <div class="ul-footer-middle">


                <!-- single widget -->
                <div class="ul-footer-middle-widget">
                    <h3 class="ul-footer-middle-widget-title">Síguenos</h3>
                    <div class="ul-footer-middle-widget-content">
                        <div class="social-links">
                        @foreach ($socialMediaLinks as $socialMediaLink)
                            <a href="{{ $socialMediaLink->url }}" target="_blank"><i class="{{ $socialMediaLink->icon }}"></i></a>
                        @endforeach
                    </div>
                    </div>
                </div>

                <!-- single widget -->
                <div class="ul-footer-middle-widget">
                    <h3 class="ul-footer-middle-widget-title">¿Necesitas ayuda? ¡Contáctanos!</h3>
                    <div class="ul-footer-middle-widget-content">
                        <div class="contact-nums">
                            @if ($whatsappPhoneNumber)
                                <a href="https://wa.me/{{ $whatsappPhoneNumber }}" target="_blank"><i class="fab fa-whatsapp"></i> {{ $whatsappPhoneNumber }}</a>
                            @else
                                <p>Número no configurado</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- single widget -->
                <div class="ul-footer-middle-widget align-self-center">
                    <a href=""><img src="{{ asset('static/picture/logo-white.svg') }}" alt="logo" class="logo"></a>
                </div>
            </div>

            <div class="ul-footer-bottom">
                <p class="copyright-txt">Copyright &copy; {{ date('Y') }}. Maye Shop. Todos los derechos reservados. Diseñado por CarlittosTnT.</p>

            </div>
        </div>
    </footer>
    <!-- FOOTER SECTION END -->