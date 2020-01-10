<?php

namespace App\DataFixtures;

use App\Entity\Training;
use Doctrine\Common\Persistence\ObjectManager;

class TrainingFixture extends BaseFixtures
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(Training::class, 20, function (Training $entity, int $count) {
            $array = ['Boxing', 'Karate', 'Running', 'Lifting', 'Swimming', 'Kickboxing', 'Sprinting', 'Football', 'Climbing', 'Racing',
                'Archery', 'Basketball', 'Badminton', 'Volleyball', 'Football', 'Wrestling', 'Discus throwing', 'Golf', 'Table tennis', 'Darts'];
            $entity
                ->setName($array[$count])
                ->setCosts(rand(0, 20))
                ->setDuration(rand(0, 30))
                ->setImg('sport-' . rand(0, 2) . '.png')
                ->setDescription($this->faker->text);
        });

        $manager->flush();
    }
}
