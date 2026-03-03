<?php

namespace App\Bundle\PdfGeneratorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Bundle de génération de PDF avec Dompdf
 * 
 * Fonctionnalités :
 * - Génération de PDF depuis templates Twig
 * - Support du logo AutoLearn
 * - Configuration flexible
 * - Service réutilisable
 */
class PdfGeneratorBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__, 2);
    }
}
