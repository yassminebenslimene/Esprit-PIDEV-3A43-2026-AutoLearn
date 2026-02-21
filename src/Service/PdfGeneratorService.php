<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

class PdfGeneratorService
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Génère un PDF depuis un template Twig
     * 
     * @param string $template Le chemin du template Twig
     * @param array $data Les données à passer au template
     * @param array $options Options supplémentaires pour Dompdf
     * @return Dompdf
     */
    public function generatePdf(string $template, array $data = [], array $options = []): Dompdf
    {
        // Configuration Dompdf
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'DejaVu Sans');
        $pdfOptions->set('isRemoteEnabled', true);
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $pdfOptions->set('isFontSubsettingEnabled', true);
        
        // Fusionner avec les options personnalisées
        foreach ($options as $key => $value) {
            $pdfOptions->set($key, $value);
        }

        // Créer l'instance Dompdf
        $dompdf = new Dompdf($pdfOptions);

        // Rendre le template Twig en HTML
        $html = $this->twig->render($template, $data);

        // Charger le HTML dans Dompdf
        $dompdf->loadHtml($html);

        // Définir la taille du papier et l'orientation
        $dompdf->setPaper('A4', 'portrait');

        // Générer le PDF
        $dompdf->render();

        return $dompdf;
    }

    /**
     * Génère un PDF pour un chapitre
     * 
     * @param object $chapitre L'entité Chapitre
     * @return Dompdf
     */
    public function generateChapterPdf($chapitre): Dompdf
    {
        return $this->generatePdf('pdf/chapitre.html.twig', [
            'chapitre' => $chapitre
        ]);
    }
}
