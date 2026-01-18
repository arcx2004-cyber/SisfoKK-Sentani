<form wire:submit="authenticate" class="space-y-8">
    {{ $this->form }}

    <div class="fi-form-actions custom-login-actions">
        @foreach ($this->getCachedFormActions() as $action)
            {{ $action }}
        @endforeach
    </div>
</form>
