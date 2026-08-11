<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasPendingAttentionBadge;
use App\Filament\Concerns\OpensWhatsAppInNewTab;
use App\Filament\Concerns\SendsRelevamientoToRelevador;
use App\Filament\Resources\RelevamientoResource\Pages;
use App\Models\Relevamiento;
use App\Models\ServiceOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class RelevamientoResource extends Resource
{
    use HasPendingAttentionBadge, OpensWhatsAppInNewTab, SendsRelevamientoToRelevador;

    protected static ?string $model = Relevamiento::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Relevamientos';

    protected static ?string $navigationLabel = 'Relevamientos';

    protected static ?string $modelLabel = 'relevamiento';

    protected static ?string $pluralModelLabel = 'relevamientos';

    protected static ?int $navigationSort = 1;

    public static function normalizeCategoryData(array $data): array
    {
        if (($data['category_id'] ?? null) === 'otro') {
            $data['category_id'] = null;
        } else {
            $data['category_other'] = null;
        }

        return $data;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('property_id')
                    ->label('Propiedad')
                    ->relationship('property', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->customer?->name.' — '.$record->display_label)
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->label('Tipo de servicio')
                    ->options(fn () => \App\Models\Category::query()->orderBy('order')->pluck('name', 'id')->put('otro', 'Otro'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required(),
                Forms\Components\TextInput::make('category_other')
                    ->label('Especificar tipo de servicio')
                    ->maxLength(255)
                    ->visible(fn (callable $get): bool => $get('category_id') === 'otro')
                    ->required(fn (callable $get): bool => $get('category_id') === 'otro'),
                Forms\Components\Select::make('assigned_to')
                    ->label('Relevador asignado')
                    ->relationship('relevador', 'name', fn ($query) => $query->where('role', 'relevador'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\DatePicker::make('scheduled_date')
                    ->label('Fecha programada')
                    ->required(),
                Forms\Components\TimePicker::make('scheduled_time_from')
                    ->label('Hora desde')
                    ->seconds(false)
                    ->required(),
                Forms\Components\TimePicker::make('scheduled_time_to')
                    ->label('Hora hasta')
                    ->seconds(false)
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'enviado_a_relevador' => 'Enviado a relevador',
                    ])
                    ->default('pendiente')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->label('Notas')
                    ->columnSpanFull(),
                Forms\Components\SpatieMediaLibraryFileUpload::make('photos')
                    ->collection('photos')
                    ->label('Fotos del relevamiento')
                    ->image()
                    ->multiple()
                    ->columnSpanFull()
                    ->visible(fn (string $operation): bool => $operation !== 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('serviceOrder'))
            ->columns([
                Tables\Columns\TextColumn::make('property.customer.name')
                    ->label('Cliente')
                    ->default('—')
                    ->searchable(),
                Tables\Columns\TextColumn::make('property.display_label')
                    ->label('Propiedad'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state, Relevamiento $record): string => match (true) {
                        static::readyToQuoteUrl($record) !== null => 'success',
                        $record->submitted_at !== null => 'gray',
                        $state === 'pendiente' => 'gray',
                        $state === 'enviado_a_relevador' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state, Relevamiento $record): string => match (true) {
                        static::readyToQuoteUrl($record) !== null => 'Relevado y listo para presupuestar',
                        $record->submitted_at !== null => 'Relevado',
                        $state === 'pendiente' => 'Pendiente',
                        $state === 'enviado_a_relevador' => 'Enviado a relevador',
                        default => $state,
                    })
                    ->url(fn (Relevamiento $record): ?string => static::readyToQuoteUrl($record))
                    ->openUrlInNewTab(false),
                Tables\Columns\TextColumn::make('reopen_requested_at')
                    ->label('Reapertura')
                    ->badge()
                    ->color('warning')
                    ->getStateUsing(fn (Relevamiento $record): ?string => $record->reopen_requested_at ? 'Solicitada' : null),
                Tables\Columns\TextColumn::make('service_type_label')
                    ->label('Tipo de servicio')
                    ->default('—'),
                Tables\Columns\TextColumn::make('scheduled_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('scheduled_time_from')
                    ->label('Horario')
                    ->formatStateUsing(fn ($state, $record) => $state
                        ? Carbon::parse($state)->format('H:i').($record->scheduled_time_to ? ' - '.Carbon::parse($record->scheduled_time_to)->format('H:i') : '')
                        : '—'),
                Tables\Columns\IconColumn::make('submitted_at')
                    ->label('Completado por relevador')
                    ->boolean()
                    ->getStateUsing(fn (Relevamiento $record): bool => $record->submitted_at !== null),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'enviado_a_relevador' => 'Enviado a relevador',
                    ]),
                Tables\Filters\SelectFilter::make('assigned_to')
                    ->label('Relevador')
                    ->relationship('relevador', 'name', fn ($query) => $query->where('role', 'relevador')),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Tipo de servicio')
                    ->relationship('category', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('enviar')
                    ->label('Enviar a relevador')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn (Relevamiento $record): bool => $record->status === 'pendiente')
                    ->requiresConfirmation()
                    ->modalHeading('Enviar relevamiento a relevador')
                    ->modalDescription('Revisá que la propiedad, el relevador asignado y la fecha/horario estén correctos antes de confirmar. Al enviarlo, se abre WhatsApp con el aviso listo para el relevador.')
                    ->modalSubmitActionLabel('Confirmar envío')
                    ->modalSubmitAction(fn ($action) => $action->extraAttributes(static::whatsAppTriggerAttributes()))
                    ->action(fn (Relevamiento $record, $livewire) => static::sendRelevamientoToRelevador($record, $livewire)),
                Tables\Actions\Action::make('approve_reopen')
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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->modalDescription(fn (Collection $records): ?string => static::deleteWarningForRecords($records)),
                ]),
            ])
            ->defaultSort('scheduled_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRelevamientos::route('/'),
            'create' => Pages\CreateRelevamiento::route('/create'),
            'edit' => Pages\EditRelevamiento::route('/{record}/edit'),
        ];
    }

    protected static function readyToQuoteUrl(Relevamiento $record): ?string
    {
        $serviceOrder = $record->serviceOrder;

        $quotablePipelineStatuses = ['visita_realizada', 'presupuestado_enviado'];

        if (! $serviceOrder || ! $serviceOrder->canReviewAndQuote() || ! in_array($serviceOrder->status, $quotablePipelineStatuses, true)) {
            return null;
        }

        return ServiceOrderResource::getUrl('review-and-quote', ['record' => $serviceOrder]);
    }

    protected static function pendingAttentionCount(): int
    {
        return static::getModel()::whereNotNull('reopen_requested_at')->count();
    }

    protected static function pendingAttentionTooltip(): ?string
    {
        return 'Relevamientos con reapertura solicitada, pendiente de aprobar';
    }

    protected static function pendingAttentionColor(): string
    {
        return 'danger';
    }

    public static function deleteWarning(Relevamiento $record): ?string
    {
        return static::deleteWarningForRecords(collect([$record]));
    }

    public static function deleteWarningForRecords(Collection $records): ?string
    {
        $relevamientoIds = $records->pluck('id');
        $orders = ServiceOrder::whereIn('relevamiento_id', $relevamientoIds)->get();

        if ($orders->isEmpty()) {
            return null;
        }

        $sujeto = $records->count() === 1 ? 'Este relevamiento tiene' : 'Los relevamientos seleccionados tienen';
        $ordenPalabra = $orders->count() === 1 ? 'la orden va' : 'las órdenes van';
        $noSeBorra = $orders->count() === 1 ? 'no se borra' : 'no se borran';

        $mensaje = "{$sujeto} {$orders->count()} orden(es) de servicio vinculada(s). "
            .'Sus ítems de trabajo y fotos se van a borrar definitivamente, '
            ."y {$ordenPalabra} a quedar sin relevamiento vinculado ({$noSeBorra}).";

        $acceptedCount = $orders->whereNotNull('budget_accepted_at')->count();
        if ($acceptedCount > 0) {
            $mensaje .= " ⚠️ {$acceptedCount} de esas órdenes ya tiene(n) un presupuesto ACEPTADO por el cliente.";
        }

        return $mensaje;
    }
}
