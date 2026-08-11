<?php

namespace App\Filament\Resources\RelevamientoResource\Pages;

use App\Filament\Concerns\OpensWhatsAppInNewTab;
use App\Filament\Concerns\SendsRelevamientoToRelevador;
use App\Filament\Resources\RelevamientoResource;
use App\Models\Relevamiento;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRelevamiento extends EditRecord
{
    use OpensWhatsAppInNewTab, SendsRelevamientoToRelevador;

    protected static string $resource = RelevamientoResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return RelevamientoResource::normalizeCategoryData($data);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve_reopen')
                ->label('Aprobar reapertura')
                ->icon('heroicon-o-lock-open')
                ->color('warning')
                ->visible(fn (Relevamiento $record): bool => $record->reopen_requested_at !== null)
                ->requiresConfirmation()
                ->modalHeading('Aprobar reapertura del relevamiento')
                ->modalDescription('El relevamiento vuelve a quedar editable. Se abre WhatsApp con el aviso listo para el relevador.')
                ->modalSubmitActionLabel('Aprobar reapertura')
                ->modalSubmitAction(fn ($action) => $action->extraAttributes(static::whatsAppTriggerAttributes()))
                ->action(fn (Relevamiento $record, $livewire) => static::sendReopenApprovedToRelevador($record, $livewire)),

            Actions\Action::make('enviar')
                ->label('Enviar a relevador')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->visible(fn (Relevamiento $record): bool => $record->status === 'pendiente')
                ->requiresConfirmation()
                ->modalHeading('Enviar relevamiento a relevador')
                ->modalDescription('Revisá que la propiedad, el relevador asignado y la fecha/horario estén correctos antes de confirmar. Al enviarlo, se abre WhatsApp con el aviso listo para el relevador.')
                ->modalSubmitActionLabel('Confirmar envío')
                ->modalSubmitAction(fn ($action) => $action->extraAttributes(static::whatsAppTriggerAttributes()))
                ->action(function (Relevamiento $record, $livewire) {
                    $this->save();
                    static::sendRelevamientoToRelevador($record, $livewire);
                }),

            Actions\Action::make('enviado_indicator')
                ->label('Enviado a relevador')
                ->icon('heroicon-o-check-circle')
                ->color('gray')
                ->disabled()
                ->visible(fn (Relevamiento $record): bool => $record->status === 'enviado_a_relevador'),

            Actions\DeleteAction::make()
                ->modalDescription(fn (Relevamiento $record): ?string => RelevamientoResource::deleteWarning($record)),
        ];
    }
}
