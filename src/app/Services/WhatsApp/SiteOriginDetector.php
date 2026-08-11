<?php

namespace App\Services\WhatsApp;

class SiteOriginDetector
{
    /**
     * Busca en el texto del mensaje algún dominio conocido de
     * config('services.whatsapp_cloud_api.sitios') — usado por el hub para
     * decidir a qué sitio pertenece un mensaje. En este proyecto (spoke) la
     * config 'sitios' no está poblada, así que esto siempre devuelve null y
     * se usa el default_site configurado.
     */
    public static function detectar(string $texto): ?string
    {
        $sitios = array_keys(config('services.whatsapp_cloud_api.sitios', []));

        foreach ($sitios as $dominio) {
            if (str_contains($texto, $dominio)) {
                return $dominio;
            }
        }

        return null;
    }
}
