<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * OrderHeader
 *
 * @ORM\Table(name="order_header")
 * @ORM\Entity(repositoryClass="App\Repository\OrderHeaderRepository")
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"order_header_read", "read", "is_active_read"}},
 *         "denormalization_context"={"groups"={"order_header_write", "is_active_write"}},
 *         "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"={
 *                  "groups"={"order_header_read_collection", "read", "is_active_read"}
 *              },
 *              "security"="is_granted('ROLE_ORDER_HEADER_LIST')"
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_ORDER_HEADER_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_ORDER_HEADER_SHOW')"
 *          },
 *          "put"={
 *              "security"="is_granted('ROLE_ORDER_HEADER_UPDATE')"
 *          },
 *          "delete"={
 *              "security"="is_granted('ROLE_ORDER_HEADER_DELETE')"
 *          }
 *     }
 * )
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "number": "exact",
 *     "client.name": "ipartial",
 *     "status.name": "ipartial",
 *     "currency.name": "ipartial",
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "number",
 *          "client.name",
 *          "status.name",
 *          "currency.name",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
class OrderHeader
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @Groups({
     *     "order_header_read",
     *     "invoice_header_read",
     *     "invoice_header_read",
     *     "invoice_header_write",
     *     "order_header_read_collection"
     * })
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({
     *     "order_header_read",
     *     "order_header_write",
     *     "invoice_header_read",
     *     "invoice_header_read",
     *     "order_header_read_collection"
     * })
     * @Assert\NotBlank()
     */
    private string $number;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\OrderStatus")
     * @Groups({
     *     "order_header_read",
     *     "order_header_write",
     *     "order_header_read_collection"
     * })
     * @Assert\NotNull()
     */
    private ?OrderStatus $status = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Channel")
     * @Groups({
     *     "order_header_read",
     *     "order_header_write",
     *     "order_header_read_collection"
     * })
     * @Assert\NotNull()
     */
    private ?Channel $channel = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client")
     * @Groups({
     *     "order_header_read",
     *     "order_header_write",
     *     "order_header_read_collection"
     * })
     * @Assert\NotNull()
     */
    private ?Client $client = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PaymentType")
     * @Groups({
     *     "order_header_read",
     *     "order_header_write",
     *     "order_header_read_collection"
     * })
     * @Assert\NotNull()
     */
    private ?PaymentType $paymentType = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ShipmentMethod")
     * @Groups({
     *     "order_header_read",
     *     "order_header_write",
     *     "order_header_read_collection"
     * })
     * @Assert\NotNull()
     */
    private ?ShipmentMethod $shipmentMethod = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderLine", mappedBy="header", cascade={"persist"})
     * @Groups({
     *     "order_header_write",
     *     "order_header_read"
     * })
     * @Assert\Valid()
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private Collection $lines;

    public function __construct()
    {
        $this->lines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getStatus(): ?OrderStatus
    {
        return $this->status;
    }

    public function setStatus(?OrderStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection|OrderLine[]
     */
    public function getLines(): Collection
    {
        return $this->lines;
    }

    public function addLine(OrderLine $line): self
    {
        if (!$this->lines->contains($line)) {
            $this->lines[] = $line;
            $line->setHeader($this);
        }

        return $this;
    }

    public function removeLine(OrderLine $line): self
    {
        if ($this->lines->contains($line)) {
            $this->lines->removeElement($line);
            // set the owning side to null (unless already changed)
            if ($line->getHeader() === $this) {
                $line->setHeader(null);
            }
        }

        return $this;
    }

    public function getPaymentType(): ?PaymentType
    {
        return $this->paymentType;
    }

    public function setPaymentType(?PaymentType $paymentType): self
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    public function getShipmentMethod(): ?ShipmentMethod
    {
        return $this->shipmentMethod;
    }

    public function setShipmentMethod(?ShipmentMethod $shipmentMethod): self
    {
        $this->shipmentMethod = $shipmentMethod;

        return $this;
    }

    public function getChannel(): ?Channel
    {
        return $this->channel;
    }

    public function setChannel(?Channel $channel): self
    {
        $this->channel = $channel;

        return $this;
    }
}
