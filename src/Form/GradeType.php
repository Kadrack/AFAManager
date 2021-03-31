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

/**
 * Class GradeType
 * @package App\Form
 */
class GradeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['form'])
        {
            case 'examUpdate':
                $this->examUpdate($builder);
                break;
            case 'examApplication':
                $this->examApplication($builder);
                break;
            case 'examApplicantValidation':
                $this->examApplicantValidation($builder);
                break;
            case 'examCandidateResult':
                $this->examCandidateResult($builder);
                break;
            case 'examCandidateAikikai':
                $this->examCandidateAikikai($builder);
                break;
            case 'kyuAdd':
                $this->kyuAdd($builder);
                break;
            case 'kyuModify':
                $this->kyuModify($builder);
                break;
            default:
                $this->examCreate($builder);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => GradeSession::class, 'form' => ''));
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function examCreate(FormBuilderInterface $builder)
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

    /**
     * @param FormBuilderInterface $builder
     */
    private function examApplication(FormBuilderInterface $builder)
    {
        $builder
            ->add('GradeComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Valider'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function examApplicantValidation(FormBuilderInterface $builder)
    {
        $builder
            ->add('GradeComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Valider'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function examUpdate(FormBuilderInterface $builder)
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

    /**
     * @param FormBuilderInterface $builder
     */
    private function examCandidateResult(FormBuilderInterface $builder)
    {
        $builder
            ->add('GradeStatus', ChoiceType::class, array('label' => 'Résultat : ', 'multiple' => false, 'expanded' => true, 'choices' => array('Refusé' => 3, 'Promu' => 4)))
            ->add('GradeCertificate', TextType::class, array('label' => 'N° Diplôme : ', 'required' => false))
            ->add('GradeComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Valider'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function examCandidateAikikai(FormBuilderInterface $builder)
    {
        $builder
            ->add('GradeCertificate', TextType::class, array('label' => 'N° certificat : '))
            ->add('GradeComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Valider'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function kyuAdd(FormBuilderInterface $builder)
    {
        $list = new ListData();

        $builder
            ->add('GradeRank', ChoiceType::class, array('label' => 'Grade : ', 'placeholder' => 'Choississez un grade', 'choices' => $list->getGradeKyu()))
            ->add('GradeDate', DateType::class, array('label' => 'Date : ', 'widget' => 'single_text'))
            ->add('GradeComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function kyuModify(FormBuilderInterface $builder)
    {
        $builder
            ->add('GradeDate', DateType::class, array('label' => 'Date : ', 'widget' => 'single_text'))
            ->add('GradeComment', TextareaType::class, array('label' => 'Commentaire : ', 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }
}
