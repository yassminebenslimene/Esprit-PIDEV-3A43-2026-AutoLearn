<?php

namespace App\Service;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;

class QrCodeService
{
    /**
     * Génère un QR code en base64 pour l'email
     */
    public function generateQrCodeBase64(string $data): string
    {
        $qrCode = new \Endroid\QrCode\QrCode(
            data: $data,
            encoding: new \Endroid\QrCode\Encoding\Encoding('UTF-8'),
            size: 300,
            margin: 10
        );

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return base64_encode($result->getString());
    }

    /**
     * Génère les données pour le QR code de participation
     */
    public function generateParticipationData(
        int $participationId,
        string $studentName,
        string $eventName,
        \DateTimeInterface $eventDate
    ): string {
        $data = [
            'type' => 'event_participation',
            'participation_id' => $participationId,
            'student' => $studentName,
            'event' => $eventName,
            'date' => $eventDate->format('Y-m-d H:i'),
            'verified' => hash('sha256', $participationId . $eventName . $studentName)
        ];

        return json_encode($data);
    }
}
