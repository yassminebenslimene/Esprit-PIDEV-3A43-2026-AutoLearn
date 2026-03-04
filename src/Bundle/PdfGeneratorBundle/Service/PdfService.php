<?php

namespace App\Bundle\PdfGeneratorBundle\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

/**
 * Service de génération de PDF
 * 
 * Ce service encapsule la logique de génération de PDF
 * avec Dompdf et Twig
 */
class PdfService
{
    private Environment $twig;
    /** @var array<string, mixed> */
    private array $defaultOptions;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(Environment $twig, array $options = [])
    {
        $this->twig = $twig;
        $this->defaultOptions = array_merge([
            'defaultFont' => 'DejaVu Sans',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isFontSubsettingEnabled' => true,
            'chroot' => realpath(__DIR__ . '/../../../../public'),
        ], $options);
    }

    /**
     * Génère un PDF depuis un template Twig
     * 
     * @param string $template Chemin du template Twig
     * @param array<string, mixed> $data Données à passer au template
     * @param array<string, mixed> $options Options Dompdf supplémentaires
     * @return Dompdf Instance Dompdf avec le PDF généré
     */
    public function generateFromTemplate(string $template, array $data = [], array $options = []): Dompdf
    {
        // Configuration Dompdf
        $pdfOptions = new Options();
        
        // Appliquer les options par défaut
        foreach ($this->defaultOptions as $key => $value) {
            $pdfOptions->set($key, $value);
        }
        
        // Appliquer les options personnalisées
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
        $paperSize = $options['paperSize'] ?? 'A4';
        $orientation = $options['orientation'] ?? 'portrait';
        $dompdf->setPaper($paperSize, $orientation);

        // Générer le PDF
        $dompdf->render();

        return $dompdf;
    }

    /**
     * Génère un PDF et retourne le contenu en string
     * 
     * @param string $template Chemin du template Twig
     * @param array<string, mixed> $data Données à passer au template
     * @param array<string, mixed> $options Options Dompdf
     * @return string Contenu du PDF
     */
    public function generatePdfString(string $template, array $data = [], array $options = []): string
    {
        $dompdf = $this->generateFromTemplate($template, $data, $options);
        return $dompdf->output();
    }

    /**
     * Génère un PDF pour un chapitre
     * 
     * @param object $chapitre Entité Chapitre
     * @return Dompdf
     */
    public function generateChapterPdf($chapitre): Dompdf
    {
        return $this->generateFromTemplate('pdf/chapitre.html.twig', [
            'chapitre' => $chapitre
        ]);
    }

    /**
     * Génère un certificat PDF
     * 
     * @param array<string, mixed> $data Données du certificat
     * @return string Contenu du PDF
     */
    public function generateCertificate(array $data): string
    {
        return $this->generatePdfString('pdf/certificate.html.twig', $data, [
            'orientation' => 'landscape'
        ]);
    }

    /**
     * Génère un badge PDF
     * 
     * @param array<string, mixed> $data Données du badge
     * @return string Contenu du PDF
     */
    public function generateBadge(array $data): string
    {
        return $this->generatePdfString('pdf/badge.html.twig', $data, [
            'paperSize' => [0, 0, 283.46, 396.85] // 10cm x 14cm
        ]);
    }
}
