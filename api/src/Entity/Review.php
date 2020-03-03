<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * A review of an item - for example, of a restaurant, movie, or store.
 *
 * @ApiResource(itemOperations={
 * 		"get",
 * 	    "put",
 * 	    "delete",
 *      "calculate_total"={
 *         "method"="GET",
 *         "path"="/item_total}",
 *         "controller"=DefaultController::class,
 *     		}
 * 		},
 *     normalizationContext={"groups"={"read"}, "enable_max_depth"=true},
 *     denormalizationContext={"groups"={"write"}, "enable_max_depth"=true}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\ReviewRepository")
 * @ApiFilter(OrderFilter::class, properties={"id","organization","resource","author","aggregateRating"})
 * @ApiFilter(SearchFilter::class, properties={"organization": "exact","resource": "exact","author": "exact"})
 * @ApiFilter(DateFilter::class, properties={"dateCreated","dateModified" })
 */
class Review
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
	 * @var string $resource A specific commonground organisation that is being reviewd, e.g a single product
     * @example https://wrc.zaakonline.nl/organisations/16353702-4614-42ff-92af-7dd11c8eef9f
	 *
	 * @Assert\NotNull
	 * @Assert\Url
	 * @Groups({"read", "write"})
	 * @ORM\Column(type="string", length=255)
	 */
	private $organization;


    /**
     * @var string $resource A specific commonground resource that is being reviewd, e.g a single product
     * @example https://pdc.zaakonline.nl/products/16353702-4614-42ff-92af-7dd11c8eef9f
     *
	 * @Assert\NotNull
     * @Assert\Url
     * @Groups({"read", "write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resource;

    /**
     * @var string $author A person or organisation from contacs component that posted this review (the desicion wheter or not this is gotten from an logedin user is up to bussness logic). Can be left empty for an annonamous review
     * @example https://cc.zaakonline.nl/people/001a40e2-4662-4838-b774-3de874607bb6
     *
     * @Assert\Url
     * @Assert\Length(
     *      max = 255
     * )
     * @Groups({"read", "write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $author;

    /**
     * @MaxDepth(1)
     * @Groups({"read", "write"})
     * @ORM\OneToMany(targetEntity="App\Entity\Rating", mappedBy="review", orphanRemoval=true,cascade={"persist"})
     */
    private $ratings;

    /**
     * @var float The overall rating, based on a collection of ratings, of this review rounded to a single decimal.
     * @example 5,5
     *
     * @Groups({"read"})
     */
    private $aggregateRating;
    
    /**
     * @var Datetime $dateCreated The moment this request was created
     *
     * @Groups({"read"})
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCreated;
    
    /**
     * @var Datetime $dateModified  The moment this request last Modified
     *
     * @Groups({"read"})
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateModified;

    public function __construct()
    {
        $this->ratings = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getOrganization(): ?string
    {
        return $this->organization;
    }

    public function setOrganization(string $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    public function getResource(): ?string
    {
        return $this->resource;
    }

    public function setResource(string $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    public function getAuthor(): ?string
    {
    	return $this->author;
    }

    public function setAuthor(string $author): self
    {
    	$this->author= $author;

        return $this;
    }

    /**
     * @return Collection|Rating[]
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): self
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings[] = $rating;
            $rating->setReview($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): self
    {
        if ($this->ratings->contains($rating)) {
            $this->ratings->removeElement($rating);
            // set the owning side to null (unless already changed)
            if ($rating->getReview() === $this) {
                $rating->setReview(null);
            }
        }

        return $this;
    }

    public function getAggregateRating(): ?float
    {
    	// Lets get al the rating values
    	$ratingArray = [];
    	foreach($this->ratings as $rating){
    		$ratingArray[] = $rating->getRatingValue();
    	}
    	if(count($ratingArray) > 0) {
            // Lets calculate the avarage
            $aggregate = array_sum($ratingArray) / count($ratingArray);
        }
    	else{
    	    $aggregate = 0;
        }
    	// Round to one decimal (round down making 1.55 into 1 . 1.5)
    	return round($aggregate, 1, PHP_ROUND_HALF_DOWN);
    }
    
    public function getDateCreated(): ?\DateTimeInterface
    {
    	return $this->dateCreated;
    }
    
    public function setDateCreated(\DateTimeInterface $dateCreated): self
    {
    	$this->dateCreated= $dateCreated;
    	
    	return $this;
    }
    
    public function getDateModified(): ?\DateTimeInterface
    {
    	return $this->dateModified;
    }
    
    public function setDateModified(\DateTimeInterface $dateModified): self
    {
    	$this->dateModified = $dateModified;
    	
    	return $this;
    }
}
