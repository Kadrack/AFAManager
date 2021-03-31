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

/**
 * Class SecretariatType
 * @package App\Form
 */
class SecretariatType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['form'])
        {
            case 'supporterCreate':
                $this->supporterCreate($builder);
                break;
            case 'supporterDelete':
                $this->supporterDelete($builder);
                break;
            case 'supporterUpdate':
                $this->supporterUpdate($builder);
                break;
            case 'modificationValidate':
                $this->modificationValidate($builder);
                break;
            case 'modificationCancel':
                $this->modificationCancel($builder);
                break;
            case 'memberUpdate':
                $this->memberUpdate($builder);
                break;
            case 'commissionCreate':
                $this->commissionCreate($builder);
                break;
            case 'commissionMemberAdd':
                $this->commissionMemberAdd($builder);
                break;
            case 'commissionMemberDelete':
                $this->commissionMemberDelete($builder);
                break;
            case 'printStamp':
                $this->printStamp($builder);
                break;
            case 'printCard':
                $this->printCard($builder);
                break;
            case 'formRenewCreate':
                $this->formRenewCreate($builder);
                break;
            case 'searchMembers':
                $this->searchMembers($builder);
                break;
            case 'cleanupMember':
                $this->cleanupMember($builder);
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

    /**
     * @param FormBuilderInterface $builder
     */
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

    /**
     * @param FormBuilderInterface $builder
     */
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

    /**
     * @param FormBuilderInterface $builder
     */
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

    /**
     * @param FormBuilderInterface $builder
     */
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

    /**
     * @param FormBuilderInterface $builder
     */
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

    /**
     * @param FormBuilderInterface $builder
     */
    private function commissionCreate(FormBuilderInterface $builder)
    {
        $list = new ListData();

        $builder
            ->add('CommissionName', TextType::class, array('label' => 'Nom : '))
            ->add('CommissionRole', ChoiceType::class, array('label' => 'Type d\'accès : ', 'placeholder' => 'Choississez un type d\'accès', 'choices' => $list->getAccessType()))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function commissionMemberAdd(FormBuilderInterface $builder)
    {
        $builder
            ->add('MemberLicence', IntegerType::class, array('label' => 'N° de licence : ', 'mapped' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function commissionMemberDelete(FormBuilderInterface $builder)
    {
        $builder
            ->add('MemberId', IntegerType::class, array('label' => 'N° de licence : ', 'disabled' => true))
            ->add('Submit', SubmitType::class, array('label' => 'Supprimer'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function printStamp(FormBuilderInterface $builder)
    {
        $builder
            ->add('MemberList', TextType::class, array('label' => 'Liste n° de licence : ', 'mapped' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Créer les timbres'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function printCard(FormBuilderInterface $builder)
    {
        $builder
            ->add('MemberId', TextType::class, array('label' => 'Liste n° de licence : ', 'mapped' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Créer les cartes'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function formRenewCreate(FormBuilderInterface $builder)
    {
        $builder
            ->add('Start', DateType::class, array('label' => 'Echéance du : ', 'widget' => 'single_text', 'mapped' => false))
            ->add('End', DateType::class, array('label' => 'Echéance au : ', 'widget' => 'single_text', 'mapped' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Générer les formulaires'))
        ;
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

    /**
     * @param FormBuilderInterface $builder
     */
    private function cleanupMember(FormBuilderInterface $builder)
    {
        $builder
            ->add('Submit', SubmitType::class, array('label' => 'Nettoyer les 50 premiers'))
        ;
    }
}
