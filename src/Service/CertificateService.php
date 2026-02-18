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
            padding: 30px;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
        }
        .certificate {
            background: white;
            padding: 40px 50px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            position: relative;
            height: 750px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .certificate::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 15px;
            right: 15px;
            bottom: 15px;
            border: 3px solid #667eea;
            border-radius: 15px;
        }
        .header {
            margin-bottom: 15px;
        }
        .logo {
            font-size: 36px;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 5px;
        }
        .title {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            margin: 15px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .subtitle {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }
        .name {
            font-size: 40px;
            font-weight: bold;
            color: #333;
            margin: 15px 0;
            border-bottom: 3px solid #667eea;
            display: inline-block;
            padding-bottom: 8px;
        }
        .description {
            font-size: 15px;
            color: #666;
            line-height: 1.5;
            margin: 15px auto;
        }
        .event-details {
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .event-name {
            font-size: 26px;
            font-weight: bold;
            color: #764ba2;
            margin-bottom: 8px;
        }
        .event-type {
            font-size: 18px;
            color: #667eea;
            margin-bottom: 5px;
        }
        .footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
        }
        .signature {
            text-align: center;
        }
        .signature-line {
            width: 180px;
            border-top: 2px solid #333;
            margin: 0 auto 8px auto;
        }
        .signature-title {
            font-size: 13px;
            color: #666;
            font-weight: bold;
        }
        .date {
            font-size: 15px;
            color: #666;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div>
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
        </div>
        
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
