<?php

namespace App\Filament\Concerns;

/**
 * Abre WhatsApp en una pestaña nueva sin que el navegador la bloquee como
 * pop-up. El link real solo se conoce después de un round-trip de Livewire,
 * y para cuando esa respuesta vuelve ya no estamos dentro del gesto
 * sincrónico del click — la mayoría de los navegadores bloquean un
 * window.open() disparado ahí.
 *
 * Truco: abrir una pestaña en blanco de forma sincrónica en el click (eso
 * sí cuenta como gesto del usuario) y, cuando el servidor responde con el
 * link definitivo, navegar esa misma pestaña ya abierta.
 */
trait OpensWhatsAppInNewTab
{
    protected static function whatsAppTriggerAttributes(): array
    {
        return [
            'onclick' => "window.__waTab = window.open('', '_blank')",
        ];
    }

    protected static function navigateWhatsAppTab(string $url): string
    {
        return 'if (window.__waTab) { window.__waTab.location = '.json_encode($url).'; window.__waTab = null; } '.
            'else { window.open('.json_encode($url).', "_blank"); }';
    }

    protected static function closeWhatsAppTab(): string
    {
        return 'if (window.__waTab) { window.__waTab.close(); window.__waTab = null; }';
    }
}
