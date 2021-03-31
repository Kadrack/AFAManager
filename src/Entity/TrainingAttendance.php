<?php
// src/Entity/TrainingAttendance.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class TrainingAttendance
 * @package App\Entity
 *
 * @ORM\Table(name="afamanager_training_attendance")
 * @ORM\Entity(repositoryClass="App\Repository\TrainingAttendanceRepository")
 */
class TrainingAttendance
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $training_attendance_id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $training_attendance_name;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $training_attendance_unique;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $training_attendance_sex;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $training_attendance_country;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $training_attendance_payment;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $training_attendance_payment_type;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $training_attendance_comment;

    /**
     * @var Training|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Training", inversedBy="training_attendances", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="training_attendance_join_training", referencedColumnName="training_id")
     */
    private ?Training $training;

    /**
     * @var Member|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Member", inversedBy="member_training_attendances", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="training_attendance_join_member", referencedColumnName="member_id")
     */
    private ?Member $training_attendance_member;

    /**
     * @var TrainingSession|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainingSession", inversedBy="training_session_attendances", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="training_attendance_join_training_session", referencedColumnName="training_session_id")
     */
    private ?TrainingSession $training_attendance_session;

    /**
     * @return int
     */
    public function getTrainingAttendanceId(): int
    {
        return $this->training_attendance_id;
    }

    /**
     * @param int $training_attendance_id
     * @return $this
     */
    public function setTrainingAttendanceId(int $training_attendance_id): self
    {
        $this->training_attendance_id = $training_attendance_id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTrainingAttendanceName(): ?string
    {
        return $this->training_attendance_name;
    }

    /**
     * @param string|null $training_attendance_name
     * @return $this
     */
    public function setTrainingAttendanceName(?string $training_attendance_name): self
    {
        $this->training_attendance_name = $training_attendance_name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTrainingAttendanceUnique(): ?string
    {
        return $this->training_attendance_unique;
    }

    /**
     * @param string|null $training_attendance_unique
     * @return $this
     */
    public function setTrainingAttendanceUnique(?string $training_attendance_unique): self
    {
        $this->training_attendance_unique = $training_attendance_unique;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTrainingAttendanceSex(): ?int
    {
        return $this->training_attendance_sex;
    }

    /**
     * @param int|null $training_attendance_sex
     * @return $this
     */
    public function setTrainingAttendanceSex(?int $training_attendance_sex): self
    {
        $this->training_attendance_sex = $training_attendance_sex;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTrainingAttendanceCountry(): ?string
    {
        return $this->training_attendance_country;
    }

    /**
     * @param string|null $training_attendance_country
     * @return $this
     */
    public function setTrainingAttendanceCountry(?string $training_attendance_country): self
    {
        $this->training_attendance_country = $training_attendance_country;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTrainingAttendancePayment(): ?int
    {
        return $this->training_attendance_payment;
    }

    /**
     * @param int|null $training_attendance_payment
     * @return $this
     */
    public function setTrainingAttendancePayment(?int $training_attendance_payment): self
    {
        $this->training_attendance_payment = $training_attendance_payment;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTrainingAttendancePaymentType(): ?int
    {
        return $this->training_attendance_payment_type;
    }

    /**
     * @param int|null $training_attendance_payment_type
     * @return $this
     */
    public function setTrainingAttendancePaymentType(?int $training_attendance_payment_type): self
    {
        $this->training_attendance_payment_type = $training_attendance_payment_type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTrainingAttendanceComment(): ?string
    {
        return $this->training_attendance_comment;
    }

    /**
     * @param string|null $training_attendance_comment
     * @return $this
     */
    public function setTrainingAttendanceComment(?string $training_attendance_comment): self
    {
        $this->training_attendance_comment = $training_attendance_comment;

        return $this;
    }

    /**
     * @return Training|null
     */
    public function getTraining(): ?Training
    {
        return $this->training;
    }

    /**
     * @param Training|null $training
     * @return $this
     */
    public function setTraining(?Training $training): self
    {
        $this->training = $training;

        return $this;
    }

    /**
     * @return Member|null
     */
    public function getTrainingAttendanceMember(): ?Member
    {
        return $this->training_attendance_member;
    }

    /**
     * @param Member|null $member
     * @return $this
     */
    public function setTrainingAttendanceMember(?Member $member): self
    {
        $this->training_attendance_member = $member;

        return $this;
    }

    /**
     * @return TrainingSession|null
     */
    public function getTrainingAttendanceSession(): ?TrainingSession
    {
        return $this->training_attendance_session;
    }

    /**
     * @param TrainingSession|null $training_session
     * @return $this
     */
    public function setTrainingAttendanceSession(?TrainingSession $training_session): self
    {
        $this->training_attendance_session = $training_session;

        return $this;
    }
}
