<?php
// src/Entity/TrainingAttendance.php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="afamanager_training_attendance")
 * @ORM\Entity(repositoryClass="App\Repository\TrainingAttendanceRepository")
 */
class TrainingAttendance
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $training_attendance_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $training_attendance_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $training_attendance_unique;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $training_attendance_sex;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $training_attendance_country;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $training_attendance_payment;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $training_attendance_payment_type;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $training_attendance_comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Training", inversedBy="training_attendances", cascade={"persist"})
     * @ORM\JoinColumn(name="training_attendance_join_training", referencedColumnName="training_id")
     */
    private $training;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Member", inversedBy="member_training_attendances", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="training_attendance_join_member", referencedColumnName="member_id")
     */
    private $training_attendance_member;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainingSession", inversedBy="training_session_attendances", cascade={"persist"})
     * @ORM\JoinColumn(name="training_attendance_join_training_session", referencedColumnName="training_session_id")
     */
    private $training_attendance_session;

    public function getTrainingAttendanceId(): ?int
    {
        return $this->training_attendance_id;
    }

    public function setTrainingAttendanceId(?int $training_attendance_id): self
    {
        $this->training_attendance_id = $training_attendance_id;

        return $this;
    }

    public function getTrainingAttendanceName(): ?string
    {
        return $this->training_attendance_name;
    }

    public function setTrainingAttendanceName(?string $training_attendance_name): self
    {
        $this->training_attendance_name = $training_attendance_name;

        return $this;
    }

    public function getTrainingAttendanceUnique(): ?string
    {
        return $this->training_attendance_unique;
    }

    public function setTrainingAttendanceUnique(?string $training_attendance_unique): self
    {
        $this->training_attendance_unique = $training_attendance_unique;

        return $this;
    }

    public function getTrainingAttendanceSex(): ?int
    {
        return $this->training_attendance_sex;
    }

    public function setTrainingAttendanceSex(?int $training_attendance_sex): self
    {
        $this->training_attendance_sex = $training_attendance_sex;

        return $this;
    }

    public function getTrainingAttendanceCountry(): ?string
    {
        return $this->training_attendance_country;
    }

    public function setTrainingAttendanceCountry(?string $training_attendance_country): self
    {
        $this->training_attendance_country = $training_attendance_country;

        return $this;
    }

    public function getTrainingAttendancePayment(): ?int
    {
        return $this->training_attendance_payment;
    }

    public function setTrainingAttendancePayment(int $training_attendance_payment): self
    {
        $this->training_attendance_payment = $training_attendance_payment;

        return $this;
    }

    public function getTrainingAttendancePaymentType(): ?int
    {
        return $this->training_attendance_payment_type;
    }

    public function setTrainingAttendancePaymentType(int $training_attendance_payment_type): self
    {
        $this->training_attendance_payment_type = $training_attendance_payment_type;

        return $this;
    }

    public function getTrainingAttendanceComment(): ?string
    {
        return $this->training_attendance_comment;
    }

    public function setTrainingAttendanceComment(?string $training_attendance_comment): self
    {
        $this->training_attendance_comment = $training_attendance_comment;

        return $this;
    }

    public function getTraining(): ?Training
    {
        return $this->training;
    }

    public function setTraining(?Training $training): self
    {
        $this->training = $training;

        return $this;
    }

    public function getTrainingAttendanceMember(): ?Member
    {
        return $this->training_attendance_member;
    }

    public function setTrainingAttendanceMember(?Member $member): self
    {
        $this->training_attendance_member = $member;

        return $this;
    }

    public function getTrainingAttendanceSession(): ?TrainingSession
    {
        return $this->training_attendance_session;
    }

    public function setTrainingAttendanceSession(?TrainingSession $training_session): self
    {
        $this->training_attendance_session = $training_session;

        return $this;
    }
}
