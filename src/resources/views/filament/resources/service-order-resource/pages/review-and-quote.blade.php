<x-filament-panels::page>
    <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <h3 class="text-base font-semibold mb-4">Datos del relevamiento</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">Cliente</dt>
                <dd class="font-medium">{{ $serviceOrder->customer?->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Propiedad</dt>
                <dd class="font-medium">{{ $serviceOrder->property?->display_label ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Servicio</dt>
                <dd class="font-medium">{{ $serviceOrder->category?->name ?? $serviceOrder->category_other ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Precio estimado por el relevador</dt>
                <dd class="font-medium">{{ $serviceOrder->relevamiento?->estimated_price ? '$'.number_format((float) $serviceOrder->relevamiento->estimated_price, 2, ',', '.') : '—' }}</dd>
            </div>
        </dl>

        @if ($serviceOrder->relevamiento?->workItems?->isNotEmpty())
            <h4 class="text-sm font-semibold mt-6 mb-2">Ítems cargados por el relevador</h4>
            <ul class="space-y-2 text-sm">
                @foreach ($serviceOrder->relevamiento->workItems as $item)
                    <li class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                        <p>{{ $item->description ?: '—' }}</p>
                        @if ($item->observations)
                            <p class="text-gray-500 mt-1">{{ $item->observations }}</p>
                        @endif
                        @if ($item->includes_pickup)
                            <p class="text-green-700 dark:text-green-400 font-semibold mt-1">Incluye retiro</p>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($serviceOrder->relevamiento?->notes)
            <h4 class="text-sm font-semibold mt-6 mb-2">Notas del relevamiento</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $serviceOrder->relevamiento->notes }}</p>
        @endif
    </div>

    <form wire:submit.prevent="save" class="mt-6">
        {{ $this->form }}
    </form>
</x-filament-panels::page>
