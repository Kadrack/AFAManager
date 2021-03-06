<?php
// src/Form/ClubType.php
namespace App\Form;

use App\Entity\Club;
use App\Entity\ClubDojo;

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

/**
 * Class ClubType
 * @package App\Form
 */
class ClubType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['form'])
        {
            case 'detailAssociation':
                $this->detailAssociation($builder);
                break;
            case 'dojoCreate':
                $this->dojoCreate($builder);
                break;
            case 'dojoDelete':
                $this->dojoDelete($builder);
                break;
            case 'dojoUpdate':
                $this->dojoUpdate($builder);
                break;
            case 'historyEntry':
                $this->historyEntry($builder);
                break;
            case 'teacherAFACreate':
                $this->teacherAFACreate($builder);
                break;
            case 'teacherAFADelete':
                $this->teacherAFADelete($builder);
                break;
            case 'teacherAFAUpdate':
                $this->teacherAFAUpdate($builder);
                break;
            case 'teacherForeignCreate':
                $this->teacherForeignCreate($builder);
                break;
            case 'teacherForeignDelete':
                $this->teacherForeignDelete($builder);
                break;
            case 'teacherForeignUpdate':
                $this->teacherForeignUpdate($builder);
                break;
            case 'trainingCreate':
                $choices = $options['choices'];
                $this->trainingCreate($builder, $choices);
                break;
            case 'trainingDelete':
                $choices = $options['choices'];
                $this->trainingDelete($builder, $choices);
                break;
            case 'trainingUpdate':
                $choices = $options['choices'];
                $this->trainingUpdate($builder, $choices);
                break;
            case 'searchMembers':
                $this->searchMembers($builder);
                break;
            default:
                $this->clubCreate($builder);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => Club::class, 'form' => '', 'choices' => null));
    }

    /**
     * @param FormBuilderInterface $builder
     */
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

    /**
     * @param FormBuilderInterface $builder
     */
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

    /**
     * @param FormBuilderInterface $builder
     */
    private function dojoCreate(FormBuilderInterface $builder)
    {
        $builder
            ->add('ClubDojoName', TextType::class, array('label' => 'Lieu : ', 'required' => false))
            ->add('ClubDojoStreet', TextType::class, array('label' => 'Adresse : '))
            ->add('ClubDojoZip', IntegerType::class, array('label' => 'Code postal : '))
            ->add('ClubDojoCity', TextType::class, array('label' => 'Localité : '))
            ->add('ClubDojoTatamis', IntegerType::class, array('label' => 'Tatamis (m²) : ', 'required' => false))
            ->add('ClubDojoDEA', ChoiceType::class, array('label' => 'Présence d\'un DEA : ', 'multiple' => false, 'expanded' => true, 'choices' => array('Non' => 0, 'Oui' => 1)))
            ->add('ClubDojoDEAFormation', DateType::class, array('label' => 'Date de formation : ', 'widget' => 'single_text', 'required' => false))
            ->add('ClubDojoComment', TextareaType::class, array('label' => 'Commentaire salle : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function dojoUpdate(FormBuilderInterface $builder)
    {
        $builder
            ->add('ClubDojoName', TextType::class, array('label' => 'Lieu : ', 'required' => false))
            ->add('ClubDojoStreet', TextType::class, array('label' => 'Adresse : '))
            ->add('ClubDojoZip', IntegerType::class, array('label' => 'Code postal : '))
            ->add('ClubDojoCity', TextType::class, array('label' => 'Localité : '))
            ->add('ClubDojoTatamis', IntegerType::class, array('label' => 'Tatamis (m²) : ', 'required' => false))
            ->add('ClubDojoDEA', ChoiceType::class, array('label' => 'Présence d\'un DEA : ', 'multiple' => false, 'expanded' => true, 'choices' => array('Non' => 0, 'Oui' => 1)))
            ->add('ClubDojoDEAFormation', DateType::class, array('label' => 'Date de formation : ', 'widget' => 'single_text', 'required' => false))
            ->add('ClubDojoComment', TextareaType::class, array('label' => 'Commentaire salle : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function dojoDelete(FormBuilderInterface $builder)
    {
        $builder
            ->add('ClubDojoName', TextType::class, array('label' => 'Lieu : ', 'required' => false, 'disabled' => true))
            ->add('ClubDojoStreet', TextType::class, array('label' => 'Adresse : ', 'disabled' => true))
            ->add('ClubDojoZip', IntegerType::class, array('label' => 'Code postal : ', 'disabled' => true))
            ->add('ClubDojoCity', TextType::class, array('label' => 'Localité : ', 'disabled' => true))
            ->add('ClubDojoTatamis', IntegerType::class, array('label' => 'Tatamis (m²) : ', 'required' => false, 'disabled' => true))
            ->add('ClubDojoDEA', ChoiceType::class, array('label' => 'Présence d\'un DEA : ', 'multiple' => false, 'expanded' => true, 'choices' => array('Non' => 0, 'Oui' => 1), 'required' => false, 'disabled' => true))
            ->add('ClubDojoDEAFormation', DateType::class, array('label' => 'Date de formation : ', 'widget' => 'single_text', 'disabled' => true))
            ->add('ClubDojoComment', TextareaType::class, array('label' => 'Commentaire salle : ', 'required' => false, 'disabled' => true))
            ->add('Submit', SubmitType::class, array('label' => 'Supprimer'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function historyEntry(FormBuilderInterface $builder)
    {
        $builder
            ->add('ClubHistoryUpdate', DateType::class, array('label' => 'Date de désaffiliation : ', 'widget' => 'single_text'))
            ->add('ClubHistoryComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
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

    /**
     * @param FormBuilderInterface $builder
     */
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

    /**
     * @param FormBuilderInterface $builder
     */
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

    /**
     * @param FormBuilderInterface $builder
     */
    private function teacherForeignCreate(FormBuilderInterface $builder)
    {
        $list = new ListData();

        $builder
            ->add('ClubTeacherTitle', ChoiceType::class, array('label' => 'Fonction : ', 'placeholder' => 'Choississez une fonction', 'choices' => $list->getTeacherTitle(0)))
            ->add('ClubTeacherType', ChoiceType::class, array('label' => 'Type : ', 'placeholder' => 'Choississez un type', 'choices' => $list->getTeacherType(0)))
            ->add('ClubTeacherFirstname', TextType::class, array('label' => 'Prénom : '))
            ->add('ClubTeacherName', TextType::class, array('label' => 'Nom : '))
            ->add('ClubTeacherGrade', ChoiceType::class, array('label' => 'Grade : ', 'placeholder' => 'Choississez un grade', 'choices' => $list->getGrade()))
            ->add('ClubTeacherGradeTitleAikikai', ChoiceType::class, array('label' => 'Grade enseignement Aïkikaï: ', 'placeholder' => 'Choississez un grade', 'choices' => $list->getGradeTitle(0), 'required' => false))
            ->add('ClubTeacherGradeTitleAdeps', ChoiceType::class, array('label' => 'Grade enseignement ADEPS : ', 'placeholder' => 'Choississez un grade', 'choices' => $list->getGradeTitle(0), 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function teacherForeignUpdate(FormBuilderInterface $builder)
    {
        $list = new ListData();

        $builder
            ->add('ClubTeacherTitle', ChoiceType::class, array('label' => 'Fonction : ', 'placeholder' => 'Choississez un jour', 'choices' => $list->getTeacherTitle(0)))
            ->add('ClubTeacherType', ChoiceType::class, array('label' => 'Type : ', 'placeholder' => 'Choississez un type', 'choices' => $list->getTeacherType(0)))
            ->add('ClubTeacherFirstname', TextType::class, array('label' => 'Prénom : '))
            ->add('ClubTeacherName', TextType::class, array('label' => 'Nom : '))
            ->add('ClubTeacherGrade', ChoiceType::class, array('label' => 'Grade : ', 'placeholder' => 'Choississez un grade', 'choices' => $list->getGrade()))
            ->add('ClubTeacherGradeTitleAikikai', ChoiceType::class, array('label' => 'Grade enseignement Aïkikaï: ', 'placeholder' => 'Choississez un grade', 'choices' => $list->getGradeTitle(0), 'required' => false))
            ->add('ClubTeacherGradeTitleAdeps', ChoiceType::class, array('label' => 'Grade enseignement ADEPS : ', 'placeholder' => 'Choississez un grade', 'choices' => $list->getGradeTitle(0), 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function teacherForeignDelete(FormBuilderInterface $builder)
    {
        $list = new ListData();

        $builder
            ->add('ClubTeacherTitle', ChoiceType::class, array('label' => 'Fonction : ', 'placeholder' => 'Choississez un jour', 'choices' => $list->getTeacherTitle(0), 'disabled' => true))
            ->add('ClubTeacherType', ChoiceType::class, array('label' => 'Type : ', 'placeholder' => 'Choississez un type', 'choices' => $list->getTeacherType(0), 'disabled' => true))
            ->add('ClubTeacherFirstname', TextType::class, array('label' => 'Prénom : ', 'disabled' => true))
            ->add('ClubTeacherName', TextType::class, array('label' => 'Nom : ', 'disabled' => true))
            ->add('ClubTeacherGradeTitleAikikai', ChoiceType::class, array('label' => 'Grade enseignement Aïkikaï: ', 'placeholder' => 'Choississez un grade', 'choices' => $list->getGradeTitle(0), 'required' => false, 'disabled' => true))
            ->add('ClubTeacherGradeTitleAdeps', ChoiceType::class, array('label' => 'Grade enseignement ADEPS : ', 'placeholder' => 'Choississez un grade', 'choices' => $list->getGradeTitle(0), 'required' => false, 'disabled' => true))
            ->add('Submit', SubmitType::class, array('label' => 'Supprimer'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param $choices
     */
    private function trainingCreate(FormBuilderInterface $builder, $choices)
    {
        $list = new ListData();

        $builder
            ->add('ClubLessonDay', ChoiceType::class, array('label' => 'Jour : ', 'placeholder' => 'Choississez un jour', 'choices' => $list->getDay(0)))
            ->add('ClubLessonStartingHour', TimeType::class, array ('label' => 'Début : '))
            ->add('ClubLessonEndingHour', TimeType::class, array ('label' => 'Fin : '))
            ->add('ClubLessonType', ChoiceType::class, array('label' => 'Type de cours : ', 'placeholder' => 'Choississez un type de cours', 'choices' => $list->getLessonType(0)))
            ->add('ClubLessonDojo', EntityType::class, array ('label' => 'Adresse : ', 'class' => ClubDojo::class, 'choices' => $choices, 'choice_label' => 'club_dojo_street'))
            ->add('ClubLessonComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param $choices
     */
    private function trainingDelete(FormBuilderInterface $builder, $choices)
    {
        $list = new ListData();

        $builder
            ->add('ClubLessonDay', ChoiceType::class, array('label' => 'Jour : ', 'placeholder' => 'Choississez un jour', 'choices' => $list->getDay(0), 'disabled' => true))
            ->add('ClubLessonStartingHour', TimeType::class, array ('label' => 'Début : ', 'disabled' => true))
            ->add('ClubLessonEndingHour', TimeType::class, array ('label' => 'Fin : ', 'disabled' => true))
            ->add('ClubLessonType', ChoiceType::class, array('label' => 'Type de cours : ', 'placeholder' => 'Choississez un type de cours', 'choices' => $list->getLessonType(0), 'disabled' => true))
            ->add('ClubLessonDojo', EntityType::class, array ('label' => 'Adresse : ', 'class' => ClubDojo::class, 'choices' => $choices, 'choice_label' => 'club_dojo_street', 'disabled' => true))
            ->add('ClubLessonComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false, 'disabled' => true))
            ->add('Submit', SubmitType::class, array('label' => 'Supprimer'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param $choices
     */
    private function trainingUpdate(FormBuilderInterface $builder, $choices)
    {
        $list = new ListData();

        $builder
            ->add('ClubLessonDay', ChoiceType::class, array('label' => 'Jour : ', 'placeholder' => 'Choississez un jour', 'choices' => $list->getDay(0)))
            ->add('ClubLessonStartingHour', TimeType::class, array ('label' => 'Début : '))
            ->add('ClubLessonEndingHour', TimeType::class, array ('label' => 'Fin : '))
            ->add('ClubLessonType', ChoiceType::class, array('label' => 'Type de cours : ', 'placeholder' => 'Choississez un type de cours', 'choices' => $list->getLessonType(0)))
            ->add('ClubLessonDojo', EntityType::class, array ('label' => 'Adresse : ', 'class' => ClubDojo::class, 'choices' => $choices, 'choice_label' => 'club_dojo_street'))
            ->add('ClubLessonComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
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
}
