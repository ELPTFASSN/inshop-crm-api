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
 * PurchaseOrderHeader
 *
 * @ORM\Table(name="purchase_order_header")
 * @ORM\Entity(repositoryClass="App\Repository\PurchaseOrderHeaderRepository")
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"purchase_order_header_read", "read", "is_active_read"}},
 *         "denormalization_context"={"groups"={"purchase_order_header_write", "is_active_write"}},
 *         "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"={
 *                  "groups"={"purchase_order_header_read_collection", "read", "is_active_read"}
 *              },
 *              "security"="is_granted('ROLE_PURCHASE_ORDER_HEADER_LIST')"
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_PURCHASE_ORDER_HEADER_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_PURCHASE_ORDER_HEADER_SHOW')"
 *          },
 *          "put"={
 *              "security"="is_granted('ROLE_PURCHASE_ORDER_HEADER_UPDATE')"
 *          },
 *          "delete"={
 *              "security"="is_granted('ROLE_PURCHASE_ORDER_HEADER_DELETE')"
 *          }
 *     }
 * )
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "number": "exact",
 *     "company.name": "ipartial",
 *     "status.name": "ipartial",
 *     "currency.name": "ipartial",
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "number",
 *          "company.name",
 *          "status.name",
 *          "currency.name",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
class PurchaseOrderHeader
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
     *     "purchase_order_header_read",
     *     "purchase_order_header_read_collection"
     * })
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({
     *     "purchase_order_header_read",
     *     "purchase_order_header_write",
     *     "purchase_order_header_read_collection"
     * })
     * @Assert\NotBlank()
     */
    private string $number;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PurchaseOrderStatus")
     * @Groups({
     *     "purchase_order_header_read",
     *     "purchase_order_header_write",
     *     "purchase_order_header_read_collection"
     * })
     * @Assert\NotNull()
     */
    private ?PurchaseOrderStatus $status = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company")
     * @Groups({
     *     "purchase_order_header_read",
     *     "purchase_order_header_write",
     *     "purchase_order_header_read_collection"
     * })
     * @Assert\NotNull()
     */
    private ?Company $company = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Currency")
     * @Groups({
     *     "purchase_order_header_read",
     *     "purchase_order_header_write",
     *     "purchase_order_header_read_collection"
     * })
     * @Assert\NotNull()
     */
    private ?Currency $currency = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PaymentType")
     * @Groups({
     *     "purchase_order_header_read",
     *     "purchase_order_header_write",
     *     "purchase_order_header_read_collection"
     * })
     * @Assert\NotNull()
     */
    private ?PaymentType $paymentType = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ShipmentMethod")
     * @Groups({
     *     "purchase_order_header_read",
     *     "purchase_order_header_write",
     *     "purchase_order_header_read_collection"
     * })
     * @Assert\NotNull()
     */
    private ?ShipmentMethod $shipmentMethod = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PurchaseOrderLine", mappedBy="header", cascade={"persist"})
     * @Groups({
     *     "purchase_order_header_write",
     *     "purchase_order_header_read"
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

    public function getStatus(): ?PurchaseOrderStatus
    {
        return $this->status;
    }

    public function setStatus(?PurchaseOrderStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): self
    {
        $this->currency = $currency;

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

    /**
     * @return Collection|PurchaseOrderLine[]
     */
    public function getLines(): Collection
    {
        return $this->lines;
    }

    public function addLine(PurchaseOrderLine $line): self
    {
        if (!$this->lines->contains($line)) {
            $this->lines[] = $line;
            $line->setHeader($this);
        }

        return $this;
    }

    public function removeLine(PurchaseOrderLine $line): self
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
}
