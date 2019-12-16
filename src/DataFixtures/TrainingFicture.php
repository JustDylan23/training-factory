<?php

namespace App\DataFixtures;

use App\Entity\Training;
use Doctrine\Common\Persistence\ObjectManager;

class TrainingFicture extends BaseFixtures
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany('App\Entity\Training', 8, function (Training $entity, $count) {
            $array = ['Boxing', 'Karate', 'Running', 'Lifting', 'Swimming', 'Kickboxing', 'Springing', 'Football'];

            $entity
                ->setName($array[$count])
                ->setCosts(rand(0, 20))
                ->setDuration(rand(0, 30))
                ->setImg('img/sport-' . rand(0, 2) . '.png')
                ->setDescription(
                    'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris facilisis odio sed finibus tempus. Donec et lacinia leo. Integer tempus ex vel enim fringilla, eget ultricies nisi pretium. Proin consectetur nec ligula at cursus. Aliquam hendrerit massa risus, sit amet ullamcorper ante pellentesque at.'
                );
        });

        $manager->flush();
    }
}
