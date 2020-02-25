<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SessionRepository")
 */
class Session
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=4096)
     */
    private $data = '{}';

    private $dataObjGetFromData = false;

    /**
     * @var array
     */
    private $dataObj = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $exp;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status = '1';

    /**
     * @ORM\Column(type="integer")
     */
    private $userId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $clientFrom;

    public function getId(): ?int
    {
        return $this->id;
    }

    private function getData(): ?string
    {
        return $this->data;
    }

    private function setData(string $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getExp(): ?string
    {
        return $this->exp;
    }

    public function setExp(string $exp): self
    {
        $this->exp = $exp;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
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

    public function getClientFrom(): ?string
    {
        return $this->clientFrom;
    }

    public function setClientFrom(string $clientFrom): self
    {
        $this->clientFrom = $clientFrom;

        return $this;
    }

    public function setDataObj($key, string $value = ''): self
    {
        $this->dataObj[$key] = $value;
        $this->data = json_encode($this->dataObj);
        return $this;
    }

    public function getDataObjValue($key): string
    {
        if ($this->dataObjGetFromData === 0) {
            $this->dataObjGetFromData = 1;
            $this->dataObj = json_decode($this->data);
            if ($this->dataObj) {
                $this->dataObj = [];
            }
        }
        return $this->dataObj[$key] ?? '';
    }
}
