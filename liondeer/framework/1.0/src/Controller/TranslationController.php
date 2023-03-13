<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

class TranslationController extends AbstractController
{
    public function __construct()
    {
    }

    #[Route('/translation.js', name: 'translation')]
    public function index(Request $request): Response
    {
        $locale = $request->getLocale();
        $file = __DIR__ . '/../../translations/messages.' . $locale . '.yml';
        $parsed = Yaml::parse(file_get_contents($file));

        $translations = $this->renderView(
            'translation/translation.js.twig',
            [
                'json' => addslashes(json_encode($parsed)),
            ]
        );

        return new Response($translations, Response::HTTP_OK, ['content-type' => 'text/javascript']);
    }

}