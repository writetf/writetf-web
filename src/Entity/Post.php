<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 * @ORM\Table(name="post")
 */
class Post implements EntityInterface
{
    /**
     * Use constants to define configuration options that rarely change instead
     * of specifying them under parameters section in config/services.yaml file.
     *
     * See https://symfony.com/doc/current/best_practices/configuration.html#constants-vs-configuration-options
     */
    public const NUM_ITEMS = 30;

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="App\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     * @Assert\Length(min=5)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $publishedAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $commentedAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $latestActivityAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @var Community
     *
     * @ORM\ManyToOne(targetEntity="Community", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $community;

    /**
     * @var Comment[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Comment",
     *      mappedBy="post",
     *      orphanRemoval=true,
     *      cascade={"persist"}
     * )
     * @ORM\OrderBy({"publishedAt": "ASC"})
     */
    private $comments;

    /**
     * @var View[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="View",
     *      mappedBy="post",
     *      orphanRemoval=true,
     *      cascade={"persist"}
     * )
     */
    private $views;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(min=5, max=255)
     */
    private $thumbnail;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notification", mappedBy="post", orphanRemoval=true)
     */
    private $notifications;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(min=5, max=255)
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(min=5, max=255)
     */
    private $video;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Love", mappedBy="post")
     */
    private $love;

    protected $titleDisplay;

    protected $descriptionDisplay;

    public function __construct()
    {
        $this->publishedAt = new DateTime();
        $this->comments = new ArrayCollection();
        $this->views = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->latestActivityAt = $this->publishedAt;
        $this->love = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    public function getSummary(): ?string
    {
        $clear = strip_tags($this->content);
        $clear = html_entity_decode($clear);
        $clear = preg_replace('/ +/', ' ', $clear);
        $clear = preg_replace('/`+/', '', $clear);

        return trim($clear);
    }

    /**
     * @return DateTime
     */
    public function getPublishedAt(): DateTime
    {
        return $this->publishedAt;
    }

    /**
     * @param DateTime $publishedAt
     */
    public function setPublishedAt(DateTime $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return DateTime
     */
    public function getCommentedAt(): ?DateTime
    {
        return $this->commentedAt;
    }

    /**
     * @param DateTime $commentedAt
     */
    public function setCommentedAt(DateTime $commentedAt): void
    {
        $this->commentedAt = $commentedAt;
    }

    /**
     * @return DateTime
     */
    public function getLatestActivityAt(): ?DateTime
    {
        return $this->latestActivityAt;
    }

    /**
     * @param DateTime $latestActivityAt
     */
    public function setLatestActivityAt(DateTime $latestActivityAt): void
    {
        $this->latestActivityAt = $latestActivityAt;
    }

    /**
     * @return User
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    /**
     * @return Community
     */
    public function getCommunity(): ?Community
    {
        return $this->community;
    }

    /**
     * @param Community $community
     */
    public function setCommunity(Community $community): void
    {
        $this->community = $community;
    }

    /**
     * @return Comment[]|ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param Comment[]|ArrayCollection $comments
     */
    public function setComments($comments): void
    {
        $this->comments = $comments;
    }

    public function addComment(Comment $comment): void
    {
        $comment->setPost($this);
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
        }
    }

    public function removeComment(Comment $comment): void
    {
        $this->comments->removeElement($comment);
    }

    /**
     * @return View[]|ArrayCollection
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @param View[]|ArrayCollection $views
     */
    public function setViews($views): void
    {
        $this->views = $views;
    }

    /**
     * @return mixed
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * @param mixed $notifications
     */
    public function setNotifications($notifications): void
    {
        $this->notifications = $notifications;
    }

    /**
     * @return string
     */
    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    /**
     * @param string $thumbnail
     */
    public function setThumbnail(string $thumbnail): void
    {
        $this->thumbnail = $thumbnail;
    }

    public function getLatestComment()
    {
        return $this->comments->last();
    }

    public function getTitleDisPlay()
    {
        $title = $this->getTitle();
        $title = html_entity_decode($title);
        $breaks = array("<br />", "<br>", "<br/>");
        $title = str_ireplace($breaks, "\n", $title);
        $title = preg_replace('#</[^>]+>#', "\n", $title);
        $title = preg_replace('#<[^>]+>#', " ", $title);
        $titleDisplay = strtok($title, "\n");
        $titleDisplay = trim($titleDisplay);
        $descriptionDisplay = preg_replace('/\s+/', ' ', $title);
        $descriptionDisplay = str_replace($titleDisplay, '', $descriptionDisplay);
        if (strlen($titleDisplay) > 57) {
            $titleDisplay = trim(mb_substr($titleDisplay, 0, 57)) . '...';
        }
        if (strlen($descriptionDisplay) > 160) {
            $descriptionDisplay = trim(mb_substr($descriptionDisplay, 0, 160)) . '...';
        } else {
            $communityInText =  !empty($this->getCommunity()) ?? ' in ' . $this->getCommunity()->getDisplayText();
            $descriptionDisplay = 'View more posts of ' . $this->getAuthor()->getDisplayText(
                ) . $communityInText . ' at Writetf.com';
        }
        $this->descriptionDisplay = $descriptionDisplay;
        $this->titleDisplay = $titleDisplay;

        return $this->titleDisplay;
    }

    public function getDescriptionDisplay()
    {
        return $this->descriptionDisplay;
    }

    /**
     * @return Collection|Love[]
     */
    public function getLove(): ?Collection
    {
        return $this->love;
    }

    public function addLove(Love $love): self
    {
        if (!$this->love->contains($love)) {
            $this->love[] = $love;
            $love->setPost($this);
        }

        return $this;
    }

    public function removeLove(Love $love): self
    {
        if ($this->love->contains($love)) {
            $this->love->removeElement($love);
            // set the owning side to null (unless already changed)
            if ($love->getPost() === $this) {
                $love->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getVideo(): string
    {
        return $this->video;
    }

    /**
     * @param string $video
     */
    public function setVideo(string $video): void
    {
        $this->video = $video;
    }
}
