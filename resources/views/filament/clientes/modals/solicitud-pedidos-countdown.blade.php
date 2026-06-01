<div
    class="solicitud-pedidos-countdown"
    x-data="{
        seconds: 30,
        interval: null,
        init() {
            this.seconds = 30;
            if (this.interval) {
                clearInterval(this.interval);
            }
            this.interval = setInterval(() => {
                if (this.seconds <= 1) {
                    clearInterval(this.interval);
                    this.seconds = 0;
                    $wire.habilitarBotonPedidosModal();
                } else {
                    this.seconds--;
                }
            }, 1000);
        },
        destroy() {
            if (this.interval) {
                clearInterval(this.interval);
            }
        },
    }"
>
    <p class="mb-4 text-center text-sm text-gray-600 dark:text-gray-400">
        Una vez te hayan confirmado aceptar la descarga dale al botón aquí abajo:
    </p>

    <div class="solicitud-pedidos-countdown__timer">
        <p class="solicitud-pedidos-countdown__label">Espera la confirmación</p>
        <p class="solicitud-pedidos-countdown__seconds" x-text="seconds"></p>
        <p class="solicitud-pedidos-countdown__unit">segundos</p>
    </div>

    <p
        x-show="seconds === 0"
        x-cloak
        class="solicitud-pedidos-countdown__ready"
    >
        Ya puedes ir a Pedidos y descargar tu programa.
    </p>
</div>
