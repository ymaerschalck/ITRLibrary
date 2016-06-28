<?php

namespace ITRLibraryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Post.
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="ITRLibraryBundle\Repository\PostRepository")
 * @UniqueEntity("title")
 * @UniqueEntity("url")
 */
class Post
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Url(
     *    checkDNS = true,
     *    protocols = {"http", "https"}
     * )
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="votes", type="float")
     */
    private $votes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="writtenAt", type="datetime", nullable=true)
     */
    private $writtenAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var Tag[]
     *
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="posts")
     * @ORM\JoinTable(
     *  name="post_tags",
     *  joinColumns={
     *      @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="tag_id", referencedColumnName="id")
     *  }
     * )
     */
    private $tags;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->votes = 1;
        $this->tags = new ArrayCollection();
    }

    public function vote($add)
    {
        $this->votes += $add;
    }

    public function getHash()
    {
        return md5($this->title);
    }

    public function tagList()
    {
        if (empty($this->tags)) {
            return false;
        }

        return implode(", ", $this->tags->toArray());
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Set url.
     *
     * @param string $url
     *
     * @return Post
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * @param string $votes
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set writtenAt.
     *
     * @param \DateTime $writtenAt
     *
     * @return Post
     */
    public function setWrittenAt($writtenAt)
    {
        $this->writtenAt = $writtenAt;

        return $this;
    }

    /**
     * Get writtenAt.
     *
     * @return \DateTime
     */
    public function getWrittenAt()
    {
        return $this->writtenAt;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Post
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return ArrayCollection|Tag[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param ArrayCollection|Tag[] $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }
}
