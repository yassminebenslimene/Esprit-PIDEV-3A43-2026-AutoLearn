<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class BadgeService
{
    /**
     * Génère un badge PDF pour un participant
     */
    public function generateBadge(
        string $studentFirstName,
        string $studentLastName,
        string $teamName,
        string $eventName,
        \DateTimeInterface $eventDate
    ): string {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        
        $dompdf = new Dompdf($options);
        
        // Template HTML du badge
        $html = $this->getBadgeTemplate(
            $studentFirstName,
            $studentLastName,
            $teamName,
            $eventName,
            $eventDate
        );
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper([0, 0, 283.46, 396.85], 'portrait'); // 10cm x 14cm (une page)
        $dompdf->render();
        
        // Retourner le PDF en string
        return $dompdf->output();
    }
    
    /**
     * Template HTML professionnel pour le badge
     */
    private function getBadgeTemplate(
        string $firstName,
        string $lastName,
        string $teamName,
        string $eventName,
        \DateTimeInterface $eventDate
    ): string {
        $fullName = htmlspecialchars(strtoupper($firstName . ' ' . $lastName), ENT_QUOTES, 'UTF-8');
        $formattedDate = htmlspecialchars($eventDate->format('F d, Y'), ENT_QUOTES, 'UTF-8');
        $teamNameSafe = htmlspecialchars($teamName, ENT_QUOTES, 'UTF-8');
        $eventNameSafe = htmlspecialchars($eventName, ENT_QUOTES, 'UTF-8');
        
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page { margin: 0; }
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: white;
        }
        .badge {
            width: 10cm;
            height: 14cm;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 15px;
            box-sizing: border-box;
            position: relative;
            color: white;
        }
        .badge-header {
            text-align: center;
            padding: 12px 0;
            border-bottom: 3px solid white;
            margin-bottom: 15px;
        }
        .logo {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .badge-type {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .badge-content {
            background: white;
            color: #333;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin: 15px 0;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .participant-name {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
            margin: 15px 0;
            line-height: 1.2;
        }
        .team-name {
            font-size: 18px;
            color: #764ba2;
            font-weight: bold;
            margin: 12px 0;
            padding: 8px;
            background: #f0f0f0;
            border-radius: 8px;
        }
        .team-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .event-info {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #e0e0e0;
        }
        .event-name {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }
        .event-date {
            font-size: 13px;
            color: #666;
        }
        .badge-footer {
            position: absolute;
            bottom: 12px;
            left: 15px;
            right: 15px;
            text-align: center;
            font-size: 10px;
            color: white;
        }
    </style>
</head>
<body>
    <div class="badge">
        <div class="badge-header">
            <div class="logo">AUTOLEARN</div>
            <div class="badge-type">Event Participant</div>
        </div>
        
        <div class="badge-content">
            <div class="participant-name">{$fullName}</div>
            
            <div class="team-name">
                <div class="team-label">Team</div>
                {$teamNameSafe}
            </div>
            
            <div class="event-info">
                <div class="event-name">{$eventNameSafe}</div>
                <div class="event-date">{$formattedDate}</div>
            </div>
        </div>
        
        <div class="badge-footer">
            Please wear this badge during the event
        </div>
    </div>
</body>
</html>
HTML;
    }
}
