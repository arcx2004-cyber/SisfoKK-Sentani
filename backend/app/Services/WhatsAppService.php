<?php

namespace App\Services;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a WhatsApp message to a specific number.
     * 
     * @param string $number Format: 0812xxx or 62812xxx
     * @param string $message
     * @return bool
     */
    public static function sendMessage(string $number, string $message): bool
    {
        // normalize number
        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        }

        // Mock implementation
        Log::info("WhatsApp Sent to {$number}: {$message}");
        
        // TODO: Integrate with Fonnte or Twilio API here
        // Http::post('https://api.fonnte.com/send', [...]);

        return true;
    }

    public static function sendBillNotification($student, $amount)
    {
        $message = "Halo {$student->nama_lengkap}, tagihan SPP Anda sebesar Rp " . number_format($amount, 0, ',', '.') . " telah terbit. Harap segera melakukan pembayaran.";
        return self::sendMessage($student->no_telepon_ortu ?? $student->no_telepon, $message);
    }
}
