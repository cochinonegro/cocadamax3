<section class="invitado-buscar" aria-label="Buscar programa">
    <div class="invitado-buscar__inner">
        <div class="invitado-buscar__badge" aria-hidden="true">
            <span>BUSCAR PROGRAMA</span>
            <span class="invitado-buscar__badge-arrow">▼</span>
        </div>

        <label
            for="invitado-programa-search"
            style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;"
        >Buscar programa</label>
        <input
            id="invitado-programa-search"
            type="search"
            class="invitado-buscar__input"
            placeholder="Escribe el nombre del programa Ej: Photoshop"
            wire:model.live.debounce.300ms="programaSearch"
            autocomplete="off"
        />
    </div>
</section>
