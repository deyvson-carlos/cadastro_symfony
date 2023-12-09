<?php

namespace App\Entity;

use App\Repository\CadastroPessoasRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cadastro_pessoas")
 */
class CadastroPessoas
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nome;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tipoPessoa;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $documento;

    /**
     * @ORM\Column(type="date")
     */
    private $dataNascimento;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $deletado = false;

    // Métodos get e set

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getTipoPessoa(): ?string
    {
        return $this->tipoPessoa;
    }

    public function getDeletado(): ?bool
    {
        return $this->deletado;
    }

    public function setTipoPessoa(string $tipoPessoa): self
    {
        $this->tipoPessoa = $tipoPessoa;

        return $this;
    }

    public function getDocumento(): ?string
    {
        return $this->documento;
    }

    public function setDocumento(string $documento): self
    {
        $this->documento = $documento;

        return $this;
    }

    public function getDataNascimento(): ?\DateTimeInterface
    {
        return $this->dataNascimento;
    }

    public function setDataNascimento(\DateTimeInterface $dataNascimento): self
    {
        $this->dataNascimento = $dataNascimento;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function setDeletado(bool $deletado): self
    {
        $this->deletado = $deletado;

        return $this;
    }

    public function isDeletado(): bool
    {
        return $this->deletado;
    }
}