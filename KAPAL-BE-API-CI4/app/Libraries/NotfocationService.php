<?php namespace App\Libraries;

use Config\Email;

class NotificationService
{
    protected $email;

    public function __construct()
    {
        $this->email = \Config\Services::email();
        $config = new Email();
        $this->email->initialize($config);
    }

    public function sendBookingConfirmation($booking, $user)
    {
        $data = [
            'booking' => $booking,
            'user' => $user
        ];

        $this->email->setTo($user['email']);
        $this->email->setSubject('Booking Confirmation - ' . $booking['booking_code']);
        $this->email->setMessage(view('emails/booking_confirmation', $data));
        
        return $this->email->send();
    }

    public function sendPaymentConfirmation($payment, $user)
    {
        $data = [
            'payment' => $payment,
            'user' => $user
        ];

        $this->email->setTo($user['email']);
        $this->email->setSubject('Payment Received - ' . $payment['payment_id']);
        $this->email->setMessage(view('emails/payment_confirmation', $data));
        
        return $this->email->send();
    }
}