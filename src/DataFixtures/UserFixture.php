<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Instructor;
use App\Entity\Member;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends BaseFixtures
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct();
        $this->passwordEncoder = $passwordEncoder;
    }

    public function loadData(ObjectManager $manager)
    {
        $this->createMany(Member::class, 20, function (Member $member, int $count) {
            $member
                ->setStreet($this->faker->streetName)
                ->setCity($this->faker->city)
                ->setPostalCode($this->faker->postcode);

            $this->setDetails($member)
                ->setEmail("member$count@mail.com");
        });
        $this->createMany(Instructor::class, 20, function (Instructor $instructor, int $count) {
            $instructor
                ->setHiringDate($this->faker->dateTimeBetween($startDate = '-10 years', $endDate = 'now'))
                ->setSalary($this->faker->numberBetween(1000, 3000));

            $this->setDetails($instructor)
                ->setEmail("trainer$count@mail.com");
        });
        $this->createMany(Admin::class, 20, function (Admin $admin, int $count) {
            $this->setDetails($admin)
                ->setEmail("admin$count@mail.com");
        });
        $manager->flush();
    }

    public function setDetails(User &$user): User
    {
        return $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'login'
        ))
            ->setBirthdate($this->faker->dateTimeBetween($startDate = '-40 years', $endDate = '-15 years'))
            ->setFirstname($this->faker->firstName)
            ->setSurnamePrepositions($this->faker->randomElement(['van', 'van der', 'de', 'van den', null, null, null]))
            ->setSurname($this->faker->lastName)
            ->setGender($this->faker->boolean);
    }
}
