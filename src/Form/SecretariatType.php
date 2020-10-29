<?php
// src/Form/SecretariatType.php
namespace App\Form;

use App\Service\ListData;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
            case 'commission_member_delete':
                $this->commissionMemberDelete($builder);
                break;
            case 'print_stamp':
                $this->printStamp($builder);
                break;
            case 'print_card':
                $this->printCard($builder);
                break;
            case 'form_renew_create':
                $this->formRenewCreate($builder);
                break;
            case 'search_members':
                $this->searchMembers($builder);
                break;
            case 'cleanup_member':
                $this->cleanupMember($builder);
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
        $list = new ListData();

        $builder
            ->add('MemberModificationPhoto', FileType::class, array('label' => 'Photo : ', 'required' => false, 'mapped' => false, 'disabled' => true))
            ->add('MemberModificationFirstname', TextType::class, array('label' => 'Prénom : ', 'required' => false, 'disabled' => true))
            ->add('MemberModificationName', TextType::class, array('label' => 'Nom : ', 'required' => false, 'disabled' => true))
            ->add('MemberModificationSex', ChoiceType::class, array('label' => 'Sexe : ', 'multiple' => false, 'expanded' => true, 'choices' => $list->getSex(0), 'required' => false, 'disabled' => true))
            ->add('MemberModificationBirthday', DateType::class, array('label' => 'Date de naissance : ', 'widget' => 'single_text', 'required' => false, 'disabled' => true))
            ->add('MemberModificationAddress', TextType::class, array('label' => 'Adresse : ', 'required' => false, 'disabled' => true))
            ->add('MemberModificationZip', IntegerType::class, array('label' => 'Code postal : ', 'required' => false, 'disabled' => true))
            ->add('MemberModificationCity', TextType::class, array('label' => 'Localité : ', 'required' => false, 'disabled' => true))
            ->add('MemberModificationPhone', TextType::class, array('label' => 'N° téléphone : ', 'required' => false, 'disabled' => true))
            ->add('MemberModificationCountry', CountryType::class, array('label' => 'Pays : ', 'choice_translation_locale' => 'fr', 'preferred_choices' => array('BE', 'FR'), 'disabled' => true))
            ->add('MemberModificationEmail', EmailType::class, array('label' => 'Email : ', 'required' => false, 'disabled' => true))
            ->add('MemberModificationAikikaiId', TextType::class, array('label' => 'Aïkikaï Id : ', 'required' => false, 'disabled' => true))
            ->add('Submit', SubmitType::class, array('label' => 'Valider'))
        ;
    }

    private function modificationCancel(FormBuilderInterface $builder)
    {
        $list = new ListData();

        $builder
            ->add('MemberModificationPhoto', FileType::class, array('label' => 'Photo : ', 'required' => false, 'mapped' => false, 'disabled' => true))
            ->add('MemberModificationFirstname', TextType::class, array('label' => 'Prénom : ', 'required' => false, 'disabled' => true))
            ->add('MemberModificationName', TextType::class, array('label' => 'Nom : ', 'required' => false, 'disabled' => true))
            ->add('MemberModificationSex', ChoiceType::class, array('label' => 'Sexe : ', 'multiple' => false, 'expanded' => true, 'choices' => $list->getSex(0), 'required' => false, 'disabled' => true))
            ->add('MemberModificationBirthday', DateType::class, array('label' => 'Date de naissance : ', 'widget' => 'single_text', 'required' => false, 'disabled' => true))
            ->add('MemberModificationAddress', TextType::class, array('label' => 'Adresse : ', 'required' => false, 'disabled' => true))
            ->add('MemberModificationZip', IntegerType::class, array('label' => 'Code postal : ', 'required' => false, 'disabled' => true))
            ->add('MemberModificationCity', TextType::class, array('label' => 'Localité : ', 'required' => false, 'disabled' => true))
            ->add('MemberModificationPhone', TextType::class, array('label' => 'N° téléphone : ', 'required' => false, 'disabled' => true))
            ->add('MemberModificationCountry', CountryType::class, array('label' => 'Pays : ', 'choice_translation_locale' => 'fr', 'preferred_choices' => array('BE', 'FR'), 'disabled' => true))
            ->add('MemberModificationEmail', EmailType::class, array('label' => 'Email : ', 'required' => false, 'disabled' => true))
            ->add('MemberModificationAikikaiId', TextType::class, array('label' => 'Aïkikaï Id : ', 'required' => false, 'disabled' => true))
            ->add('Submit', SubmitType::class, array('label' => 'Valider'))
        ;
    }

    private function memberUpdate(FormBuilderInterface $builder)
    {
        $list = new ListData();

        $builder
            ->add('MemberPhoto', FileType::class, array('label' => 'Photo : ', 'required' => false, 'mapped' => false))
            ->add('MemberFirstname', TextType::class, array('label' => 'Prénom : '))
            ->add('MemberName', TextType::class, array('label' => 'Nom : '))
            ->add('MemberSex', ChoiceType::class, array('label' => 'Sexe : ', 'multiple' => false, 'expanded' => true, 'choices' => $list->getSex(0)))
            ->add('MemberBirthday', DateType::class, array('label' => 'Date de naissance : ', 'widget' => 'single_text'))
            ->add('MemberAddress', TextType::class, array('label' => 'Adresse : '))
            ->add('MemberZip', IntegerType::class, array('label' => 'Code postal : '))
            ->add('MemberCity', TextType::class, array('label' => 'Localité : '))
            ->add('MemberPhone', TextType::class, array('label' => 'N° téléphone : ', 'required' => false))
            ->add('MemberCountry', CountryType::class, array('label' => 'Pays : ', 'choice_translation_locale' => 'fr', 'preferred_choices' => array('BE', 'FR')))
            ->add('MemberEmail', EmailType::class, array('label' => 'Email : ', 'required' => false))
            ->add('MemberAikikaiId', TextType::class, array('label' => 'Aikikai Id : ', 'required' => false))
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

    private function commissionMemberDelete(FormBuilderInterface $builder)
    {
        $builder
            ->add('MemberId', IntegerType::class, array('label' => 'N° de licence : ', 'disabled' => true))
            ->add('Submit', SubmitType::class, array('label' => 'Supprimer'))
        ;
    }

    private function printStamp(FormBuilderInterface $builder)
    {
        $builder
            ->add('MemberList', TextType::class, array('label' => 'Liste n° de licence : ', 'mapped' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Créer les timbres'))
        ;
    }

    private function printCard(FormBuilderInterface $builder)
    {
        $builder
            ->add('MemberId', TextType::class, array('label' => 'Liste n° de licence : ', 'mapped' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Créer les cartes'))
        ;
    }

    private function formRenewCreate(FormBuilderInterface $builder)
    {
        $builder
            ->add('Start', DateType::class, array('label' => 'Echéance du : ', 'widget' => 'single_text', 'mapped' => false))
            ->add('End', DateType::class, array('label' => 'Echéance au : ', 'widget' => 'single_text', 'mapped' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Générer les formulaires'))
        ;
    }

    private function searchMembers(FormBuilderInterface $builder)
    {
        $builder
            ->add('Search', TextType::class, array('label' => 'N° licence, Nom', 'mapped' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Rechercher'))
        ;
    }

    private function cleanupMember(FormBuilderInterface $builder)
    {
        $builder
            ->add('Submit', SubmitType::class, array('label' => 'Nettoyer les 50 premiers'))
        ;
    }
}
