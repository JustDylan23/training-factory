<?php


namespace App\Form;


use App\Entity\Training;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrainingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Naam*',
                'help' => 'Dit is de naam van de training',
                'empty_data' => ''
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Tijdsduur*',
                'help' => 'De tijdsduur word opgeslagen in minuten',
                'attr' => [
                    'placeholder' => 'minuten'
                ]
            ])
            ->add('costs', MoneyType::class, [
                'label' => 'Prijs*',
                'attr' => [
                    'placeholder' => '00,00'
                ]
            ])
            ->add('img')
            ->add('description', TextareaType::class, [
                'label' => 'Beschrijving',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'data_class' => Training::class
        ]);
    }
}