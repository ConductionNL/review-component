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
 * The act of expressing a positive sentiment about the resource. An author likes an resource (a proposition, topic or theme) with participants.
 *
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}, "enable_max_depth"=true},
 *     denormalizationContext={"groups"={"write"}, "enable_max_depth"=true},
 *     itemOperations={
 *          "get",
 *          "put",
 *          "delete",
 *          "get_change_logs"={
 *              "path"="/stages/{id}/change_log",
 *              "method"="get",
 *              "swagger_context" = {
 *                  "summary"="Changelogs",
 *                  "description"="Gets al the change logs for this resource"
 *              }
 *          },
 *          "get_audit_trail"={
 *              "path"="/stages/{id}/audit_trail",
 *              "method"="get",
 *              "swagger_context" = {
 *                  "summary"="Audittrail",
 *                  "description"="Gets the audit trail for this resource"
 *              }
 *          }
 *     },
 * )
 * @ORM\Table(name="likes")
 * @ORM\Entity(repositoryClass="App\Repository\LikeRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\ChangeLog")
 * 
 * @ApiFilter(OrderFilter::class)
 * @ApiFilter(DateFilter::class, strategy=DateFilter::EXCLUDE_NULL)
 * @ApiFilter(SearchFilter::class)
 */
class Like
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
	 * @var string $itemReviewed A specific commonground organisation that is being reviewd, e.g a single product
	 * @example https://wrc.zaakonline.nl/organisations/16353702-4614-42ff-92af-7dd11c8eef9f
	 *
	 * @Assert\NotNull
	 * @Assert\Url
	 * @Groups({"read", "write"})
	 * @ORM\Column(type="string", length=255)
	 */
	private $organization;
		
	/**
	 * @var string $resource A specific commonground resource that is being liked, e.g a single product
	 * @example pdc.zaakonline.nl/products/16353702-4614-42ff-92af-7dd11c8eef9f
	 *
	 * @Assert\NotNull
	 * @Assert\Url
	 * @Assert\Length(
	 *      max = 255
	 * )
	 * @Groups({"read", "write"})
	 * @ORM\Column(type="string", length=255,)
	 */
	private $resource;
	
	/**
	 * @var string author A person or organisation from contacs component that posted this like (the desicion wheter or not this is gotten from an logedin user is up to bussness logic)
	 * @example https://cc.zaakonline.nl/people/001a40e2-4662-4838-b774-3de874607bb6
	 *
	 * @Assert\NotNull
	 * @Assert\Url
	 * @Assert\Length(
	 *      max = 255
	 * )
	 * @Groups({"read", "write"})
	 * @ORM\Column(type="string", length=255)
	 */
	private $author;
	
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

    public function getId()
    {
        return $this->id;
    }
    
    public function getCreatedAt(): ?\DateTimeInterface
    {
    	return $this->createdAt;
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
    	$this->author = $author;
    	
    	return $this;
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
