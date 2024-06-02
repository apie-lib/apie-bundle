<?php
namespace Apie\Tests\ApieBundle\BoundedContext\Entities;

use Apie\Core\Attributes\Internal;
use Apie\Core\Entities\EntityInterface;
use Apie\CountryAndPhoneNumber\BritishPhoneNumber;
use Apie\CountryAndPhoneNumber\DutchPhoneNumber;
use Apie\Fixtures\ValueObjects\EncryptedPassword;
use Apie\Fixtures\ValueObjects\Password as StrongPassword;
use Apie\Tests\ApieBundle\BoundedContext\ValueObjects\UserIdentifier;

class User implements EntityInterface
{
    private UserIdentifier $id;
    private EncryptedPassword $encryptedPassword;

    public function __construct(StrongPassword $password, private DutchPhoneNumber|BritishPhoneNumber $phoneNumber, UserIdentifier $id = null)
    {
        $this->id = $id ?? UserIdentifier::createRandom();
        $this->encryptedPassword = EncryptedPassword::fromUnencryptedPassword($password);
    }

    #[Internal()]
    public function verifyAuthentication(string $password)
    {
        return $this->encryptedPassword->verifyUnencryptedPassword($password);
    }

    public function getId(): UserIdentifier
    {
        return $this->id;
    }
}
