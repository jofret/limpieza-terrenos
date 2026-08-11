@extends('layouts.app')

@section('meta_title', 'Tu presupuesto - AltoParque')
@section('meta_description', 'AltoParque WhatsApp ✅ 11 7178 9529 | Revisá y aceptá tu presupuesto.')
@section('meta_robots', 'noindex, nofollow')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-200 p-6">
            <p class="text-gray-700"><span class="font-semibold text-gray-800">Cliente:</span> {{ $serviceOrder->customer->name }}</p>
            <p class="text-gray-700"><span class="font-semibold text-gray-800">Propiedad:</span> {{ $serviceOrder->property?->display_label ?? '—' }}</p>
            @if ($serviceOrder->category)
                <p class="text-gray-700"><span class="font-semibold text-gray-800">Servicio:</span> {{ $serviceOrder->category->name }}</p>
            @endif
        </div>

        <div class="p-8 space-y-6">
            <div class="text-center">
                <p class="text-sm text-gray-500">Precio del presupuesto</p>
                <p class="text-4xl font-bold text-green-700">
                    ${{ number_format((float) ($serviceOrder->final_price ?? $serviceOrder->price ?? 0), 2, ',', '.') }}
                </p>
            </div>

            @if ($serviceOrder->budget_comment)
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500 mb-1">Comentario</p>
                    <p class="text-gray-800">{{ $serviceOrder->budget_comment }}</p>
                </div>
            @endif

            <div
                x-data="{
                    accepted: {{ $serviceOrder->budget_accepted_at ? 'true' : 'false' }},
                    sending: false,
                    payment: [],
                    accept() {
                        this.sending = true;

                        fetch('{{ route('budget.accept', $serviceOrder->budget_token) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ payment_method_preference: this.payment }),
                        })
                            .then(() => { this.accepted = true; })
                            .finally(() => { this.sending = false; });
                    },
                }"
            >
                <template x-if="!accepted">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-2">¿Cómo preferís pagar?</p>
                            <label class="flex items-center gap-2 mb-1">
                                <input type="checkbox" value="efectivo" x-model="payment"> Efectivo
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" value="transferencia" x-model="payment"> Transferencia
                            </label>
                        </div>

                        <button
                            type="button"
                            @click="accept()"
                            :disabled="sending"
                            class="w-full bg-green-700 hover:bg-green-800 text-white font-semibold py-3 rounded-lg text-base disabled:opacity-60"
                        >
                            <span x-show="!sending">Aceptar presupuesto</span>
                            <span x-show="sending" x-cloak>Procesando...</span>
                        </button>
                    </div>
                </template>

                <template x-if="accepted">
                    <div class="bg-green-700 text-white rounded-lg p-4 text-center font-semibold">
                        ✅ ¡Gracias! Presupuesto aceptado. Nos ponemos en contacto para coordinar la fecha.
                    </div>
                </template>
            </div>

            <p class="text-center text-sm text-gray-500">
                Cualquier consulta, respondé el mensaje que te enviamos. ¡Gracias por confiar en AltoParque!
            </p>
        </div>
    </div>
</div>
@endsection
