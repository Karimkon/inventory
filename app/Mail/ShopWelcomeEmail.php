<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Shop;
use App\Models\User;

class ShopWelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $shop;
    public $user;
    public $password;
    public $pin; 

    /**
     * Create a new message instance.
     */
    public function __construct(Shop $shop, User $user, $password, $pin = null) // ADD $pin PARAMETER
    {
        $this->shop = $shop;
        $this->user = $user;
        $this->password = $password;
        $this->pin = $pin; 
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Welcome to Redvers Shopflow - Your Shop is Ready!')
                    ->view('emails.shop-welcome')
                    ->with([
                        'shop' => $this->shop,
                        'user' => $this->user,
                        'password' => $this->password,
                        'pin' => $this->pin 
                    ]);
    }
}