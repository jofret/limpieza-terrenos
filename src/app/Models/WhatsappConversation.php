<?php

namespace App\Models;

use App\Mail\WhatsappConversationDerivedMailable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Mail;

class WhatsappConversation extends Model
{
    use HasFactory;

    public const ESTADOS = [
        'claudia_atendiendo' => 'Claudia atendiendo',
        'esperando_agenda_visita' => 'Esperando agenda de visita',
        'esperando_cotizacion_foto' => 'Esperando cotización por foto',
        'con_humano' => 'Con humano',
        'cerrada' => 'Cerrada',
    ];

    private const ADMIN_EMAILS = [
        'info@serviciodejardineria.com.ar',
        'jofretjofret@gmail.com',
    ];

    protected $fillable = [
        'customer_id',
        'sitio_origen',
        'zona',
        'servicio_solicitado',
        'foto_path',
        'estado_conversacion',
        'asignado_a',
    ];

    /**
     * Motivo de la derivación a humano, para el email de aviso. Es transitorio
     * (no se persiste): lo setea quien llama a derivarAHumano() y lo lee el
     * hook de abajo en el mismo request.
     */
    public ?string $motivoDerivacion = null;

    protected static function booted(): void
    {
        static::updated(function (WhatsappConversation $conversation): void {
            if (
                $conversation->wasChanged('estado_conversacion')
                && $conversation->estado_conversacion === 'con_humano'
                && blank($conversation->asignado_a)
            ) {
                Mail::to(self::ADMIN_EMAILS)->send(new WhatsappConversationDerivedMailable(
                    $conversation,
                    $conversation->motivoDerivacion ?? 'Conversación derivada a un humano.',
                ));
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function asignadoA(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsappMessage::class)->orderBy('enviado_en');
    }

    public function estaConHumano(): bool
    {
        return $this->estado_conversacion === 'con_humano';
    }

    public function derivarAHumano(?int $userId = null, ?string $motivo = null): void
    {
        $this->motivoDerivacion = $motivo;

        $this->update([
            'estado_conversacion' => 'con_humano',
            'asignado_a' => $userId ?? $this->asignado_a,
        ]);
    }

    public function cerrar(): void
    {
        $this->update(['estado_conversacion' => 'cerrada']);
    }

    public function reabrir(): void
    {
        $this->update(['estado_conversacion' => 'claudia_atendiendo']);
    }
}
