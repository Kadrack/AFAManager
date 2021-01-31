<?php
// src/Entity/ClubDojo.php
namespace App\Entity;

use DateTimeInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="afamanager_club_dojo")
 * @ORM\Entity(repositoryClass="App\Repository\ClubDojoRepository")
 */
class ClubDojo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private ?int $club_dojo_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $club_dojo_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Assert\NotBlank()
     */
    private ?string $club_dojo_street;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @Assert\NotBlank()
     */
    private ?int $club_dojo_zip;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank()
     */
    private ?string $club_dojo_city;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $club_dojo_tatamis;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $club_dojo_dea;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTimeInterface $club_dojo_dea_formation;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $club_dojo_comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="club_dojos", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="club_dojo_join_club", referencedColumnName="club_id")
     */
    private ?Club $club_dojo_club;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ClubLesson", mappedBy="club_lesson_dojo", orphanRemoval=true, cascade={"persist"})
     */
    private $club_dojo_lessons;

    public function __construct()
    {
        $this->club_lessons = new ArrayCollection();
    }

    public function getClubDojoId(): ?int
    {
        return $this->club_dojo_id;
    }

    public function getClubDojoName(): ?string
    {
        return $this->club_dojo_name;
    }

    public function setClubDojoName(?string $club_dojo_name): self
    {
        $this->club_dojo_name = $club_dojo_name;

        return $this;
    }

    public function getClubDojoStreet(): ?string
    {
        return $this->club_dojo_street;
    }

    public function setClubDojoStreet(?string $club_dojo_street): self
    {
        $this->club_dojo_street = $club_dojo_street;

        return $this;
    }

    public function getClubDojoZip(): ?int
    {
        return $this->club_dojo_zip;
    }

    public function setClubDojoZip(?int $club_dojo_zip): self
    {
        $this->club_dojo_zip = $club_dojo_zip;

        return $this;
    }

    public function getClubDojoCity(): ?string
    {
        return $this->club_dojo_city;
    }

    public function setClubDojoCity(?string $club_dojo_city): self
    {
        $this->club_dojo_city = $club_dojo_city;

        return $this;
    }

    public function getClubDojoTatamis(): ?int
    {
        return $this->club_dojo_tatamis;
    }

    public function setClubDojoTatamis(?int $club_dojo_tatamis): self
    {
        $this->club_dojo_tatamis = $club_dojo_tatamis;

        return $this;
    }

    public function getClubDojoDEA(): ?bool
    {
        return $this->club_dojo_dea;
    }

    public function setClubDojoDEA(?bool $club_dojo_dea): self
    {
        $this->club_dojo_dea = $club_dojo_dea;

        return $this;
    }

    public function getClubDojoDEAFormation(): ?DateTimeInterface
    {
        return $this->club_dojo_dea_formation;
    }

    public function setClubDojoDEAFormation(?DateTimeInterface $club_dojo_dea_formation): self
    {
        $this->club_dojo_dea_formation = $club_dojo_dea_formation;

        return $this;
    }

    public function getClubDojoComment(): ?string
    {
        return $this->club_dojo_comment;
    }

    public function setClubDojoComment(?string $club_dojo_comment): self
    {
        $this->club_dojo_comment = $club_dojo_comment;

        return $this;
    }

    public function getClubDojoClub(): ?Club
    {
        return $this->club_dojo_club;
    }

    public function setClubDojoClub(?Club $club_dojo_club): self
    {
        $this->club_dojo_club = $club_dojo_club;

        return $this;
    }

    /**
     * @return Collection|ClubLesson[]
     */
    public function getClubDojoLessons(): Collection
    {
        return $this->club_lessons;
    }

    public function addClubDojoLessons(ClubLesson $club_dojo_lesson): self
    {
        if (!$this->club_dojo_lessons->contains($club_dojo_lesson)) {
            $this->club_dojo_lessons[] = $club_dojo_lesson;
            $club_dojo_lesson->setClubLessonDojo($this);
        }

        return $this;
    }

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
