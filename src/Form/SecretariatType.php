<?php
// src/Form/SecretariatType.php
namespace App\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SecretariatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['form'])
        {
            case 'supporter_create':
                $this->supporterCreate($builder);
                break;
            case 'supporter_delete':
                $this->supporterDelete($builder);
                break;
            case 'supporter_update':
                $this->supporterUpdate($builder);
                break;
            default:
                null;
        }
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => '', 'form' => ''));
    }

    private function supporterCreate(FormBuilderInterface $builder)
    {
        $builder
            ->add('SecretariatSupporterName', TextType::class, array('label' => 'Nom : '))
            ->add('SecretariatSupporterAddress', TextType::class, array('label' => 'Adresse : '))
            ->add('SecretariatSupporterZip', IntegerType::class, array('label' => 'Code postal : '))
            ->add('SecretariatSupporterCity', TextType::class, array('label' => 'Localité : '))
            ->add('SecretariatSupporterComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter'))
        ;
    }

    private function supporterUpdate(FormBuilderInterface $builder)
    {
        $builder
            ->add('SecretariatSupporterName', TextType::class, array('label' => 'Nom : '))
            ->add('SecretariatSupporterAddress', TextType::class, array('label' => 'Adresse : '))
            ->add('SecretariatSupporterZip', IntegerType::class, array('label' => 'Code postal : '))
            ->add('SecretariatSupporterCity', TextType::class, array('label' => 'Localité : '))
            ->add('SecretariatSupporterComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    private function supporterDelete(FormBuilderInterface $builder)
    {
        $builder
            ->add('SecretariatSupporterName', TextType::class, array('label' => 'Nom : ', 'disabled' => true))
            ->add('SecretariatSupporterAddress', TextType::class, array('label' => 'Adresse : ', 'disabled' => true))
            ->add('SecretariatSupporterZip', IntegerType::class, array('label' => 'Code postal : ', 'disabled' => true))
            ->add('SecretariatSupporterCity', TextType::class, array('label' => 'Localité : ', 'disabled' => true))
            ->add('SecretariatSupporterComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false, 'disabled' => true))
            ->add('Submit', SubmitType::class, array('label' => 'Effacer'))
        ;
    }
}
