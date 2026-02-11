<x-guest-layout>

    <form method="post" action="{{ route('login') }}">
        @csrf
        <p class="text-center">
            {{ trans('two-factor::messages.continue') }}
        </p>
        <div class="form-row justify-content-center py-3">
            <div>
                <x-input-label for="{{ $input }}" :value="__('Code')" />
                <x-text-input id="{{ $input }}" class="block mt-1 w-full" type="text" name="{{ $input }}" required autofocus/>
                <x-input-error :messages="$errors->get($input)" class="mt-2" />
            </div>

            <div class="w-100"></div>
            <div class="flex items-center justify-end mt-4">
                <x-primary-button>
                    {{ __('Verify') }}
                </x-primary-button>
            </div>
        </div>
    </form>
</x-guest-layout>