<?php
// src/Form/UserType.php
namespace App\Form;

use App\Entity\User;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class MemberType
 * @package App\Form
 */
class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['form'])
        {
            case 'create':
                $this->loginCreate($builder);
                break;
            default:
                break;
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => User::class, 'form' => ''));
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function loginCreate(FormBuilderInterface $builder)
    {
        $builder
            ->add('Login', TextType::class, array('label' => 'Login : '))
            ->add('Email', EmailType::class, array('label' => 'Email : ', 'mapped' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Créer'))
        ;
    }
}
