<?php
// src/Form/ClubType.php
namespace App\Form;

use App\Entity\Club;
use App\Entity\TrainingAddress;

use App\Service\ListData;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class ClubType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['form'])
        {
            case 'detail_association':
                $this->detailAssociation($builder);
                break;
            case 'dojo_create':
                $this->dojoCreate($builder);
                break;
            case 'dojo_delete':
                $this->dojoDelete($builder);
                break;
            case 'dojo_update':
                $this->dojoUpdate($builder);
                break;
            case 'history_entry':
                $this->historyEntry($builder);
                break;
            case 'teacher_afa_create':
                $this->teacherAFACreate($builder);
                break;
            case 'teacher_afa_delete':
                $this->teacherAFADelete($builder);
                break;
            case 'teacher_afa_update':
                $this->teacherAFAUpdate($builder);
                break;
            case 'teacher_foreign_create':
                $this->teacherForeignCreate($builder);
                break;
            case 'teacher_foreign_delete':
                $this->teacherForeignDelete($builder);
                break;
            case 'teacher_foreign_update':
                $this->teacherForeignUpdate($builder);
                break;
            case 'training_create':
                $choices = $options['choices'];
                $this->trainingCreate($builder, $choices);
                break;
            case 'training_delete':
                $choices = $options['choices'];
                $this->trainingDelete($builder, $choices);
                break;
            case 'training_update':
                $choices = $options['choices'];
                $this->trainingUpdate($builder, $choices);
                break;
            case 'search_members':
                $this->searchMembers($builder);
                break;
            default:
                $this->clubCreate($builder);
        }
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => Club::class, 'form' => '', 'choices' => null));
    }

    private function clubCreate(FormBuilderInterface $builder)
    {
        $list = new ListData();

        $builder
            ->add('ClubId', IntegerType::class, array('label' => 'N° Club : '))
            ->add('ClubName', TextType::class, array('label' => 'Nom : '))
            ->add('ClubAddress', TextType::class, array('label' => 'Adresse du siège : '))
            ->add('ClubZip', IntegerType::class, array('label' => 'Code postal : '))
            ->add('ClubCity', TextType::class, array('label' => 'Localité : '))
            ->add('ClubProvince', ChoiceType::class, array('label' => 'Province : ', 'placeholder' => 'Choississez une province', 'choices' => $list->getProvince(0)))
            ->add('ClubCreation', DateType::class, array('label' => 'Date de création : ', 'widget' => 'single_text', 'required' => false))
            ->add('ClubMembership', DateType::class, array('label' => 'Date d\'affiliation : ', 'widget' => 'single_text', 'mapped' => false))
            ->add('ClubType', ChoiceType::class, array('label' => 'Type d\'association : ', 'placeholder' => 'Choississez un type', 'choices' => $list->getClubType(0)))
            ->add('ClubBceNumber', TextType::class, array('label' => 'N° Entreprise : ', 'required' => false))
            ->add('ClubIban', TextType::class, array('label' => 'N° IBAN : ', 'required' => false))
            ->add('ClubUrl', UrlType::class, array('label' => 'Site internet : ', 'required' => false))
            ->add('ClubEmailPublic', EmailType::class, array('label' => 'Email publique : ', 'required' => false))
            ->add('ClubEmailContact', EmailType::class, array('label' => 'Email secrétariat : ', 'required' => false))
            ->add('ClubComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter'))
        ;
    }

    private function detailAssociation(FormBuilderInterface $builder)
    {
        $list = new ListData();

        $builder
            ->add('ClubName', TextType::class)
            ->add('ClubAddress', TextType::class)
            ->add('ClubZip', IntegerType::class)
            ->add('ClubCity', TextType::class)
            ->add('ClubType', ChoiceType::class, array('placeholder' => 'Choississez une fonction', 'choices' => $list->getClubType(0)))
            ->add('ClubBceNumber', TextType::class, array('label' => 'N° Entreprise : ', 'required' => false))
            ->add('ClubIban', TextType::class, array('label' => 'N° IBAN : ', 'required' => false))
            ->add('ClubNameContact', TextType::class)
            ->add('ClubEmailContact', EmailType::class)
            ->add('ClubPhoneContact', TextType::class)
            ->add('ClubAddressContact', TextType::class)
            ->add('ClubZipContact', IntegerType::class)
            ->add('ClubCityContact', TextType::class)
            ->add('ClubUrl', UrlType::class, array('required' => false))
            ->add('ClubEmailPublic', EmailType::class, array('required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    private function dojoCreate(FormBuilderInterface $builder)
    {
        $builder
            ->add('TrainingAddressName', TextType::class, array('label' => 'Lieu : ', 'required' => false))
            ->add('TrainingAddressStreet', TextType::class, array('label' => 'Adresse : '))
            ->add('TrainingAddressZip', IntegerType::class, array('label' => 'Code postal : '))
            ->add('TrainingAddressCity', TextType::class, array('label' => 'Localité : '))
            ->add('TrainingAddressTatamis', IntegerType::class, array('label' => 'Tatamis (m²) : ', 'required' => false))
            ->add('TrainingAddressDEA', ChoiceType::class, array('label' => 'Présence d\'un DEA : ', 'multiple' => false, 'expanded' => true, 'choices' => array('Non' => 0, 'Oui' => 1)))
            ->add('TrainingAddressDEAFormation', DateType::class, array('label' => 'Date de formation : ', 'widget' => 'single_text', 'required' => false))
            ->add('TrainingAddressComment', TextareaType::class, array('label' => 'Commentaire salle : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter'))
        ;
    }

    private function dojoUpdate(FormBuilderInterface $builder)
    {
        $builder
            ->add('TrainingAddressName', TextType::class, array('label' => 'Lieu : ', 'required' => false))
            ->add('TrainingAddressStreet', TextType::class, array('label' => 'Adresse : '))
            ->add('TrainingAddressZip', IntegerType::class, array('label' => 'Code postal : '))
            ->add('TrainingAddressCity', TextType::class, array('label' => 'Localité : '))
            ->add('TrainingAddressTatamis', IntegerType::class, array('label' => 'Tatamis (m²) : ', 'required' => false))
            ->add('TrainingAddressDEA', ChoiceType::class, array('label' => 'Présence d\'un DEA : ', 'multiple' => false, 'expanded' => true, 'choices' => array('Non' => 0, 'Oui' => 1)))
            ->add('TrainingAddressDEAFormation', DateType::class, array('label' => 'Date de formation : ', 'widget' => 'single_text', 'required' => false))
            ->add('TrainingAddressComment', TextareaType::class, array('label' => 'Commentaire salle : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    private function dojoDelete(FormBuilderInterface $builder)
    {
        $builder
            ->add('TrainingAddressName', TextType::class, array('label' => 'Lieu : ', 'required' => false, 'disabled' => true))
            ->add('TrainingAddressStreet', TextType::class, array('label' => 'Adresse : ', 'disabled' => true))
            ->add('TrainingAddressZip', IntegerType::class, array('label' => 'Code postal : ', 'disabled' => true))
            ->add('TrainingAddressCity', TextType::class, array('label' => 'Localité : ', 'disabled' => true))
            ->add('TrainingAddressTatamis', IntegerType::class, array('label' => 'Tatamis (m²) : ', 'required' => false, 'disabled' => true))
            ->add('TrainingAddressDEA', ChoiceType::class, array('label' => 'Présence d\'un DEA : ', 'multiple' => false, 'expanded' => true, 'choices' => array('Non' => 0, 'Oui' => 1), 'required' => false, 'disabled' => true))
            ->add('TrainingAddressDEAFormation', DateType::class, array('label' => 'Date de formation : ', 'widget' => 'single_text', 'disabled' => true))
            ->add('TrainingAddressComment', TextareaType::class, array('label' => 'Commentaire salle : ', 'required' => false, 'disabled' => true))
            ->add('Submit', SubmitType::class, array('label' => 'Supprimer'))
        ;
    }

    private function historyEntry(FormBuilderInterface $builder)
    {
        $builder
            ->add('ClubHistoryUpdate', DateType::class, array('label' => 'Date de désaffiliation : ', 'widget' => 'single_text'))
            ->add('ClubHistoryComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter'))
        ;
    }

    private function teacherAFACreate(FormBuilderInterface $builder)
    {
        $list = new ListData();

        $builder
            ->add('ClubTeacherTitle', ChoiceType::class, array('label' => 'Fonction : ', 'placeholder' => 'Choississez une fonction', 'choices' => $list->getTeacherTitle(0)))
            ->add('ClubTeacherType', ChoiceType::class, array('label' => 'Type : ', 'placeholder' => 'Choississez un type', 'choices' => $list->getTeacherType(0)))
            ->add('ClubTeacherMember', IntegerType::class, array('label' => 'N° Licence : ', 'mapped' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter'))
        ;
    }

    private function teacherAFAUpdate(FormBuilderInterface $builder)
    {
        $list = new ListData();

        $builder
            ->add('ClubTeacherTitle', ChoiceType::class, array('label' => 'Fonction : ', 'placeholder' => 'Choississez un jour', 'choices' => $list->getTeacherTitle(0)))
            ->add('ClubTeacherType', ChoiceType::class, array('label' => 'Type : ', 'placeholder' => 'Choississez un type', 'choices' => $list->getTeacherType(0)))
            ->add('ClubTeacherMember', IntegerType::class, array('label' => 'N° Licence : ', 'mapped' => false, 'disabled' => true))
            ->add('ClubTeacherFirstname', TextType::class, array('label' => 'Prénom : ', 'mapped' => false, 'disabled' => true))
            ->add('ClubTeacherName', TextType::class, array('label' => 'Nom : ', 'mapped' => false, 'disabled' => true))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    private function teacherAFADelete(FormBuilderInterface $builder)
    {
        $list = new ListData();

        $builder
            ->add('ClubTeacherTitle', ChoiceType::class, array('label' => 'Fonction : ', 'placeholder' => 'Choississez un jour', 'choices' => $list->getTeacherTitle(0), 'disabled' => true))
            ->add('ClubTeacherType', ChoiceType::class, array('label' => 'Type : ', 'placeholder' => 'Choississez un type', 'choices' => $list->getTeacherType(0), 'disabled' => true))
            ->add('ClubTeacherMember', IntegerType::class, array('label' => 'N° Licence : ', 'mapped' => false, 'disabled' => true))
            ->add('ClubTeacherFirstname', TextType::class, array('label' => 'Prénom : ', 'mapped' => false, 'disabled' => true))
            ->add('ClubTeacherName', TextType::class, array('label' => 'Nom : ', 'mapped' => false, 'disabled' => true))
            ->add('Submit', SubmitType::class, array('label' => 'Supprimer'))
        ;
    }

    private function teacherForeignCreate(FormBuilderInterface $builder)
    {
        $list = new ListData();

        $builder
            ->add('ClubTeacherTitle', ChoiceType::class, array('label' => 'Fonction : ', 'placeholder' => 'Choississez une fonction', 'choices' => $list->getTeacherTitle(0)))
            ->add('ClubTeacherType', ChoiceType::class, array('label' => 'Type : ', 'placeholder' => 'Choississez un type', 'choices' => $list->getTeacherType(0)))
            ->add('ClubTeacherFirstname', TextType::class, array('label' => 'Prénom : '))
            ->add('ClubTeacherName', TextType::class, array('label' => 'Nom : '))
            ->add('ClubTeacherGrade', ChoiceType::class, array('label' => 'Grade : ', 'placeholder' => 'Choississez un grade', 'choices' => $list->getGrade(0)))
            ->add('ClubTeacherGradeTitleAikikai', ChoiceType::class, array('label' => 'Grade enseignement Aïkikaï: ', 'placeholder' => 'Choississez un grade', 'choices' => $list->getGradeTitleAikikai(0), 'required' => false))
            ->add('ClubTeacherGradeTitleAdeps', ChoiceType::class, array('label' => 'Grade enseignement ADEPS : ', 'placeholder' => 'Choississez un grade', 'choices' => $list->getGradeTitleAdeps(0), 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter'))
        ;
    }

    private function teacherForeignUpdate(FormBuilderInterface $builder)
    {
        $list = new ListData();

        $builder
            ->add('ClubTeacherTitle', ChoiceType::class, array('label' => 'Fonction : ', 'placeholder' => 'Choississez un jour', 'choices' => $list->getTeacherTitle(0)))
            ->add('ClubTeacherType', ChoiceType::class, array('label' => 'Type : ', 'placeholder' => 'Choississez un type', 'choices' => $list->getTeacherType(0)))
            ->add('ClubTeacherFirstname', TextType::class, array('label' => 'Prénom : '))
            ->add('ClubTeacherName', TextType::class, array('label' => 'Nom : '))
            ->add('ClubTeacherGrade', ChoiceType::class, array('label' => 'Grade : ', 'placeholder' => 'Choississez un grade', 'choices' => $list->getGrade(0)))
            ->add('ClubTeacherGradeTitleAikikai', ChoiceType::class, array('label' => 'Grade enseignement Aïkikaï: ', 'placeholder' => 'Choississez un grade', 'choices' => $list->getGradeTitleAikikai(0), 'required' => false))
            ->add('ClubTeacherGradeTitleAdeps', ChoiceType::class, array('label' => 'Grade enseignement ADEPS : ', 'placeholder' => 'Choississez un grade', 'choices' => $list->getGradeTitleAdeps(0), 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    private function teacherForeignDelete(FormBuilderInterface $builder)
    {
        $list = new ListData();

        $builder
            ->add('ClubTeacherTitle', ChoiceType::class, array('label' => 'Fonction : ', 'placeholder' => 'Choississez un jour', 'choices' => $list->getTeacherTitle(0), 'disabled' => true))
            ->add('ClubTeacherType', ChoiceType::class, array('label' => 'Type : ', 'placeholder' => 'Choississez un type', 'choices' => $list->getTeacherType(0), 'disabled' => true))
            ->add('ClubTeacherFirstname', TextType::class, array('label' => 'Prénom : ', 'disabled' => true))
            ->add('ClubTeacherName', TextType::class, array('label' => 'Nom : ', 'disabled' => true))
            ->add('ClubTeacherGradeTitleAikikai', ChoiceType::class, array('label' => 'Grade enseignement Aïkikaï: ', 'placeholder' => 'Choississez un grade', 'choices' => $list->getGradeTitleAikikai(0), 'required' => false, 'disabled' => true))
            ->add('ClubTeacherGradeTitleAdeps', ChoiceType::class, array('label' => 'Grade enseignement ADEPS : ', 'placeholder' => 'Choississez un grade', 'choices' => $list->getGradeTitleAdeps(0), 'required' => false, 'disabled' => true))
            ->add('Submit', SubmitType::class, array('label' => 'Supprimer'))
        ;
    }

    private function trainingCreate(FormBuilderInterface $builder, $choices)
    {
        $list = new ListData();

        $builder
            ->add('TrainingDay', ChoiceType::class, array('label' => 'Jour : ', 'placeholder' => 'Choississez un jour', 'choices' => $list->getDay(0)))
            ->add('TrainingStartingHour', TimeType::class, array ('label' => 'Début : '))
            ->add('TrainingEndingHour', TimeType::class, array ('label' => 'Fin : '))
            ->add('TrainingType', ChoiceType::class, array('label' => 'Type de cours : ', 'placeholder' => 'Choississez un type de cours', 'choices' => $list->getTrainingType(0)))
            ->add('TrainingAddress', EntityType::class, array ('label' => 'Adresse : ', 'class' => TrainingAddress::class, 'choices' => $choices, 'choice_label' => 'training_address_street'))
            ->add('TrainingComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter'))
        ;
    }

    private function trainingDelete(FormBuilderInterface $builder, $choices)
    {
        $list = new ListData();

        $builder
            ->add('TrainingDay', ChoiceType::class, array('label' => 'Jour : ', 'placeholder' => 'Choississez un jour', 'choices' => $list->getDay(0), 'disabled' => true))
            ->add('TrainingStartingHour', TimeType::class, array ('label' => 'Début : ', 'disabled' => true))
            ->add('TrainingEndingHour', TimeType::class, array ('label' => 'Fin : ', 'disabled' => true))
            ->add('TrainingType', ChoiceType::class, array('label' => 'Type de cours : ', 'placeholder' => 'Choississez un type de cours', 'choices' => $list->getTrainingType(0), 'disabled' => true))
            ->add('TrainingAddress', EntityType::class, array ('label' => 'Adresse : ', 'class' => TrainingAddress::class, 'choices' => $choices, 'choice_label' => 'training_address_street', 'disabled' => true))
            ->add('TrainingComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false, 'disabled' => true))
            ->add('Submit', SubmitType::class, array('label' => 'Supprimer'))
        ;
    }

    private function trainingUpdate(FormBuilderInterface $builder, $choices)
    {
        $list = new ListData();

        $builder
            ->add('TrainingDay', ChoiceType::class, array('label' => 'Jour : ', 'placeholder' => 'Choississez un jour', 'choices' => $list->getDay(0)))
            ->add('TrainingStartingHour', TimeType::class, array ('label' => 'Début : '))
            ->add('TrainingEndingHour', TimeType::class, array ('label' => 'Fin : '))
            ->add('TrainingType', ChoiceType::class, array('label' => 'Type de cours : ', 'placeholder' => 'Choississez un type de cours', 'choices' => $list->getTrainingType(0)))
            ->add('TrainingAddress', EntityType::class, array ('label' => 'Adresse : ', 'class' => TrainingAddress::class, 'choices' => $choices, 'choice_label' => 'training_address_street'))
            ->add('TrainingComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    private function searchMembers(FormBuilderInterface $builder)
    {
        $builder
            ->add('Search', TextType::class, array('label' => 'N° licence, Nom', 'mapped' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Rechercher'))
        ;
    }
}
