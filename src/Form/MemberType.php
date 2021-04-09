<?php
// src/Form/MemberType.php
namespace App\Form;

use App\Entity\Club;
use App\Entity\Member;

use App\Service\ListData;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class MemberType
 * @package App\Form
 */
class MemberType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['form'])
        {
            case 'myDataUpdate':
                $this->myDataUpdate($builder);
                break;
            case 'licenceRenew':
                $this->licenceRenew($builder);
                break;
            case 'licenceRenewKyu':
                $this->licenceRenewKyu($builder);
                break;
            default:
                $this->create($builder);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => Member::class, 'form' => ''));
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function myDataUpdate(FormBuilderInterface $builder)
    {
        $builder
            ->add('MemberModificationPhoto', FileType::class, array('label' => 'Photo : ', 'required' => false, 'mapped' => false))
            ->add('MemberModificationFirstname', TextType::class, array('label' => 'Prénom : ', 'required' => false))
            ->add('MemberModificationName', TextType::class, array('label' => 'Nom : ', 'required' => false))
            ->add('MemberModificationBirthday', DateType::class, array('label' => 'Date de naissance : ', 'widget' => 'single_text', 'required' => false))
            ->add('MemberModificationAddress', TextType::class, array('label' => 'Adresse : ', 'required' => false))
            ->add('MemberModificationZip', IntegerType::class, array('label' => 'Code postal : ', 'required' => false))
            ->add('MemberModificationCity', TextType::class, array('label' => 'Localité : ', 'required' => false))
            ->add('MemberModificationPhone', TextType::class, array('label' => 'N° téléphone : ', 'required' => false))
            ->add('MemberModificationCountry', CountryType::class, array('label' => 'Pays : ', 'choice_translation_locale' => 'fr', 'preferred_choices' => array('BE', 'FR')))
            ->add('MemberModificationEmail', EmailType::class, array('label' => 'Email : ', 'required' => false))
            ->add('MemberModificationAikikaiId', TextType::class, array('label' => 'Id Aïkikaï : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function create(FormBuilderInterface $builder)
    {
        $list = new ListData();

        $builder
            ->add('MemberFirstName', TextType::class, array('label' => 'Prénom : '))
            ->add('MemberName', TextType::class, array('label' => 'Nom : '))
            ->add('MemberPhoto', FileType::class, array('label' => 'Photo : ', 'required'=> false))
            ->add('MemberSex', ChoiceType::class, array('label' => 'Sexe : ', 'multiple' => false, 'expanded' => true, 'choices' => $list->getSex(0)))
            ->add('MemberAddress', TextType::class, array('label' => 'Adresse : '))
            ->add('MemberZip', IntegerType::class, array('label' => 'Code postal : '))
            ->add('MemberCity', TextType::class, array('label' => 'Localité : '))
            ->add('MemberCountry', CountryType::class, array('label' => 'Pays : ', 'choice_translation_locale' => 'fr', 'preferred_choices' => array('BE', 'FR')))
            ->add('MemberEmail', EmailType::class, array('label' => 'Email : ', 'required'=> false))
            ->add('MemberPhone', TextType::class, array('label' => 'N° téléphone : ', 'required'=> false))
            ->add('MemberBirthday', BirthdayType::class, array('label' => 'Date de naissance : ', 'widget' => 'single_text'))
            ->add('GradeRank', ChoiceType::class, array('label' => 'Grade : ', 'placeholder' => 'Choississez un grade', 'choices' => $list->getGrade(), 'required' => false, 'mapped' => false))
            ->add('MemberLicenceMedicalCertificate', DateType::class, array('label' => 'Date certificat : ', 'widget' => 'single_text', 'mapped' => false))
            ->add('MemberComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function licenceRenew(FormBuilderInterface $builder)
    {
        $builder
            ->add('MemberLicenceClub', EntityType::class, array('label' => 'Club : ', 'class' => Club::class, 'choice_label' => 'club_id'))
            ->add('MemberLicenceDeadline', DateType::class, array('label' => 'Date échéance : ', 'widget' => 'single_text'))
            ->add('MemberLicenceMedicalCertificate', DateType::class, array('label' => 'Date certificat : ', 'widget' => 'single_text'))
            ->add('Submit', SubmitType::class, array('label' => 'Enregistrer'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function licenceRenewKyu(FormBuilderInterface $builder)
    {
        $list = new ListData();

        $builder
            ->add('MemberLicenceClub', EntityType::class, array('label' => 'Club : ', 'class' => Club::class, 'choice_label' => 'club_id'))
            ->add('GradeKyuRank', ChoiceType::class, array('label' => 'Grade : ', 'placeholder' => 'Choississez un grade', 'choices' => $list->getGradeKyu(), 'required' => false, 'mapped' => false))
            ->add('MemberLicenceDeadline', DateType::class, array('label' => 'Date échéance : ', 'widget' => 'single_text'))
            ->add('MemberLicenceMedicalCertificate', DateType::class, array('label' => 'Date certificat : ', 'widget' => 'single_text'))
            ->add('Submit', SubmitType::class, array('label' => 'Enregistrer'))
        ;
    }
}
