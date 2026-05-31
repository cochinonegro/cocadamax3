@if ($user = auth()->user())
    <span class="fi-clientes-user-name hidden sm:inline">
        {{ $user->name }}
    </span>
@endif
