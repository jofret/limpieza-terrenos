<?php

namespace App\Filament\Concerns;

use App\Models\Relevamiento;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;

/**
 * Nota: en este proyecto el relevador no tiene portal propio (solo el
 * RelevadorResource de gestión en el admin) — el mensaje de WhatsApp es
 * informativo, sin enlace a un dashboard, y el relevamiento se completa
 * desde el panel admin.
 */
trait SendsRelevamientoToRelevador
{
    public static function sendRelevamientoToRelevador(Relevamiento $record, $livewire): void
    {
        $relevador = $record->relevador;

        if (! $relevador || ! filled($relevador->whatsapp)) {
            $livewire->js(static::closeWhatsAppTab());

            Notification::make()
                ->title('No se pudo enviar')
                ->body('El relevador asignado no tiene WhatsApp cargado.')
                ->danger()
                ->send();

            return;
        }

        $record->update(['status' => 'enviado_a_relevador']);

        $telefono = $relevador->whatsappPhone();

        $horario = $record->scheduled_time_from
            ? Carbon::parse($record->scheduled_time_from)->format('H:i').($record->scheduled_time_to ? ' a '.Carbon::parse($record->scheduled_time_to)->format('H:i') : '')
            : null;

        $mensaje = "Hola {$relevador->name}! 👋\n\n";
        $mensaje .= "Te asignamos un nuevo relevamiento en *AltoParque*:\n\n";
        $mensaje .= "📍 {$record->property->display_label}\n";
        $mensaje .= "🔧 {$record->service_type_label}\n";
        if ($record->scheduled_date) {
            $mensaje .= '🗓 '.Carbon::parse($record->scheduled_date)->format('d/m/Y').($horario ? " ({$horario})" : '')."\n";
        }
        $mensaje .= "\nCualquier duda, coordinamos por acá.";

        $mensajeCodificado = urlencode($mensaje);

        $whatsappLink = "https://api.whatsapp.com/send/?phone={$telefono}&text={$mensajeCodificado}&type=phone_number&app_absent=0";

        Notification::make()
            ->title('Relevamiento enviado a relevador')
            ->body('Se abrió WhatsApp con el mensaje listo para enviar.')
            ->success()
            ->send();

        $livewire->js(static::navigateWhatsAppTab($whatsappLink));
    }

    public static function sendReopenApprovedToRelevador(Relevamiento $record, $livewire): void
    {
        $relevador = $record->relevador;

        if (! $relevador || ! filled($relevador->whatsapp)) {
            $record->approveReopen();
            $livewire->js(static::closeWhatsAppTab());

            Notification::make()
                ->title('Reapertura aprobada')
                ->body('El relevador asignado no tiene WhatsApp cargado, no se le pudo avisar.')
                ->warning()
                ->send();

            return;
        }

        $record->approveReopen();

        $telefono = $relevador->whatsappPhone();

        $mensaje = "Hola {$relevador->name}! 👋\n\n";
        $mensaje .= "Se aprobó la reapertura de un relevamiento en *AltoParque*:\n\n";
        $mensaje .= "📍 {$record->property->display_label}\n";
        $mensaje .= "🔧 {$record->service_type_label}\n";
        $mensaje .= "\nYa lo podés volver a editar desde el panel.";

        $mensajeCodificado = urlencode($mensaje);

        $whatsappLink = "https://api.whatsapp.com/send/?phone={$telefono}&text={$mensajeCodificado}&type=phone_number&app_absent=0";

        Notification::make()
            ->title('Reapertura aprobada')
            ->body('Se abrió WhatsApp con el aviso listo para enviar.')
            ->success()
            ->send();

        $livewire->js(static::navigateWhatsAppTab($whatsappLink));
    }
}
