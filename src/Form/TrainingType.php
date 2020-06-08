<?php
// src/Form/ClubType.php
namespace App\Form;

use App\Entity\Training;
use App\Entity\TrainingSession;

use App\Service\ListData;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TrainingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['form'])
        {
            case 'address_create':
                $this->addressCreate($builder);
                break;
            case 'attendance_add':
                $choices = $options['choices'];
                $this->attendanceAdd($builder, $choices);
                break;
            case 'attendance_foreign_add':
                $choices = $options['choices'];
                $this->attendanceForeignAdd($builder, $choices);
                break;
            case 'session_add':
                $this->sessionAdd($builder);
                break;
            case 'session_create':
                $this->sessionCreate($builder);
                break;
            case 'session_delete':
                $this->sessionDelete($builder);
                break;
            case 'session_update':
                $this->sessionUpdate($builder);
                break;
            case 'training_create':
                $this->trainingCreate($builder);
                break;
            case 'training_delete':
                $this->trainingDelete($builder);
                break;
            case 'training_update':
                $this->trainingUpdate($builder);
                break;
            default:
                $this->trainingCreate($builder);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => Training::class, 'form' => '', 'choices' => null));
    }

    private function addressCreate(FormBuilderInterface $builder)
    {
        $builder
            ->add('TrainingAddressName', TextType::class, array('label' => 'Lieu : ', 'required' => false))
            ->add('TrainingAddressStreet', TextType::class, array('label' => 'Adresse : '))
            ->add('TrainingAddressZip', IntegerType::class, array('label' => 'Code postal : '))
            ->add('TrainingAddressCity', TextType::class, array('label' => 'Localité : '))
            ->add('TrainingAddressTatamis', IntegerType::class, array('label' => 'Tatamis (m²) : ', 'required' => false))
            ->add('TrainingAddressDEA', ChoiceType::class, array('label' => 'Présence d\'un DEA : ', 'multiple' => false, 'expanded' => true, 'choices' => array('Non' => 0, 'Oui' => 1)))
        ;
    }

    private function attendanceAdd(FormBuilderInterface $builder, $choices)
    {
        $list = new ListData();

        $builder
            ->add('TrainingAttendanceId', TextType::class, array('label' => 'N° de licence : ', 'required' => true, 'mapped' => false))
            ->add('TrainingAttendanceSession', ChoiceType::class, array('label' => 'Session : ', 'multiple' => true, 'expanded' => true, 'choices' => $choices, 'mapped' => false, 'choice_value' => 'training_session_id', 'choice_label' => 'training_session_choice_name'))
            ->add('TrainingAttendancePayment', IntegerType::class, array('label' => 'Paiement : ', 'required' => false, 'mapped' => false))
            ->add('TrainingAttendancePaymentType', ChoiceType::class, array('label' => 'Mode de paiement : ', 'multiple' => false, 'expanded' => true, 'choices' => $list->getPaymentType(0), 'mapped' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter'))
        ;
    }

    private function attendanceForeignAdd(FormBuilderInterface $builder, $choices)
    {
        $list = new ListData();

        $builder
            ->add('TrainingAttendanceName', TextType::class, array('label' => 'Nom : ', 'required' => true, 'mapped' => false))
            ->add('TrainingAttendanceSex', ChoiceType::class, array('label' => 'Sexe : ', 'multiple' => false, 'expanded' => true, 'choices' => $list->getSex(0), 'mapped' => false))
            ->add('TrainingAttendanceCountry', CountryType::class, array('label' => 'Pays : ', 'choice_translation_locale' => 'fr', 'preferred_choices' => array('BE', 'FR'), 'required' => true, 'mapped' => false, 'placeholder' => 'Choississez un pays',))
            ->add('TrainingAttendanceSession', ChoiceType::class, array('label' => 'Session : ', 'multiple' => true, 'expanded' => true, 'choices' => $choices, 'mapped' => false, 'choice_value' => 'training_session_id', 'choice_label' => 'training_session_choice_name'))
            ->add('TrainingAttendancePayment', IntegerType::class, array('label' => 'Paiement : ', 'required' => false, 'mapped' => false))
            ->add('TrainingAttendancePaymentType', ChoiceType::class, array('label' => 'Mode de paiement : ', 'multiple' => false, 'expanded' => true, 'choices' => $list->getPaymentType(0), 'mapped' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter'))
        ;
    }

    private function sessionAdd(FormBuilderInterface $builder)
    {
        $builder
            ->add('TrainingSessionDate', DateType::class, array('label' => 'Date : ', 'widget' => 'single_text'))
            ->add('TrainingSessionStartingHour', TimeType::class, array ('label' => 'Début : '))
            ->add('TrainingSessionEndingHour', TimeType::class, array ('label' => 'Fin : '))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter'))
        ;
    }

    private function sessionCreate(FormBuilderInterface $builder)
    {
        $builder
            ->add('TrainingSessionDate', DateType::class, array('label' => 'Date : ', 'widget' => 'single_text'))
            ->add('TrainingSessionStartingHour', TimeType::class, array ('label' => 'Début : '))
            ->add('TrainingSessionEndingHour', TimeType::class, array ('label' => 'Fin : '))
        ;
    }

    private function sessionUpdate(FormBuilderInterface $builder)
    {
        $builder
            ->add('TrainingSessionDate', DateType::class, array('label' => 'Date : ', 'widget' => 'single_text'))
            ->add('TrainingSessionStartingHour', TimeType::class, array ('label' => 'Début : '))
            ->add('TrainingSessionEndingHour', TimeType::class, array ('label' => 'Fin : '))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    private function sessionDelete(FormBuilderInterface $builder)
    {
        $builder
            ->add('TrainingSessionDate', DateType::class, array('label' => 'Date : ', 'widget' => 'single_text', 'disabled' => true))
            ->add('TrainingSessionStartingHour', TimeType::class, array ('label' => 'Début : ', 'disabled' => true))
            ->add('TrainingSessionEndingHour', TimeType::class, array ('label' => 'Fin : ', 'disabled' => true))
            ->add('Submit', SubmitType::class, array('label' => 'Supprimer'))
        ;
    }

    private function trainingCreate(FormBuilderInterface $builder)
    {
        $list = new ListData();

        $builder
            ->add('TrainingName', TextType::class, array('label' => 'Nom : '))
            ->add('TrainingPlace', TextType::class, array('label' => 'Lieu : ', 'required' => false))
            ->add('TrainingStreet', TextType::class, array('label' => 'Adresse : '))
            ->add('TrainingZip', IntegerType::class, array('label' => 'Code postal : '))
            ->add('TrainingCity', TextType::class, array('label' => 'Localité : '))
            ->add('Session', TrainingType::class, array('form' => 'session_create', 'data_class' => TrainingSession::class))
            ->add('TrainingComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter'))
        ;
    }

    private function trainingUpdate(FormBuilderInterface $builder)
    {
        $list = new ListData();

        $builder
            ->add('TrainingType', ChoiceType::class, array('label' => 'Type de stage : ', 'placeholder' => 'Choississez un type', 'choices' => $list->getTrainingType(0)))
            ->add('TrainingName', TextType::class, array('label' => 'Nom : '))
            ->add('TrainingComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    private function trainingDelete(FormBuilderInterface $builder)
    {
        $list = new ListData();

        $builder
            ->add('TrainingType', ChoiceType::class, array('label' => 'Type de stage : ', 'placeholder' => 'Choississez un type', 'choices' => $list->getTrainingType(0), 'disabled' => true))
            ->add('TrainingName', TextType::class, array('label' => 'Nom : ', 'disabled' => true))
            ->add('TrainingComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false, 'disabled' => true))
            ->add('Submit', SubmitType::class, array('label' => 'Supprimer'))
        ;
    }
}
