<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InstructorRepository")
 */
class Instructor extends User
{
    const ROLE = 'ROLE_INSTRUCTOR';

    /**
     * @ORM\Column(type="date")
     */
    private $hiringDate;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private $salary;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Lesson", mappedBy="instructor")
     */
    private $lessons;

    public function __construct()
    {
        parent::__construct(self::ROLE);
        $this->lessons = new ArrayCollection();
    }

    public function getHiringDate(): ?DateTimeInterface
    {
        return $this->hiringDate;
    }

    public function setHiringDate(DateTimeInterface $hiringDate): self
    {
        $this->hiringDate = $hiringDate;

        return $this;
    }

    public function getSalary(): ?string
    {
        return $this->salary;
    }

    public function setSalary(string $salary): self
    {
        $this->salary = $salary;

        return $this;
    }

    /**
     * @return Collection|Lesson[]
     */
    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    public function addLesson(Lesson $lesson): self
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons[] = $lesson;
            $lesson->setInstructor($this);
        }

        return $this;
    }

    public function removeLesson(Lesson $lesson): self
    {
        if ($this->lessons->contains($lesson)) {
            $this->lessons->removeElement($lesson);
            // set the owning side to null (unless already changed)
            if ($lesson->getInstructor() === $this) {
                $lesson->setInstructor(null);
            }
        }

        return $this;
    }
}
