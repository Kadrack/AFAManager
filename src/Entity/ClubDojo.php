<?php
// src/Entity/ClubDojo.php
namespace App\Entity;

use DateTime;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ClubDojo
 * @package App\Entity
 *
 * @ORM\Table(name="afamanager_club_dojo")
 * @ORM\Entity(repositoryClass="App\Repository\ClubDojoRepository")
 */
class ClubDojo
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $club_dojo_id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $club_dojo_name;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Assert\NotBlank]
    private ?string $club_dojo_street;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    #[Assert\NotBlank]
    private ?int $club_dojo_zip;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Assert\NotBlank]
    private ?string $club_dojo_city;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $club_dojo_tatamis;

    /**
     * @var bool|null
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $club_dojo_dea;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $club_dojo_dea_formation;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $club_dojo_comment;

    /**
     * @var Club|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="club_dojos", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="club_dojo_join_club", referencedColumnName="club_id")
     */
    private ?Club $club_dojo_club;

    /**
     * @var Collection|ArrayCollection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\ClubLesson", mappedBy="club_lesson_dojo", orphanRemoval=true, cascade={"persist"})
     */
    private Collection|ArrayCollection|null $club_dojo_lessons;

    /**
     * ClubDojo constructor.
     */
    public function __construct()
    {
        $this->club_dojo_lessons = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getClubDojoId(): int
    {
        return $this->club_dojo_id;
    }

    /**
     * @return string|null
     */
    public function getClubDojoName(): ?string
    {
        return $this->club_dojo_name;
    }

    /**
     * @param string|null $club_dojo_name
     * @return $this
     */
    public function setClubDojoName(?string $club_dojo_name): self
    {
        $this->club_dojo_name = $club_dojo_name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubDojoStreet(): ?string
    {
        return $this->club_dojo_street;
    }

    /**
     * @param string|null $club_dojo_street
     * @return $this
     */
    public function setClubDojoStreet(?string $club_dojo_street): self
    {
        $this->club_dojo_street = $club_dojo_street;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClubDojoZip(): ?int
    {
        return $this->club_dojo_zip;
    }

    /**
     * @param int|null $club_dojo_zip
     * @return $this
     */
    public function setClubDojoZip(?int $club_dojo_zip): self
    {
        $this->club_dojo_zip = $club_dojo_zip;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubDojoCity(): ?string
    {
        return $this->club_dojo_city;
    }

    /**
     * @param string|null $club_dojo_city
     * @return $this
     */
    public function setClubDojoCity(?string $club_dojo_city): self
    {
        $this->club_dojo_city = $club_dojo_city;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClubDojoTatamis(): ?int
    {
        return $this->club_dojo_tatamis;
    }

    /**
     * @param int|null $club_dojo_tatamis
     * @return $this
     */
    public function setClubDojoTatamis(?int $club_dojo_tatamis): self
    {
        $this->club_dojo_tatamis = $club_dojo_tatamis;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getClubDojoDEA(): ?bool
    {
        return $this->club_dojo_dea;
    }

    /**
     * @param bool|null $club_dojo_dea
     * @return $this
     */
    public function setClubDojoDEA(?bool $club_dojo_dea): self
    {
        $this->club_dojo_dea = $club_dojo_dea;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getClubDojoDEAFormation(): ?DateTime
    {
        return $this->club_dojo_dea_formation;
    }

    /**
     * @param DateTime|null $club_dojo_dea_formation
     * @return $this
     */
    public function setClubDojoDEAFormation(?DateTime $club_dojo_dea_formation): self
    {
        $this->club_dojo_dea_formation = $club_dojo_dea_formation;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubDojoComment(): ?string
    {
        return $this->club_dojo_comment;
    }

    /**
     * @param string|null $club_dojo_comment
     * @return $this
     */
    public function setClubDojoComment(?string $club_dojo_comment): self
    {
        $this->club_dojo_comment = $club_dojo_comment;

        return $this;
    }

    /**
     * @return Club|null
     */
    public function getClubDojoClub(): ?Club
    {
        return $this->club_dojo_club;
    }

    /**
     * @param Club|null $club_dojo_club
     * @return $this
     */
    public function setClubDojoClub(?Club $club_dojo_club): self
    {
        $this->club_dojo_club = $club_dojo_club;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getClubDojoLessons(): Collection
    {
        return $this->club_dojo_lessons;
    }

    /**
     * @param ClubLesson $club_dojo_lesson
     * @return $this
     */
    public function addClubDojoLessons(ClubLesson $club_dojo_lesson): self
    {
        if (!$this->club_dojo_lessons->contains($club_dojo_lesson)) {
            $this->club_dojo_lessons[] = $club_dojo_lesson;
            $club_dojo_lesson->setClubLessonDojo($this);
        }

        return $this;
    }

    /**
     * @param ClubLesson $club_dojo_lesson
     * @return $this
     */
    public function removeClubDojoLessons(ClubLesson $club_dojo_lesson): self
    {
        if ($this->club_dojo_lessons->contains($club_dojo_lesson)) {
            $this->club_dojo_lessons->removeElement($club_dojo_lesson);
            // set the owning side to null (unless already changed)
            if ($club_dojo_lesson->getClubLessonDojo() === $this) {
                $club_dojo_lesson->setClubLessonDojo(null);
            }
        }

        return $this;
    }
}
