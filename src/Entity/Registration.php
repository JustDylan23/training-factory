<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RegistrationRepository")
 */
class Registration
{
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="App\Entity\Member", inversedBy="registrations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $member;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="App\Entity\Lesson", inversedBy="registrations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lesson;

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(?Member $member): self
    {
        $this->member = $member;

        return $this;
    }

    public function getLesson(): ?Lesson
    {
        return $this->lesson;
    }

    public function setLesson(?Lesson $lesson): self
    {
        $this->lesson = $lesson;

        return $this;
    }
}
