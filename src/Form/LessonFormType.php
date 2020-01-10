<?php


namespace App\Form;


use App\Entity\Lesson;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;

class LessonFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Lesson|null $lesson */
        $lesson = $options['data'] ?? null;
        $isEdit = $lesson && $lesson->getId();

        $maxPeopleConstraints = [];

        if (!$isEdit) {
            $maxPeopleConstraints[] = new GreaterThan([
                'value' => new \DateTime()
            ]);
        }

        $builder
            ->add('training')
            ->add('location', ChoiceType::class, [
                'choices' => [
                    'The Hague' => 'The Hague',
                    'Amsterdam' => 'Amsterdam',
                    'Rotterdam' => 'Rotterdam',
                    'Groningen' => 'Groningen',
                ]
            ])
            ->add('maxPeople', IntegerType::class, [
                'label' => 'Maximum amount of participants',
                'attr' => [
                    'placeholder' => 'amount'
                ]
            ])
            ->add('time', DateTimeType::class, [
                'widget' => 'single_text',
                'constraints' => $maxPeopleConstraints
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'data_class' => Lesson::class
        ]);
    }
}