<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The review stats for a resource.
 *
 *  * @ApiResource(
 *     normalizationContext={"groups"={"read"}},
 *     denormalizationContext={"groups"={"write"}}
 * )
 */
class Total
{
    /**
     * @var UuidInterface The UUID identifier of this resource
     *
     * @example e2984465-190a-4562-829e-a8cca81aa35d
     *
     * @Assert\Uuid
     * @Groups({"read"})
     */
    private $id;

    /**
     * @var string A specific commonground organisation that is being reviewd, e.g a single product
     *
     * @example https://wrc.zaakonline.nl/organisations/16353702-4614-42ff-92af-7dd11c8eef9f
     *
     * @Assert\NotNull
     * @Assert\Url
     * @Groups({"read", "write"})
     */
    private $organization;

    /**
     * @var string A specific commonground resource that is being reviewd, e.g a single product
     *
     * @example https://pdc.zaakonline.nl/products/16353702-4614-42ff-92af-7dd11c8eef9f
     *
     * @Assert\NotNull
     * @Assert\Url
     * @Groups({"read", "write"})
     */
    private $resource;

    /**
     * @var float The overall rating, based on a collection of ratings, of this review rounded to a single decimal.
     *
     * @example 5,5
     *
     * @Groups({"read"})
     */
    private $rating;

    /**
     * @var int The total amount of likes.
     *
     * @example 1000
     *
     * @Groups({"read"})
     */
    private $likes;

    /**
     * @var int The total amount of revieuws
     *
     * @example 5
     *
     * @Groups({"read"})
     */
    private $reviews;

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

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(int $likes): self
    {
        $this->likes = $likes;

        return $this;
    }

    public function getReviews(): ?int
    {
        return $this->revies;
    }

    public function setReviews(int $review): self
    {
        $this->reviews = $review;

        return $this;
    }
}
