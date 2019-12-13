<?php

namespace App\DataFixtures;

use App\Entity\User;
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
        $member
            ->setEmail('member@mail.com')
            ->setRoles(['ROLE_MEMBER'])
            ->setPassword($this->passwordEncoder->encodePassword(
                $member,
                'login'
            ));
        $manager->persist($member);

        $trainer = new User();
        $trainer
            ->setEmail('trainer@mail.com')
            ->setRoles(['ROLE_TRAINER'])
            ->setPassword($this->passwordEncoder->encodePassword(
                $trainer,
                'login'
            ));
        $manager->persist($trainer);

        $admin = new User();
        $admin
            ->setEmail('admin@mail.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->passwordEncoder->encodePassword(
                $admin,
                'login'
            ));
        $manager->persist($admin);

        $manager->flush();
    }
}
