<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

class Contact
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=3,
     *     max=50,
     *     minMessage="Votre prénom doit faire au minimum {{ limit }} caractères",
     *     maxMessage="Votre prénom doit faire au maximum {{ limit }} caractères"
     * )
     */
    public $firstName;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=3,
     *     max=50,
     *     minMessage="Votre nom doit faire au minimum {{ limit }} caractères",
     *     maxMessage="Votre nom doit faire au maximum {{ limit }} caractères"
     * )
     */
    public $lastName;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=5,
     *     max=300,
     *     minMessage="Votre message doit faire au minimum {{ limit }} caractères",
     *     maxMessage="Votre message doit faire au maximum {{ limit }} caractères"
     * )
     */
    public $message;

    /**
     * @return mixed
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     * @return Contact
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     * @return Contact
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return Contact
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     * @return Contact
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }
}
