<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * A rating of a single aspect within a review, there by allowing a person to rate for examle the color of a product on a numeric scale
 *
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}, "enable_max_depth"=true},
 *     denormalizationContext={"groups"={"write"}, "enable_max_depth"=true}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\RatingRepository")
 * @ApiFilter(OrderFilter::class, properties={"id","organization","itemReviewed","reviewer","aggregateRating"})
 * @ApiFilter(SearchFilter::class, properties={"organization": "exact","review.id": "exact","reviewer": "exact"})
 */
class Rating
{
	/**
	 * @var UuidInterface The UUID identifier of this resource
	 *
	 * @example e2984465-190a-4562-829e-a8cca81aa35d
	 *
	 * @Assert\Uuid
	 * @Groups({"read"})
	 * @ORM\Id
	 * @ORM\Column(type="uuid", unique=true)
	 * @ORM\GeneratedValue(strategy="CUSTOM")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	private $id;

    /**
     *
     * @Assert\NotNull
     * @Groups({"read","write"})
     * @MaxDepth(1)
     * @ORM\ManyToOne(targetEntity="App\Entity\Review", inversedBy="ratings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $review;

    /**
     * @var string $author A person or organisation from contacs component that posted this review (the desicion wheter or not this is gotten from an logedin user is up to bussness logic). Can be left empty for an annonamous review
     *
     * @example https://cc.zaakonline.nl/people/001a40e2-4662-4838-b774-3de874607bb6
     *
     * @Groups({"read"})
     */
    private $author;

    /**
     * @var integer The best posbile rating that could be given
     *
     * @example 10
     *
     * @Groups({"read"})
     */
    private $bestRating;

    /**
     * @var integer The worst posbile rating that could be given
     *
     * @example 1
     *
     * @Groups({"read"})
     */
    private $worstRating;

    /**
     * @var integer an explanation for the given rating
     * @example 5
     *
	 * @Assert\NotNull
     * @Assert\Length(
     *      max = 2
     * )
     * @Assert\Positive
     * @Groups({"read", "write"})
     * @ORM\Column(type="integer")
     */
    private $ratingValue;

    /**
     * @var string The rating given
     * @example I really like the color!
     *
     * @Assert\Length(
     *      max = 2255
     * )
     * @Groups({"read", "write"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $ratingExplanation;

    /**
     * The aspect of the item that is rated
     *
     * @MaxDepth(1)
     * @Groups({"read", "write"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Aspect", inversedBy="ratings")
     * @ORM\JoinColumn(nullable=true)
     */
    private $reviewAspect;

    /**
     * @var Datetime The moment this component was found by the crawler
     *
     * @Groups({"read"})
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var Datetime The last time this component was changed
     *
     * @Groups({"read"})
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function getId()
    {
        return $this->id;
    }

    public function getReview(): ?Review
    {
        return $this->review;
    }

    public function setReview(?Review $review): self
    {
        $this->review = $review;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->review->getAuthor();
    }

    public function getBestRating(): ?int
    {
    	return $this->reviewAspect->getBestRating();
    }

    public function getWorstRating(): ?int
    {
    	return $this->reviewAspect->getWorstRating();
    }

    public function getRatingValue(): ?int
    {
        return $this->ratingValue;
    }

    public function setRatingValue(int $ratingValue): self
    {
        $this->ratingValue = $ratingValue;

        return $this;
    }

    public function getRatingExplanation(): ?string
    {
        return $this->ratingExplanation;
    }

    public function setRatingExplanation(?string $ratingExplanation): self
    {
        $this->ratingExplanation = $ratingExplanation;

        return $this;
    }

    public function getReviewAspect(): ?Aspect
    {
        return $this->reviewAspect;
    }

    public function setReviewAspect(?Aspect $reviewAspect): self
    {
        $this->reviewAspect = $reviewAspect;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
    	return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
    	$this->createdAt = $createdAt;

    	return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
    	return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
    	$this->updatedAt = $updatedAt;

    	return $this;
    }
}
