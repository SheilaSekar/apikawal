<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dasbor APIKAWAL
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p>Selamat datang, {{ auth()->user()->name }}.</p>
                    <p class="mt-2">Peran: <strong>{{ auth()->user()->role->label() }}</strong> · Reputasi: {{ auth()->user()->reputation }}</p>
                    <p class="mt-2">Anda belum mengirim laporan. <a class="underline" href="{{ route('home') }}">Pelajari alur APIKAWAL</a>.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
