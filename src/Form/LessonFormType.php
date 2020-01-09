<?php


namespace App\Form;


use App\Entity\Lesson;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
                    'placeholder' => 'minutes'
                ]
            ])
            ->add('time');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'data_class' => Lesson::class
        ]);
    }
}