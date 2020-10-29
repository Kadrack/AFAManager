<?php
// src/Form/SecretariatType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AccountingType
 * @package App\Form
 */
class AccountingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['form'])
        {
            case 'search_members':
                $this->searchMembers($builder);
                break;
            default:
                null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => '', 'form' => ''));
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function searchMembers(FormBuilderInterface $builder)
    {
        $builder
            ->add('Search', TextType::class, array('label' => 'N° licence, Nom', 'mapped' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Rechercher'))
        ;
    }
}
