<div>
    <x-header :title="$title" separator progress-indicator>
    </x-header>
    <x-form wire:submit="authenticate">
        <x-input label="E-mail" icon="o-envelope" wire:model="email" />
        <x-input label="Senha" wire:model="password" icon="o-key" type="password" />

        <x-slot:actions>
            <x-button link="{{ route('password.request') }}" label="Esqueci minha senha" />
            <x-button label="Entrar" class="btn-success" type="submit" spinner="save" />
        </x-slot:actions>
    </x-form>
</div>
