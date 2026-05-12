<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class message extends Mailable
{
    public $data;
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $data = $this->data;
        return $this->from('ajayioluwaseunebenezer@zohomail.com', 'Horsep2')
            ->subject($data['subject'])
            ->markdown($data['view'])
            ->with($data);
    }
}
