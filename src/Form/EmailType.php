<?php
// src/Form/EmailType.php
namespace App\Form;

use App\Entity\Email;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('EmailTitle', TextType::class, array('label' => 'Titre : '))
            ->add('EmailBody', TextareaType::class, array('label' => ' '))
            ->add('Submit', SubmitType::class, array('label' => 'Envoyer'))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => Email::class));
    }
}
