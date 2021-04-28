<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PictureRepository;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=PictureRepository::class)
 * @Vich\Uploadable
 */
class Picture
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $filename;

    /**
     * @ORM\ManyToOne(targetEntity=Property::class, inversedBy="pictures")
     * @ORM\JoinColumn(nullable=false)
     */
    private $property;

    /**
     * @Vich\UploadableField(mapping="property_image", fileNameProperty="filename")
     * 
     * @var File|null
     * @Assert\Image(
     *      mimeTypes="image/jpeg"
     * )
     */
    private $imageFile;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getProperty(): ?Property
    {
        return $this->property;
    }

    public function setProperty(?Property $property): self
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get the value of imageFile
     *
     * @return  File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * Set the value of imageFile
     *
     * @param  File|null  $imageFile
     *
     * @return  self
     */ 
    public function setImageFile(?File $imageFile): self
    {
        $this->imageFile = $imageFile;
        return $this;
    }
}
