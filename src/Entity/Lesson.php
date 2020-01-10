<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LessonRepository")
 */
class Lesson
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Assert\NotBlank()
     */
    private $time;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    private $location;

    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank()
     */
    private $maxPeople;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Training", inversedBy="lessons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $training;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Instructor", inversedBy="lessons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $instructor;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Registration", mappedBy="lesson", orphanRemoval=true)
     */
    private $registrations;

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTime(): ?DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(?DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getMaxPeople(): ?int
    {
        return $this->maxPeople;
    }

    public function setMaxPeople(?int $maxPeople): self
    {
        $this->maxPeople = $maxPeople;

        return $this;
    }

    public function getTraining(): ?training
    {
        return $this->training;
    }

    public function setTraining(?training $training): self
    {
        $this->training = $training;

        return $this;
    }

    public function getInstructor(): ?Instructor
    {
        return $this->instructor;
    }

    public function setInstructor(?Instructor $instructor): self
    {
        $this->instructor = $instructor;

        return $this;
    }

    /**
     * @return Collection|Registration[]
     */
    public function getRegistrations(): Collection
    {
        return $this->registrations;
    }

    public function addRegistration(Registration $registration): self
    {
        if (!$this->registrations->contains($registration)) {
            $this->registrations[] = $registration;
            $registration->setLesson($this);
        }

        return $this;
    }

    public function removeRegistration(Registration $registration): self
    {
        if ($this->registrations->contains($registration)) {
            $this->registrations->removeElement($registration);
            // set the owning side to null (unless already changed)
            if ($registration->getLesson() === $this) {
                $registration->setLesson(null);
            }
        }

        return $this;
    }
}
