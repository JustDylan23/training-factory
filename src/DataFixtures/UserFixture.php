<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $member = new User();
        $this->setDetails($member)
            ->setEmail('member@mail.com')
            ->setRoles(['ROLE_MEMBER']);
        $manager->persist($member);
        $trainer = new User();
        $this->setDetails($trainer)
            ->setEmail('trainer@mail.com')
            ->setRoles(['ROLE_TRAINER']);
        $manager->persist($trainer);
        $admin = new User();
        $this->setDetails($admin)
            ->setEmail('admin@mail.com')
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);
        $manager->flush();
    }

    public function setDetails(User &$user): User
    {
        return $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'login'
        ))
            ->setDateOfBirth(new DateTime())
            ->setFirstname("Dylan")
            ->setSurnamePrepositions("van")
            ->setSurname("Hagen")
            ->setGender(true);
    }
}
