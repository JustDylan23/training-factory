<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdminRepository")
 */
class Admin extends User
{
    const ROLE = 'ROLE_ADMIN';

    public function __construct()
    {
        parent::__construct(self::ROLE);
    }
}
