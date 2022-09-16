<?php
namespace Apie\Tests\ApieBundle\BoundedContext\Entities;

use Apie\Core\Attributes\Internal;
use Apie\Core\Entities\EntityInterface;
use Apie\CountryAndPhoneNumber\BritishPhoneNumber;
use Apie\CountryAndPhoneNumber\DutchPhoneNumber;
use Apie\Tests\ApieBundle\BoundedContext\ValueObjects\UserIdentifier;
use Apie\TextValueObjects\EncryptedPassword;
use Apie\TextValueObjects\StrongPassword;

class User implements EntityInterface
{
    private UserIdentifier $id;
    private EncryptedPassword $encryptedPassword;

    public function __construct(StrongPassword $password, private DutchPhoneNumber|BritishPhoneNumber $phoneNumber)
    {
        $this->id = UserIdentifier::createRandom();
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
