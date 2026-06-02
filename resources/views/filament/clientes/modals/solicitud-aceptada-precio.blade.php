<div class="solicitud-aceptada-precio">
    <p class="solicitud-aceptada-precio__titulo">
        SOLICITUD ACEPTADA
        @if (filled($precioFormateado))
            , EL MONTO DE ESTE PACK SOLICITADO ES DE:
        @endif
    </p>

    @if (filled($precioFormateado))
        <p class="solicitud-aceptada-precio__monto">{{ $precioFormateado }}</p>
    @else
        <p class="solicitud-aceptada-precio__sin-precio">
            El administrador aún no ha indicado un monto acordado. Podrás verlo más adelante o consultarlo con soporte.
        </p>
    @endif

    <p class="solicitud-aceptada-precio__hint">
        Pulsa «Ir a Pedidos» para descargar tu programa.
    </p>
</div>
