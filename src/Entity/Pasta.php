<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Pasta
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\PastaRepository")
 */
class Pasta
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(type="string",length=32)
     */
    private $tid;
    /**
     * @ORM\Column(type="text")
     */
    private $content;
    /**
     * @ORM\Column(type="text")
     */
    private $stripped;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="pasta")
     */
    private $comments;
    /**
     * @ORM\Column(type="datetime")
     */
    private $timestamp;
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="liked")
     * @ORM\JoinTable("likes")
     */
    private $likes;
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Picture")
     * @ORM\JoinColumn(name="picture_id", referencedColumnName="id")
     */
    private $picture;
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tags", inversedBy="posts")
     * @ORM\JoinTable(name="posts_tags")
     */
    private $tags;
    /**
     * @ORM\Column(type="boolean")
     */
    private $deleted;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
        $this->stripped = preg_replace("/[^a-z0-9.]+/i", "", $content);
    }

    /**
     * @return mixed
     */
    public function getStripped()
    {
        return $this->stripped;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author): void
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param mixed $comments
     */
    public function setComments($comments): void
    {
        $this->comments = $comments;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp->format("Y-m-d H:i:s");
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return Collection
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * @param Collection $likes
     */
    public function setLikes(Collection $likes): void
    {
        $this->likes = $likes;
    }

    /**
     * @return mixed
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param mixed $picture
     */
    public function setPicture($picture): void
    {
        $this->picture = $picture;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param Collection $tags
     */
    public function setTags(Collection $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @return mixed
     */
    public function getDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @param mixed $deleted
     */
    public function setDeleted($deleted): void
    {
        $this->deleted = $deleted;
    }

    public function delete(): void
    {
        $this->deleted = true;
    }

    /**
     * @return mixed
     */
    public function getTid()
    {
        return $this->tid;
    }

    /**
     * @param mixed $tid
     */
    public function setTid(): void
    {
        $this->tid = $this->randomseed();
    }

    public function getLikesCount()
    {
        return $this->likes->count();
    }

    public function isLong()
    {
        return strlen($this->content) > 1200;
    }

    public function randomseed()
    {
        $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $str = "";
        for ($i = 0; $i < 32; $i++) {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $str;
    }


}