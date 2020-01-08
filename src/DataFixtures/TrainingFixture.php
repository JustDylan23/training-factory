<?php

namespace App\DataFixtures;

use App\Entity\Training;
use Doctrine\Common\Persistence\ObjectManager;

class TrainingFixture extends BaseFixtures
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany('App\Entity\Training', 100, function (Training $entity) {
            $array = ['Boxing', 'Karate', 'Running', 'Lifting', 'Swimming', 'Kickboxing', 'Sprinting', 'Football', 'Climbing', 'Racing'];
            $entity
                ->setName($array[rand(0, 9)])
                ->setCosts(rand(0, 20))
                ->setDuration(rand(0, 30))
                ->setImg('sport-' . rand(0, 2) . '.png')
                ->setDescription($this->faker->text);
        });

        $manager->flush();
    }
}
