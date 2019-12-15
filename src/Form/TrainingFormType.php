<?php


namespace App\Form;


use App\Entity\Training;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrainingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'help' => 'This is the name of the training',
            ])
            ->add('duration', IntegerType::class, [
                'help' => 'The duration will be stored in minutes',
                'attr' => [
                    'placeholder' => 'minutes'
                ]
            ])
            ->add('costs', MoneyType::class, [
                'label' => 'Price',
                'help' => 'Leave this empty if its free',
                'attr' => [
                    'placeholder' => '00,00',
                ],
            ])
            ->add('img')
            ->add('description');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'data_class' => Training::class
        ]);
    }
}