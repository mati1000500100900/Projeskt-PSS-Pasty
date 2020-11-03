<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 * @package App\Entity
 * @ORM\Entity()
 * @ORM\Table(name="app_users")
 */
class User implements UserInterface, \Serializable
{
    private $rolesarray=['ROLE_USER','ROLE_ADMIN'];
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(name="is_shadow", type="boolean")
     */
    private $isShadowbanned;
    /**
     * @ORM\Column(name="roles", type="integer")
     */
    private $roles;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Pasta", mappedBy="author")
     */
    private $posts;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Pasta", mappedBy="likes")
     */
    private $liked;

    public function __construct()
    {
        $this->isActive = true;
        $this->isShadowbanned = false;
    }

    /**
     * @return mixed
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function block(): void
    {
        $this->isActive = false;
    }

    /**
     * @return mixed
     */
    public function getIsShadowbanned(): bool
    {
        return $this->isShadowbanned;
    }

    /**
     * @param mixed $isShadowbanned
     */
    public function setIsShadowbanned($isShadowbanned): void
    {
        $this->isShadowbanned = $isShadowbanned;
    }

    public function getRoles(): array
    {
        $my_roles=[];
        if(!$this->isActive){
            array_push($my_roles,'ROLE_BANNED');
        }
        if($this->isShadowbanned){
            array_push($my_roles,'ROLE_SHADOW');
        }
        for($i=0;$i<count($this->rolesarray);$i++){
            if(($this->roles >> $i)&1){
                array_push($my_roles,$this->rolesarray[$i]);
            }
        }
        return $my_roles;
    }

    public function setRoles($roles): void
    {
        $this->roles=$roles;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword($password): void
    {
        $this->password=password_hash($password, PASSWORD_BCRYPT, ['cost'=>12]);
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    public function eraseCredentials()
    {
        return null;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }

    /**
     * @return mixed
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param mixed $posts
     */
    public function setPosts($posts): void
    {
        $this->posts = $posts;
    }

    /**
     * @return mixed
     */
    public function getLiked()
    {
        return $this->liked;
    }

    /**
     * @param mixed $liked
     */
    public function setLiked($liked): void
    {
        $this->liked = $liked;
    }


}