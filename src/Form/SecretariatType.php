<?php
// src/Form/SecretariatType.php
namespace App\Form;

use App\Service\ListData;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

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
            case 'modification_validate':
                $this->modificationValidate($builder);
                break;
            case 'modification_cancel':
                $this->modificationCancel($builder);
                break;
            case 'member_update':
                $this->memberUpdate($builder);
                break;
            case 'commission_create':
                $this->commissionCreate($builder);
                break;
            case 'commission_member_add':
                $this->commissionMemberAdd($builder);
                break;
            case 'print_stamp':
                $this->printStamp($builder);
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

    private function modificationValidate(FormBuilderInterface $builder)
    {
        $builder
            ->add('MemberModificationPhoto', FileType::class, array('label' => 'Photo : ', 'required' => false, 'mapped' => false, 'disabled' => true))
            ->add('MemberModificationAddress', TextType::class, array('label' => 'Adresse : ', 'required' => false, 'disabled' => true))
            ->add('MemberModificationZip', IntegerType::class, array('label' => 'Code postal : ', 'required' => false, 'disabled' => true))
            ->add('MemberModificationCity', TextType::class, array('label' => 'Localité : ', 'required' => false, 'disabled' => true))
            ->add('MemberModificationPhone', TextType::class, array('label' => 'N° téléphone : ', 'required' => false, 'disabled' => true))
            ->add('MemberModificationCountry', CountryType::class, array('label' => 'Pays : ', 'choice_translation_locale' => 'fr', 'preferred_choices' => array('BE', 'FR'), 'disabled' => true))
            ->add('MemberModificationEmail', EmailType::class, array('label' => 'Email : ', 'required' => false, 'disabled' => true))
            ->add('Submit', SubmitType::class, array('label' => 'Valider'))
        ;
    }

    private function modificationCancel(FormBuilderInterface $builder)
    {
        $builder
            ->add('MemberModificationPhoto', FileType::class, array('label' => 'Photo : ', 'required' => false, 'mapped' => false, 'disabled' => true))
            ->add('MemberModificationAddress', TextType::class, array('label' => 'Adresse : ', 'required' => false, 'disabled' => true))
            ->add('MemberModificationZip', IntegerType::class, array('label' => 'Code postal : ', 'required' => false, 'disabled' => true))
            ->add('MemberModificationCity', TextType::class, array('label' => 'Localité : ', 'required' => false, 'disabled' => true))
            ->add('MemberModificationPhone', TextType::class, array('label' => 'N° téléphone : ', 'required' => false, 'disabled' => true))
            ->add('MemberModificationCountry', CountryType::class, array('label' => 'Pays : ', 'choice_translation_locale' => 'fr', 'preferred_choices' => array('BE', 'FR'), 'disabled' => true))
            ->add('MemberModificationEmail', EmailType::class, array('label' => 'Email : ', 'required' => false, 'disabled' => true))
            ->add('Submit', SubmitType::class, array('label' => 'Valider'))
        ;
    }

    private function memberUpdate(FormBuilderInterface $builder)
    {
        $builder
            ->add('MemberPhoto', FileType::class, array('label' => 'Photo : ', 'required' => false, 'mapped' => false))
            ->add('MemberAddress', TextType::class, array('label' => 'Adresse : '))
            ->add('MemberZip', IntegerType::class, array('label' => 'Code postal : '))
            ->add('MemberCity', TextType::class, array('label' => 'Localité : '))
            ->add('MemberPhone', TextType::class, array('label' => 'N° téléphone : '))
            ->add('MemberCountry', CountryType::class, array('label' => 'Pays : ', 'choice_translation_locale' => 'fr', 'preferred_choices' => array('BE', 'FR')))
            ->add('MemberEmail', EmailType::class, array('label' => 'Email : '))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    private function commissionCreate(FormBuilderInterface $builder)
    {
        $list = new ListData();

        $builder
            ->add('CommissionName', TextType::class, array('label' => 'Nom : '))
            ->add('CommissionRole', ChoiceType::class, array('label' => 'Type d\'accès : ', 'placeholder' => 'Choississez un type d\'accès', 'choices' => $list->getAccessType()))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter'))
        ;
    }

    private function commissionMemberAdd(FormBuilderInterface $builder)
    {
        $builder
            ->add('MemberLicence', IntegerType::class, array('label' => 'N° de licence : ', 'mapped' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter'))
        ;
    }

    private function printStamp(FormBuilderInterface $builder)
    {
        $builder
            ->add('MemberList', TextType::class, array('label' => 'Liste n° de licence : ', 'mapped' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Créer les timbres'))
        ;
    }
}
