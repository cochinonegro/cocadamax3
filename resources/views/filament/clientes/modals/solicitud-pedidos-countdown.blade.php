<div
    class="solicitud-pedidos-countdown"
    x-data="{
        seconds: 15,
        interval: null,
        init() {
            this.seconds = 15;
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
        El administrador gestionará tu solicitud por Telegram. Cuando te confirme la aceptación, pulsa CONTINUAR:
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
        Ya puedes pulsar CONTINUAR para ver el resumen de tu solicitud.
    </p>
</div>
