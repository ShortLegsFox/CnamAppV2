<?php

namespace App\Entity;

#[Entity]
#[Table('utilisateur')]
class Utilisateur
{
    #[id]
    #[Column, GeneratedValue]
    private int $id;

    #[Column(name: 'nom')]
    private string $nom;
    #[Column(name: 'prenom')]
    private string $prenom;
    #[Column(name: 'adresse')]
    private string $adresse;
    #[Column(name: 'codepostal')]
    private string $codepostal;
    #[Column(name: 'ville')]
    private string $ville;
    #[Column(name: 'email')]
    private string $email;
    #[Column(name: 'sexe')]
    private string $sexe;
    #[Column(name: 'login')]
    private string $login;
    #[Column(name: 'password')]
    private string $password;
    #[Column(name: 'telephone')]
    private string $telephone;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function getAdresse(): string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): void
    {
        $this->adresse = $adresse;
    }

    public function getCodepostal(): string
    {
        return $this->codepostal;
    }

    public function setCodepostal(string $codepostal): void
    {
        $this->codepostal = $codepostal;
    }

    public function getVille(): string
    {
        return $this->ville;
    }

    public function setVille(string $ville): void
    {
        $this->ville = $ville;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getSexe(): string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): void
    {
        $this->sexe = $sexe;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): void
    {
        $this->telephone = $telephone;
    }
}