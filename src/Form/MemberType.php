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

class MemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['form'])
        {
            case 'update':
                $this->update($builder);
                break;
            case 'licence_renew':
                $this->licenceRenew($builder);
                break;
            case 'licence_renew_kyu':
                $this->licenceRenewKyu($builder);
                break;
            default:
                $this->create($builder);
        }
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => Member::class, 'form' => ''));
    }

    private function create(FormBuilderInterface $builder)
    {
        $list = new ListData();

        $builder
            ->add('MemberFirstName', TextType::class,
                array('label' => 'Prénom : '))
            ->add('MemberName', TextType::class,
                array('label' => 'Nom : '))
            ->add('MemberPhoto', FileType::class,
                array('label' => 'Photo : ',
                    'required'=> false))
            ->add('MemberSex', ChoiceType::class,
                array('label' => 'Sexe : ',
                    'multiple' => false,
                    'expanded' => true,
                    'choices' => array('Féminin' => 0, 'Masculin' => 1)))
            ->add('MemberAddress', TextType::class,
                array('label' => 'Adresse : '))
            ->add('MemberZip', IntegerType::class,
                array('label' => 'Code postal : '))
            ->add('MemberCity', TextType::class,
                array('label' => 'Localité : '))
            ->add('MemberCountry', CountryType::class,
                array('label' => 'Pays : ',
                    'choice_translation_locale' => 'fr',
                    'preferred_choices' => array('BE', 'FR')))
            ->add('MemberEmail', EmailType::class,
                array('label' => 'Email : '))
            ->add('MemberBirthday', BirthdayType::class,
                array('label' => 'Date de naissance : ',
                    'widget' => 'single_text'))
            ->add('GradeKyuRank', ChoiceType::class,
                array('label' => 'Grade : ',
                    'placeholder' => 'Choississez un grade',
                    'choices' => $list->getGradeKyu(),
                    'required' => false,
                    'mapped' => false))
            ->add('MemberLicenceMedicalCertificate', DateType::class,
                array('label' => 'Date certificat : ',
                    'widget' => 'single_text',
                    'mapped' => false))
            ->add('MemberComment', TextareaType::class,
                array('label' => 'Commentaire : ',
                    'required' => false))
            ->add('Submit', SubmitType::class,
                array('label' => 'Ajouter'))
        ;
    }

    private function update(FormBuilderInterface $builder)
    {
        $builder
            ->add('MemberPhoto', FileType::class,
                array('label' => 'Photo : ',
                    'required' => false,
                    'mapped' => false))
            ->add('MemberAddress', TextType::class,
                array('label' => 'Adresse : '))
            ->add('MemberZip', IntegerType::class,
                array('label' => 'Code postal : '))
            ->add('MemberCity', TextType::class,
                array('label' => 'Localité : '))
            ->add('MemberCountry', CountryType::class,
                array('label' => 'Pays : ',
                    'choice_translation_locale' => 'fr',
                    'preferred_choices' => array('BE', 'FR')))
            ->add('MemberEmail', EmailType::class,
                array('label' => 'Email : '))
            ->add('MemberComment', TextareaType::class,
                array('label' => 'Commentaire : ',
                    'required' => false))
            ->add('Submit', SubmitType::class,
                array('label' => 'Modifier'))
        ;
    }

    private function licenceRenew(FormBuilderInterface $builder)
    {
        $builder
            ->add('MemberLicenceClub', EntityType::class,
                array('label' => 'Club : ',
                    'class' => Club::class,
                    'choice_label' => 'club_number'))
            ->add('MemberLicenceDeadline', DateType::class,
                array('label' => 'Date échéance : ',
                    'widget' => 'single_text'))
            ->add('MemberLicenceMedicalCertificate', DateType::class,
                array('label' => 'Date certificat : ',
                    'widget' => 'single_text'))
            ->add('Submit', SubmitType::class,
                array('label' => 'Enregistrer'))
        ;
    }

    private function licenceRenewKyu(FormBuilderInterface $builder)
    {
        $list = new ListData();

        $builder
            ->add('MemberLicenceClub', EntityType::class,
                array('label' => 'Club : ',
                    'class' => Club::class,
                    'choice_label' => 'club_number'))
            ->add('GradeKyuRank', ChoiceType::class,
                array('label' => 'Grade : ',
                    'placeholder' => 'Choississez un grade',
                    'choices' => $list->getGradeKyu(),
                    'required' => false,
                    'mapped' => false))
            ->add('MemberLicenceDeadline', DateType::class,
                array('label' => 'Date échéance : ',
                    'widget' => 'single_text'))
            ->add('MemberLicenceMedicalCertificate', DateType::class,
                array('label' => 'Date certificat : ',
                    'widget' => 'single_text'))
            ->add('Submit', SubmitType::class,
                array('label' => 'Enregistrer'))
        ;
    }
}
