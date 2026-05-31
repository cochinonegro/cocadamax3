@if ($user = auth()->user())
    <span class="fi-auth-user-name">
        {{ filament()->getUserName($user) }}
    </span>
@endif
