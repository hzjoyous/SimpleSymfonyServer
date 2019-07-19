<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FitnessStatisticsRepository")
 */
class FitnessStatistics
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $userId;

    /**
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @ORM\Column(type="string")
     */
    private $fitnessDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $fitnessProgram;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getFitnessDate(): ?string
    {
        return $this->fitnessDate;
    }

    public function setFitnessDate(string $fitnessDate): self
    {
        $this->fitnessDate = $fitnessDate;

        return $this;
    }

    public function getFitnessProgram(): ?int
    {
        return $this->fitnessProgram;
    }

    public function setFitnessProgram(int $fitnessProgram): self
    {
        $this->fitnessProgram = $fitnessProgram;

        return $this;
    }
}
