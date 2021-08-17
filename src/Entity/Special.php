<?php


namespace ATD\CruiseFactory\Entity;


use ATD\CruiseFactory\Services\Data\Collections\ArrayCollection;
use ATD\CruiseFactory\Services\Data\Collections\Collection;
use DateTime;
use DateTimeInterface;

class Special {
	public int $id;
	private string $special_header;
	private string $validity_date_start;
	private string $validity_date_end;
	private string $type;
	private float $start_price;
	private ?Currency $currency;
	private ?Cruise $cruise;
	private ?SpecialLeadPrice $leadPricing;
	private Collection $pricing;
	private Collection $itinerary;
	private ?int $departure_id;

	public function __construct() {
		$this->pricing   = new ArrayCollection();
		$this->itinerary = new ArrayCollection();
	}

	public function getId(): int {
		return $this->id;
	}

	public function setId( int $id ): self {
		$this->id = $id;

		return $this;
	}

	public function getName(): string {
		return $this->special_header;
	}

	public function setName( string $name ): self {
		$this->special_header = $name;

		return $this;
	}

	public function getValidFrom(): ?DateTimeInterface {
		if ( $date = DateTime::createFromFormat( 'Y-m-d', $this->validity_date_start ) ) {
			return $date;
		}

		return null;
	}

	public function setValidFrom( DateTimeInterface $date ): self {
		$this->validity_date_start = $date->format( 'Y-m-d' );

		return $this;
	}

	public function getValidTo(): ?DateTimeInterface {
		if ( $date = DateTime::createFromFormat( 'Y-m-d', $this->validity_date_end ) ) {
			return $date;
		}

		return null;
	}

	public function setValidTo( DateTimeInterface $date ): self {
		$this->validity_date_end = $date->format( 'Y-m-d' );

		return $this;
	}

	public function getCruise(): ?Cruise {
		return $this->cruise ?? null;
	}

	public function setCruise( Cruise $cruise ): self {
		$this->cruise = $cruise;

		return $this;
	}

	public function getType(): string {
		return $this->type;
	}

	public function setType( string $type ): self {
		$this->type = $type;

		return $this;
	}

	public function getStartPrice(): float {
		return $this->start_price;
	}

	public function setStartPrice( float $startPrice ): self {
		$this->start_price = $startPrice;

		return $this;
	}

	public function getCurrency(): ?Currency {
		return $this->currency ?? null;
	}

	public function setCurrency( Currency $currency ): self {
		$this->currency = $currency;

		return $this;
	}

	public function getSpecialLeadPrice(): SpecialLeadPrice {
		return $this->leadPricing;
	}

	public function setSpecialLeadPrice( SpecialLeadPrice $leadPricing ): self {
		$this->leadPricing = $leadPricing;

		return $this;
	}

	public function getSpecialPrices() {
		return $this->pricing;
	}

	public function setSpecialPrices( array $pricing ): self {
		$this->pricing = new ArrayCollection( $pricing );

		return $this;
	}

	public function getDepartureId(): ?int {
		return $this->departure_id ?? null;
	}

	public function setDepartureId( int $specialDepartureId ): self {
		$this->departure_id = $specialDepartureId;

		return $this;
	}

	public function getSpecialItinerary(): Collection {
		return $this->itinerary;
	}

	public function getSpecialPreItinerary(): Collection {
		return $this->itinerary->filter( function ( $i ) {
			return $i->getType() === 'pre';
		} );
	}

	public function getSpecialPostItinerary(): Collection {
		return $this->itinerary->filter( function ( $i ) {
			return $i->getType() === 'post';
		} );
	}

	public function setSpecialItinerary( array $itinerary ): self {
		$this->itinerary = new ArrayCollection( $itinerary );

		return $this;
	}
}