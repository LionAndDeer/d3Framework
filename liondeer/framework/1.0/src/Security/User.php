<?php

namespace App\Security;

use Liondeer\Framework\Model\Tenant;
use Liondeer\Framework\Transmitter\Helper\UserSignatureHelper;
use Symfony\Component\Security\Core\User\UserInterface;

/** @codeCoverageIgnore */
class User implements UserInterface
{
    // TODO: Abstract
    private string $tenantId;
    private array $roles = [];
    private string $id;
    private string $username;
    private string $firstName;
    private string $lastName;
    private string $title;
    private array $emails = [];
    private array $phoneNumbers = [];
    private array $groups = [];
    private array $photos = [];
    private ?string $bearerToken = null;
    private string $baseUri;
    private string $displayName;

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): User
    {
        $this->displayName = $displayName;

        return $this;
    }


    public function getBearerToken(): ?string
    {
        return $this->bearerToken;
    }

    public function setBearerToken(string $bearerToken): self
    {
        $this->bearerToken = $bearerToken;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        //return (['ROLE_USER']);

        return array_unique($this->roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return User
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return array
     */
    public function getEmails(): array
    {
        return $this->emails;
    }

    /**
     * @param array $emails
     *
     * @return User
     */
    public function setEmails(array $emails): self
    {
        $this->emails = $emails;

        return $this;
    }

    /**
     * @return array
     */
    public function getPhoneNumbers(): array
    {
        return $this->phoneNumbers;
    }

    /**
     * @param array $phoneNumbers
     *
     * @return User
     */
    public function setPhoneNumbers(array $phoneNumbers): self
    {
        $this->phoneNumbers = $phoneNumbers;

        return $this;
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @param array $groups
     *
     * @return User
     */
    public function setGroups(array $groups): self
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * @return array
     */
    public function getPhotos(): array
    {
        return $this->photos;
    }

    /**
     * @param array $photos
     *
     * @return User
     */
    public function setPhotos(array $photos): self
    {
        $this->photos = $photos;

        return $this;
    }

    public function createDummyUser(string $baseUri, string $tenantId): self
    {
        $this->id = '1';
        $this->username = 'brave.elephant';
        $this->firstName = 'Brave';
        $this->lastName = 'Elephant';
        $this->roles = ['ROLE_BRAVE_USER'];
        $this->baseUri = $baseUri;
        $this->tenantId = $tenantId;

        return $this;
    }

    public function getTenant(): Tenant
    {
        $tenant = new Tenant();
        $tenant
            ->setTenantId($this->getTenantId())
            ->setBaseUri($this->getBaseUri());

        return $tenant;
    }

    public function getTenantId(): ?string
    {
        return $this->tenantId;
    }

    public function setTenantId(string $tenantId): self
    {
        $this->tenantId = $tenantId;

        return $this;
    }

    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    public function setBaseUri(string $baseUri): self
    {
        $this->baseUri = $baseUri;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): ?string
    {
        // not needed for apps that do not check user passwords
        return null;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        // not needed for apps that do not check user passwords
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getSignature(): string
    {
        $userSignatureHelper = new UserSignatureHelper();

        return $userSignatureHelper->getSignature($this->getTenantId(), $this->getId());
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return User
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->id;
    }
}
