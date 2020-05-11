<?php
// src/Form/ExamType.php
namespace App\Form;

use App\Entity\GradeSession;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class GradeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['form'])
        {
            case 'exam_update':
                $this->exam_update($builder);
                break;
            case 'exam_application':
                $this->exam_application($builder);
                break;
            case 'exam_applicant_validation':
                $this->exam_applicant_validation($builder);
                break;
            case 'exam_candidate_result':
                $this->exam_candidate_result($builder);
                break;
            case 'exam_candidate_aikikai':
                $this->exam_candidate_aikikai($builder);
                break;
            case 'kagami_create':
                $this->kagami_create($builder);
                break;
            case 'kagami_update':
                $this->kagami_update($builder);
                break;
            case 'kagami_candidate_result':
                $this->kagami_candidate_result($builder);
                break;
            default:
                $this->exam_create($builder);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => GradeSession::class, 'form' => ''));
    }

    private function exam_create(FormBuilderInterface $builder)
    {
        $builder
            ->add('GradeSessionDate', DateType::class, array('label' => 'Date de session : ', 'widget' => 'single_text'))
            ->add('GradeSessionCandidateOpen', DateType::class, array('label' => 'Ouverture inscription : ', 'widget' => 'single_text'))
            ->add('GradeSessionCandidateClose', DateType::class, array('label' => 'Fermeture inscription : ', 'widget' => 'single_text'))
            ->add('GradeSessionPlace', TextType::class, array('label' => 'Lieu : ', 'required' => false))
            ->add('GradeSessionStreet', TextType::class, array('label' => 'Adresse : '))
            ->add('GradeSessionZip', IntegerType::class, array('label' => 'Code postal : '))
            ->add('GradeSessionCity', TextType::class, array('label' => 'Localité : '))
            ->add('GradeSessionComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter'))
        ;
    }

    private function exam_application(FormBuilderInterface $builder)
    {
        $builder
            ->add('GradeDanComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Valider'))
        ;
    }

    private function exam_applicant_validation(FormBuilderInterface $builder)
    {
        $builder
            ->add('GradeDanComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Valider'))
        ;
    }

    private function exam_update(FormBuilderInterface $builder)
    {
        $builder
            ->add('GradeSessionDate', DateType::class, array('label' => 'Date de session : ', 'widget' => 'single_text'))
            ->add('GradeSessionCandidateOpen', DateType::class, array('label' => 'Ouverture inscription : ', 'widget' => 'single_text'))
            ->add('GradeSessionCandidateClose', DateType::class, array('label' => 'Fermeture inscription : ', 'widget' => 'single_text'))
            ->add('GradeSessionPlace', TextType::class, array('label' => 'Lieu : ', 'required' => false))
            ->add('GradeSessionStreet', TextType::class, array('label' => 'Adresse : '))
            ->add('GradeSessionZip', IntegerType::class, array('label' => 'Code postal : '))
            ->add('GradeSessionCity', TextType::class, array('label' => 'Localité : '))
            ->add('GradeSessionComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    private function exam_candidate_result(FormBuilderInterface $builder)
    {
        $builder
            ->add('GradeDanStatus', ChoiceType::class, array('label' => 'Résultat : ', 'multiple' => false, 'expanded' => true, 'choices' => array('Refusé' => 3, 'Promu' => 4)))
            ->add('GradeDanCertificate', TextType::class, array('label' => 'N° Diplôme : ', 'required' => false))
            ->add('GradeDanComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Valider'))
        ;
    }

    private function exam_candidate_aikikai(FormBuilderInterface $builder)
    {
        $builder
            ->add('GradeDanCertificate', TextType::class, array('label' => 'N° certificat : '))
            ->add('GradeDanComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Valider'))
        ;
    }

    private function kagami_create(FormBuilderInterface $builder)
    {
        $builder
            ->add('GradeSessionDate', DateType::class, array('label' => 'Date de session : ', 'widget' => 'single_text'))
            ->add('GradeSessionCandidateOpen', DateType::class, array('label' => 'Ouverture inscription : ', 'widget' => 'single_text'))
            ->add('GradeSessionComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter'))
        ;
    }

    private function kagami_update(FormBuilderInterface $builder)
    {
        $builder
            ->add('GradeSessionDate', DateType::class, array('label' => 'Date de session : ', 'widget' => 'single_text'))
            ->add('GradeSessionCandidateOpen', DateType::class, array('label' => 'Ouverture inscription : ', 'widget' => 'single_text'))
            ->add('GradeSessionCandidateClose', DateType::class, array('label' => 'Fermeture inscription : ', 'widget' => 'single_text'))
            ->add('GradeSessionComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    private function kagami_candidate_result(FormBuilderInterface $builder)
    {
        $builder
            ->add('GradeDanStatus', ChoiceType::class, array('label' => 'Résultat : ', 'multiple' => false, 'expanded' => true, 'choices' => array('Refusé' => 3, 'Promu' => 4)))
            ->add('GradeDanCertificate', TextType::class, array('label' => 'N° Diplôme : ', 'required' => false))
            ->add('GradeDanComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Valider'))
        ;
    }
}
