<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-semibold">Status relawan: {{ $user->volunteer_status->label() }}</h3>
                    @if($user->role->value === 'warga' && in_array($user->volunteer_status->value, ['none', 'rejected'], true))
                        <form class="mt-4 space-y-3" method="POST" action="{{ route('profile.volunteer.apply') }}">
                            @csrf
                            <label class="form-label" for="motivation">Alasan ingin menjadi relawan</label>
                            <textarea class="form-control" id="motivation" name="motivation" minlength="20" maxlength="500" required aria-describedby="motivation-help"></textarea>
                            <p id="motivation-help" class="text-sm text-slate-600">20–500 karakter. Pengajuan akan ditinjau admin.</p>
                            @error('motivation')<p class="text-red-700" role="alert">{{ $message }}</p>@enderror
                            <button class="button button-primary">Ajukan menjadi relawan</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
