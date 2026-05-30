<?php

namespace App\Mail;

use App\Models\Rental;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentalRekapMail extends Mailable
{
    use Queueable, SerializesModels;

    public Rental $rental;

    /**
     * @param Rental $rental  Harus sudah di-load relasi items.equipment
     */
    public function __construct(Rental $rental)
    {
        $this->rental = $rental;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Rekap Sewa LensHub – ' . $this->rental->kode_sewa,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.rental-rekap',
        );
    }
}
