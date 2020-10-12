<?php
// src/Form/UserType.php
namespace App\Form;

use App\Entity\User;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
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
            case 'club_manager_add':
                $this->clubManagerAdd($builder);
                break;
            case 'club_manager_delete':
                $this->clubManagerDelete($builder);
                break;
            case 'change_login':
                $this->changeLogin($builder);
                break;
            case 'change_password':
                $this->changePassword($builder);
                break;
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
            ->add('Password', PasswordType::class, array('label' => 'Nouveau mot de passe : ', 'mapped' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Créer'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function changeLogin(FormBuilderInterface $builder)
    {
        $builder
            ->add('Login', TextType::class, array('label' => 'Login : '))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function changePassword(FormBuilderInterface $builder)
    {
        $builder
            ->add('Password1', PasswordType::class, array('label' => 'Nouveau mot de passe : ', 'mapped' => false))
            ->add('Password2', PasswordType::class, array('label' => 'Vérification         : ', 'mapped' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function clubManagerAdd(FormBuilderInterface $builder)
    {
        $builder

            ->add('Login', TextType::class, array('label' => 'Login : '))
            ->add('UserMember', IntegerType::class, array('label' => 'N° de licence : ', 'mapped' => false, 'required' => false))
            ->add('UserFirstname', TextType::class, array('label' => 'Prénom : ', 'required' => false))
            ->add('UserRealName', TextType::class, array('label' => 'Nom : ', 'required' => false))
            ->add('Password', PasswordType::class, array('label' => 'Mot de passe : ', 'mapped' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Créer'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function clubManagerDelete(FormBuilderInterface $builder)
    {
        $builder

            ->add('Login', TextType::class, array('label' => 'Login : ', 'disabled' => true))
            ->add('Submit', SubmitType::class, array('label' => 'Supprimer'))
        ;
    }
}
