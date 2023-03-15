<?php

namespace App\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/%d3_app_name%')]
class UpserviceController extends AbstractController
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    #[Route('/upservice', name: 'upservice')]
    public function index(): Response
    {
        return $this->render(
            'upservice/index.html.twig',
            [
                'services' => [
                    'HTTPS' => true,
                    'Database' => $this->checkDatabase(),
                    'API' => false,
                ],
                'xheaders' => $this->getXHeaders(),
            ]
        );
    }

    private function checkDatabase(): bool
    {
        //TODO: Check Database Connection
        return true;
    }

    private function getXHeaders(): array
    {
        $xheaders = [];
        if ($this->getParameter('app_env') == 'dev') {
            // @codeCoverageIgnoreStart
            $tenant = 'XXX'; //TODO Use your own
            $baseuri = 'https://yours.d-velop.cloud'; //TODO Use your own
            $private = base64_decode($this->getParameter("d3_app_secret"));

            $signature = base64_encode(hash_hmac('sha256', $baseuri . $tenant, $private, true));

            $xheaders['tenant'] = $tenant;
            $xheaders['baseuri'] = $baseuri;
            $xheaders['signature'] = $signature;
            // @codeCoverageIgnoreEnd
        }

        return $xheaders;
    }
}