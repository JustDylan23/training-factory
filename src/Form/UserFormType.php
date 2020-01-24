<?php


namespace App\Form;


use App\Entity\Admin;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var User|null $user */
        $user = $options['data'];
        $isEdit = $user && $user->getId();

        $isAdmin = $options['isAdmin'];

        $builder->add('email', EmailType::class, [
            'disabled' => $isEdit && !$isAdmin
        ]);
        if (!$isEdit) {
            $builder
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'constraints' => [
                        new Length([
                            'min' => 6
                        ]),
                        new NotBlank()
                    ],
                    'invalid_message' => 'The password fields must match.',
                    'first_options' => ['label' => 'Password'],
                    'second_options' => ['label' => 'Repeat Password'],
                ]);
        }
        $builder
            ->add('firstname')
            ->add('surnamePrepositions', TextType::class, [
                'required' => false,
            ])
            ->add('surname')
            ->add('gender', ChoiceType::class, [
                'placeholder' => 'Choose a gender',
                'choices' => [
                    'Male' => true,
                    'Female' => false,
                    'Other' => null,
                    'Prefer not to say' => null,
                ],
            ])
            ->add('birthdate', BirthdayType::class, [
                'placeholder' => [
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                ],
            ]);


        if ($isAdmin) {
            $builder->add('disabled', CheckboxType::class, [
                'help' => 'When checked, you disabled the users account, to enable it uncheck it.'
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'isAdmin' => false,
            'data' => null,
        ]);
    }
}