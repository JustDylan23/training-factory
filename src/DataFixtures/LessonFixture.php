<?php


namespace App\DataFixtures;


use App\Entity\Instructor;
use App\Entity\Lesson;
use App\Entity\Training;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LessonFixture extends BaseFixtures implements DependentFixtureInterface
{

    protected function loadData(ObjectManager $em)
    {
        $this->createMany(Lesson::class, 20, function (Lesson $lesson, int $count) {
            $training = $this->getReferenceByClass(Training::class, $count);
            if ($training instanceof Training) {
                $lesson->setTraining($training);
            }
            $instructor = $this->getReferenceByClass(Instructor::class, $count);
            if ($instructor instanceof Instructor) {
                $lesson->setInstructor($instructor);
            }
            $lesson->setMaxPeople(rand(3, 30));
            $lesson->setTime($this->faker->dateTimeBetween('now', '+30 days'));
            $lesson->setLocation($this->faker->randomElement(['The Hague', 'Amsterdam', 'Rotterdam', 'Groningen']));
        });

        $em->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [
            TrainingFixture::class,
        ];
    }
}