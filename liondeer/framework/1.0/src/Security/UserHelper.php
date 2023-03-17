<?php

namespace App\Security;

use App\Manager\PermissionManager;
use Liondeer\Framework\D3\Model\UserResponse;
use Liondeer\Framework\D3\Proxy\IdentityProvider\UserProxy;
use Liondeer\Framework\Exception\LiondeerD3FrameworkException;
use Liondeer\Framework\Security\UserHelperInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class UserHelper implements UserHelperInterface
{

    private string $bearer;
    private array $credentials;
    private UserResponse $userResponse;

    public function __construct(
        private SerializerInterface $serializer,
        private PermissionManager $permissionManager,
        private UserProxy $identityProviderUserService
    ) {
    }

    /**
     * @throws ClientExceptionInterface
     * @throws LiondeerD3FrameworkException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getCurrentUser($bearerToken, $credentials): User|InterAppUser
    {
        $this->bearer = $bearerToken;
        $this->credentials = $credentials;
        $this->userResponse = $this->identityProviderUserService->validate($bearerToken, $credentials);

        if (array_search('Apps', $this->userResponse->getGroups())) {
            return $this->getInterAppuser();
        } else {
            return $this->getD3User();
        }
    }

    private function getInterAppuser(): InterAppUser
    {
        $interAppuser = new InterAppUser();
        $interAppuser
            ->setUsername($this->userResponse->getUserName())
            ->setGroups($this->userResponse->getGroups())
            ->setBearerToken($this->bearer)
            ->setBaseUri($this->credentials['d3BaseUri'])
            ->setTenantId($this->credentials['d3TenantId'])
            ->setId($this->userResponse->getId());

        return $interAppuser;
    }

    private function getD3User(): User
    {
        $user = new User();

        $user
            ->setId($this->userResponse->getId())
            ->setGroups($this->userResponse->getGroups())
            ->setRoles(
                $this->permissionManager->getUserPermissionsArray(
                    $this->credentials['d3TenantId'],
                    $this->userResponse->getGroups()
                )
            )
            ->setFirstName($this->userResponse->getName()->getGivenName())
            ->setLastName($this->userResponse->getName()->getFamilyName())
            ->setEmails($this->userResponse->getEmails())
            ->setUsername($this->userResponse->getUserName())
            ->setDisplayName($this->userResponse->getDisplayName())
            ->setBearerToken($this->bearer)
            ->setBaseUri($this->credentials['d3BaseUri'])
            ->setTenantId($this->credentials['d3TenantId'])
            ->setPhotos($this->userResponse->getPhotos());

        return $user;
    }
}
