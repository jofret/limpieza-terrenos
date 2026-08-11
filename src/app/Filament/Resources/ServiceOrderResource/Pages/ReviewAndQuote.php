<?php

namespace App\Filament\Resources\ServiceOrderResource\Pages;

use App\Filament\Concerns\OpensWhatsAppInNewTab;
use App\Filament\Resources\ServiceOrderResource;
use App\Models\ServiceOrder;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class ReviewAndQuote extends Page implements HasForms
{
    use InteractsWithForms;
    use OpensWhatsAppInNewTab;

    protected static string $resource = ServiceOrderResource::class;

    protected static string $view = 'filament.resources.service-order-resource.pages.review-and-quote';

    public ServiceOrder $serviceOrder;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(int|string $record): void
    {
        $this->serviceOrder = ServiceOrder::with([
            'customer',
            'property',
            'category',
            'relevamiento.workItems.media',
        ])->findOrFail($record);

        abort_unless($this->serviceOrder->canReviewAndQuote(), 404);

        $this->form->fill([
            'final_price' => $this->serviceOrder->final_price,
            'final_price_notes' => $this->serviceOrder->final_price_notes,
            'budget_comment' => $this->serviceOrder->budget_comment,
        ]);
    }

    public function getTitle(): string
    {
        return 'Revisar y presupuestar';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Precio final')
                    ->schema([
                        Forms\Components\TextInput::make('final_price')
                            ->label('Precio final')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                        Forms\Components\Textarea::make('final_price_notes')
                            ->label('Observaciones del precio final')
                            ->helperText('Interno: no se muestra al cliente.')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('budget_comment')
                            ->label('Comentario para el cliente')
                            ->helperText('Público: aparece en el presupuesto que ve el cliente.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('Guardar')
                ->color('gray')
                ->action('save'),

            Actions\Action::make('sendBudget')
                ->label('Enviar presupuesto al cliente')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Enviar presupuesto')
                ->modalDescription(fn (): string => $this->sendBudgetDescription())
                ->modalSubmitActionLabel('Enviar ahora')
                ->modalSubmitAction(fn ($action) => $action->extraAttributes(static::whatsAppTriggerAttributes()))
                ->action('sendBudget'),
        ];
    }

    private function sendBudgetDescription(): string
    {
        if (! filled($this->serviceOrder->customer->phone)) {
            return 'El cliente no tiene teléfono cargado — no hay forma de enviarle el presupuesto.';
        }

        return 'Se guarda el precio final cargado y se envía por WhatsApp con un link para verlo y aceptarlo.';
    }

    public function save(): void
    {
        $this->serviceOrder->update($this->form->getState());

        Notification::make()
            ->title('Precio final guardado')
            ->success()
            ->send();
    }

    public function sendBudget()
    {
        $this->serviceOrder->update($this->form->getState());

        $token = $this->serviceOrder->generateBudgetToken();
        $enlace = url('/presupuesto/'.$token);

        if (! filled($this->serviceOrder->customer->phone)) {
            $this->js(static::closeWhatsAppTab());

            Notification::make()
                ->title('No se pudo enviar')
                ->body('El cliente no tiene teléfono cargado.')
                ->danger()
                ->send();

            return null;
        }

        $telefono = $this->serviceOrder->customer->whatsappPhone();

        $mensaje = "Hola {$this->serviceOrder->customer->name}! 👋\n\n";
        $mensaje .= "Ya está listo el presupuesto de *AltoParque* para tu servicio.\n\n";
        $mensaje .= "📋 Podés verlo y aceptarlo acá:\n";
        $mensaje .= $enlace."\n\n";
        $mensaje .= '¡Gracias por confiar en nosotros! 🌿';

        $mensajeCodificado = urlencode($mensaje);

        $whatsappLink = "https://api.whatsapp.com/send/?phone={$telefono}&text={$mensajeCodificado}&type=phone_number&app_absent=0";

        Notification::make()
            ->title('Presupuesto guardado')
            ->body('Se abrió WhatsApp con el mensaje listo para enviar.')
            ->success()
            ->send();

        $this->js(static::navigateWhatsAppTab($whatsappLink));

        return null;
    }
}
