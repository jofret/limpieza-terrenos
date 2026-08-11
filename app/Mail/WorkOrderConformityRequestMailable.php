<?php

namespace App\Mail;

use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkOrderConformityRequestMailable extends Mailable
{
    use Queueable, SerializesModels;

    public WorkOrder $workOrder;

    public string $enlace;

    public function __construct(WorkOrder $workOrder, string $enlace)
    {
        $this->workOrder = $workOrder;
        $this->enlace = $enlace;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Confirmá la conformidad de tu trabajo - AltoParque',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.work-order-conformity-request',
        );
    }
}
