<?php
// src/Form/MailType.php
namespace App\Form;

use App\Entity\Mail;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class EmailType
 * @package App\Form
 */
class MailType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('MailPriority', CheckboxType::class, array('label' => 'Prioritaire', 'required' => false))
            ->add('MailTo', TextType::class, array('label' => 'A : '))
            ->add('MailCc', TextType::class, array('label' => 'CC : ', 'required' => false))
            ->add('MailBcc', TextType::class, array('label' => 'CCI : ', 'required' => false))
            ->add('MailTitle', TextType::class, array('label' => 'Titre : '))
            ->add('MailBody', TextareaType::class, array('label' => 'Contenu :', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Envoyer'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => Mail::class));
    }
}
