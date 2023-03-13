<?php /** @noinspection ALL */

namespace App\Security;

use JetBrains\PhpStorm\ArrayShape;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class D3WebhookAuthenticator extends AbstractGuardAuthenticator
{
    public function __construct(
        private LoggerInterface $logger
    )
    { }


    public function supports(Request $request): bool
    {
        if (
            $request->headers->has('x-dv-tenant-id')
            && $request->headers->has('x-dv-baseuri')
            && $request->headers->has('x-dv-api-key')
            && $request->headers->has('x-dv-request-count')
        ) {
            return true;
        }

        return false;
    }

    #[ArrayShape([
        'd3TenantId' => "null|string",
        'd3BaseUri' => "null|string",
        'd3ApiKey' => "null|string",
        'd3UserId' => "null|string"
    ])] public function getCredentials(
        Request $request
    ): array {
        return [
            'd3TenantId' => $request->headers->get('x-dv-tenant-id'),
            'd3BaseUri' => $request->headers->get('x-dv-baseuri'),
            'd3ApiKey' => $request->headers->get('x-dv-api-key'),
            'd3UserId' => $request->headers->get('x-dv-exec-user-id') ?: '',
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?User
    {
        $tenant = $credentials['d3TenantId'];

        if (null === $tenant) {
            return null;
        }
        $user = new User();
        $user->createDummyUser($credentials['d3BaseUri'], $tenant);
        $user->setBearerToken($credentials['d3ApiKey']);

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        $tenant = $credentials['d3TenantId'];
        $baseuri = $credentials['d3BaseUri'];
        $apiKey = $credentials['d3ApiKey'];

        //TODO API Key gegen dvelop prüfen
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return null;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            // you might translate this message
            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
