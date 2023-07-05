<?php

namespace App\Security;

use App\Helper\DuoConfigHelper;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class SmartInvoiceWebhookAuthenticator extends AbstractGuardAuthenticator
{
    private string $requestBody;

    public function __construct(private DuoConfigHelper $helper)
    {
    }

    public function supports(Request $request): bool
    {
        return $request->headers->has('x-smart-invoice-signature');
    }

    #[ArrayShape([
        't' => 'string',
        'v1' => 'string',
        'tenantId' => 'string',
        'baseUri' => 'string'
    ])]
    public function getCredentials(Request $request): array
    {
        $header = $request->headers->get('x-smart-invoice-signature');
        $tenantId = $request->headers->get('x-dv-tenant-id');
        $baseUri = $request->headers->get('x-dv-baseuri');
        $timestamp = substr(explode(',', $header)[0], 2);
        $signature = substr(explode(',', $header)[1], 3);
        $this->requestBody = $request->getContent();

        return ['t' => $timestamp, 'v1' => $signature, 'tenantId' => $tenantId, 'baseUri' => $baseUri];
    }

    public function getUser($credentials, UserProviderInterface $userProvider): User
    {
        $requestBody = json_decode($this->requestBody, true);
        $user = new User();
        $user->createDummyUser($credentials['baseUri'], $credentials['tenantId']);
        if (empty($user->getTenantId())) {
            if (array_key_exists('base_uri', $requestBody['tenant'])) {
                $user->createDummyUser($requestBody['tenant']['base_uri'], $requestBody['tenant']['id']);
            }
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        //TODO: Secret aus DB holen!!!!!!!!!!!!!einself
        $shouldFinishAt = new DateTime('+5 sec');
        /** @var User $user */
        $secret = $this->helper->getSmartInvoiceSecret($user->getTenantId());
        $minimumTime = new DateTime('5 minutes ago');

        if ($credentials['t'] < $minimumTime->getTimestamp()) {
            time_sleep_until($shouldFinishAt->getTimestamp());

            return false;
        }

        $concatenated = $credentials['t'] . '.' . $this->requestBody;
        $hashed = hash_hmac('sha256', $concatenated, $secret);

        time_sleep_until($shouldFinishAt->getTimestamp());

        return hash_equals($hashed, $credentials['v1']);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return null;
    }

    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        $data = [
            // you might translate this message
            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }
}
