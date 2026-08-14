<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class WorkOrder extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public const PIPELINE_STATUSES = [
        'nueva' => 'Nueva',
        'programado' => 'Programado',
        'en_curso' => 'En curso',
        'completado' => 'Completado',
    ];

    public const OTHER_STATUSES = [
        'cancelado' => 'Cancelado',
        'reprogramado' => 'Reprogramado',
    ];

    protected $fillable = [
        'service_order_id',
        'work_date',
        'time_slot',
        'status',
        'conformity_token',
        'conformity_sent_at',
        'conformity_confirmed_at',
    ];

    protected $casts = [
        'work_date' => 'date',
        'conformity_sent_at' => 'datetime',
        'conformity_confirmed_at' => 'datetime',
    ];

    public static function allStatusOptions(): array
    {
        return [
            'Pipeline' => self::PIPELINE_STATUSES,
            'Otros' => self::OTHER_STATUSES,
        ];
    }

    /**
     * Cuando una Orden de Trabajo pasa a "completado" (a mano desde el admin,
     * o vía confirmConformity()), el Customer asociado pasa a "activo".
     */
    protected static function booted(): void
    {
        static::saved(function (WorkOrder $workOrder) {
            if ($workOrder->wasChanged('status') && $workOrder->status === 'completado') {
                $workOrder->serviceOrder?->customer?->update(['status' => 'activo']);
            }
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('before_photos');
        $this->addMediaCollection('after_photos');
    }

    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    public function getWorkOrderNumberAttribute(): string
    {
        return 'OT00'.$this->serviceOrder->documentNumberBase();
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(WorkOrderChecklistItem::class)->orderBy('order')->orderBy('id');
    }

    public function workers(): BelongsToMany
    {
        return $this->belongsToMany(Worker::class);
    }

    public function generateConformityToken(): string
    {
        $token = $this->conformity_token ?: md5($this->id.time().rand(1000, 9999));

        $this->update([
            'conformity_token' => $token,
            'conformity_sent_at' => now(),
        ]);

        return $token;
    }

    public function confirmConformity(): void
    {
        if ($this->conformity_confirmed_at !== null) {
            return;
        }

        $this->update([
            'conformity_confirmed_at' => now(),
            'status' => 'completado',
        ]);

        $order = $this->serviceOrder;

        $order->update([
            'status' => $order->status === 'trabajo_programado' ? 'conformidad_cliente' : $order->status,
        ]);
    }
}
