<?php

namespace App\Entity;

use App\Form\InstructorFormType;
use DateTimeInterface;
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

    public function __construct()
    {
        parent::__construct(self::ROLE);
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
}
