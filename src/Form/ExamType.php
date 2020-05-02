<?php
// src/Form/ExamType.php
namespace App\Form;

use App\Entity\GradeSession;

use App\Service\ListData;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ExamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['form'])
        {
            case 'update':
                $this->update($builder);
                break;
            case 'exam_application':
                $this->examApplication($builder);
                break;
            case 'applicant_validation':
                $this->applicantValidation($builder);
                break;
            case 'candidate_result':
                $this->candidate_result($builder);
                break;
            case 'candidate_aikikai':
                $this->candidate_aikikai($builder);
                break;
            default:
                $this->examCreate($builder);
        }
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => GradeSession::class, 'form' => ''));
    }

    private function examCreate(FormBuilderInterface $builder)
    {
        $builder
            ->add('GradeSessionDate', DateType::class,
                array('label' => 'Date de session : ',
                    'widget' => 'single_text'))
            ->add('GradeSessionCandidateOpen', DateType::class,
                array('label' => 'Ouverture inscription : ',
                    'widget' => 'single_text'))
            ->add('GradeSessionCandidateClose', DateType::class,
                array('label' => 'Fermeture inscription : ',
                    'widget' => 'single_text'))
            ->add('GradeSessionPlace', TextType::class,
                array('label' => 'Lieu : ',
                    'required' => false))
            ->add('GradeSessionStreet', TextType::class,
                array('label' => 'Adresse : '))
            ->add('GradeSessionZip', IntegerType::class,
                array('label' => 'Code postal : '))
            ->add('GradeSessionCity', TextType::class,
                array('label' => 'Localité : '))
            ->add('GradeSessionComment', TextareaType::class,
                array('label' => 'Commentaire : ',
                    'required' => false))
            ->add('Submit', SubmitType::class,
                array('label' => 'Ajouter'))
        ;
    }

    private function examApplication(FormBuilderInterface $builder)
    {
        $builder
            ->add('GradeDanComment', TextareaType::class,
                array('label' => 'Commentaire : ',
                    'required' => false))
            ->add('Submit', SubmitType::class,
                array('label' => 'Valider'))
        ;
    }

    private function applicantValidation(FormBuilderInterface $builder)
    {
        $builder
            ->add('GradeDanComment', TextareaType::class,
                array('label' => 'Commentaire : ',
                    'required' => false))
            ->add('Submit', SubmitType::class,
                array('label' => 'Valider'))
        ;
    }

    private function update(FormBuilderInterface $builder)
    {
        $builder
            ->add('GradeSessionDate', DateType::class,
                array('label' => 'Date de session : ',
                    'widget' => 'single_text'))
            ->add('GradeSessionCandidateOpen', DateType::class,
                array('label' => 'Ouverture inscription : ',
                    'widget' => 'single_text'))
            ->add('GradeSessionCandidateClose', DateType::class,
                array('label' => 'Fermeture inscription : ',
                    'widget' => 'single_text'))
            ->add('GradeSessionPlace', TextType::class,
                array('label' => 'Lieu : ',
                    'required' => false))
            ->add('GradeSessionStreet', TextType::class,
                array('label' => 'Adresse : '))
            ->add('GradeSessionZip', IntegerType::class,
                array('label' => 'Code postal : '))
            ->add('GradeSessionCity', TextType::class,
                array('label' => 'Localité : '))
            ->add('GradeSessionComment', TextareaType::class,
                array('label' => 'Commentaire : ',
                    'required' => false))
            ->add('Submit', SubmitType::class,
                array('label' => 'Modifier'))
        ;
    }

    private function candidate_result(FormBuilderInterface $builder)
    {
        $builder
            ->add('GradeDanStatus', ChoiceType::class,
                array('label' => 'Résultat : ',
                    'multiple' => false,
                    'expanded' => true,
                    'choices' => array('Refusé' => 3, 'Promu' => 4)))
            ->add('GradeDanCertificate', TextType::class,
                array('label' => 'N° Diplôme : ',
                    'required' => false))
            ->add('GradeDanComment', TextareaType::class,
                array('label' => 'Commentaire : ',
                    'required' => false))
            ->add('Submit', SubmitType::class,
                array('label' => 'Valider'))
        ;
    }

    private function candidate_aikikai(FormBuilderInterface $builder)
    {
        $builder
            ->add('GradeDanCertificate', TextType::class,
                array('label' => 'N° certificat : '))
            ->add('GradeDanComment', TextareaType::class,
                array('label' => 'Commentaire : ',
                    'required' => false))
            ->add('Submit', SubmitType::class,
                array('label' => 'Valider'))
        ;
    }
}
