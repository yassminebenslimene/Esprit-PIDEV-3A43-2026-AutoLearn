<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Participation;

class CertificateService
{
    /**
     * Génère un certificat PDF pour un participant
     */
    public function generateCertificate(
        string $studentFirstName,
        string $studentLastName,
        string $eventName,
        string $eventType,
        \DateTimeInterface $eventDate
    ): string {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        
        // Template HTML du certificat
        $html = $this->getCertificateTemplate(
            $studentFirstName,
            $studentLastName,
            $eventName,
            $eventType,
            $eventDate
        );
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        // Retourner le PDF en string
        return $dompdf->output();
    }
    
    /**
     * Template HTML professionnel pour le certificat
     */
    private function getCertificateTemplate(
        string $firstName,
        string $lastName,
        string $eventName,
        string $eventType,
        \DateTimeInterface $eventDate
    ): string {
        $fullName = $firstName . ' ' . $lastName;
        $formattedDate = $eventDate->format('F d, Y');
        
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0; }
        body {
            margin: 0;
            padding: 60px;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
        }
        .certificate {
            background: white;
            padding: 80px 60px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            position: relative;
        }
        .certificate::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 3px solid #667eea;
            border-radius: 15px;
        }
        .header {
            margin-bottom: 40px;
        }
        .logo {
            font-size: 48px;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }
        .title {
            font-size: 42px;
            font-weight: bold;
            color: #667eea;
            margin: 30px 0;
            text-transform: uppercase;
            letter-spacing: 3px;
        }
        .subtitle {
            font-size: 20px;
            color: #666;
            margin-bottom: 40px;
        }
        .recipient {
            font-size: 18px;
            color: #666;
            margin-bottom: 15px;
        }
        .name {
            font-size: 48px;
            font-weight: bold;
            color: #333;
            margin: 20px 0 40px 0;
            border-bottom: 3px solid #667eea;
            display: inline-block;
            padding-bottom: 10px;
        }
        .description {
            font-size: 18px;
            color: #666;
            line-height: 1.8;
            margin: 30px auto;
            max-width: 700px;
        }
        .event-details {
            margin: 40px 0;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .event-name {
            font-size: 28px;
            font-weight: bold;
            color: #764ba2;
            margin-bottom: 15px;
        }
        .event-type {
            font-size: 20px;
            color: #667eea;
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 60px;
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
        }
        .signature {
            text-align: center;
        }
        .signature-line {
            width: 200px;
            border-top: 2px solid #333;
            margin: 0 auto 10px auto;
        }
        .signature-title {
            font-size: 14px;
            color: #666;
            font-weight: bold;
        }
        .date {
            font-size: 16px;
            color: #666;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="header">
            <div class="logo">AUTOLEARN</div>
        </div>
        
        <div class="title">Certificate of Participation</div>
        
        <div class="subtitle">This is to certify that</div>
        
        <div class="name">{$fullName}</div>
        
        <div class="description">
            has successfully participated in and completed
        </div>
        
        <div class="event-details">
            <div class="event-name">{$eventName}</div>
            <div class="event-type">{$eventType}</div>
        </div>
        
        <div class="description">
            Demonstrating commitment to continuous learning and professional development
            through active engagement and collaboration.
        </div>
        
        <div class="date">Date: {$formattedDate}</div>
        
        <div class="footer">
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-title">Event Coordinator</div>
            </div>
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-title">Platform Director</div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
