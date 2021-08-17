<?php


namespace ATD\CruiseFactory\Feed;


use ATD\CruiseFactory\Services\Data\DBAL\EntityManager;
use DateTimeInterface;

interface Feed {
	public function import( DateTimeInterface $updatedAt ): bool;
	public function fetchServices(): ?string;
	public function fetchIncrement(): ?string;
	public static function getTableNameWithPrefix(): string;
	public static function getCreateTableSchema(): string;
	public function getLastUpdate(): ?DateTimeInterface;
	public function getRelationships(): array;
	public function getCollections(): array;
	public function getEntity(): string;
	public function getEntityManager(): EntityManager;
	public static function getFeedName(): string;
}