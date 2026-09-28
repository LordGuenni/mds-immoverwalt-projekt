<?php

namespace App\Service;

use App\Entity\Property;
use App\Entity\RentalUnit;
use App\Repository\PropertyRepository;

class PropertyService
{
    private $propertyRepository;

    public function __construct(PropertyRepository $propertyRepository)
    {
        $this->propertyRepository = $propertyRepository;
    }

    public function nextPropertyNumber(): string
    {
        return sprintf('OB-%04d', count($this->propertyRepository->findAll()) + 101);
    }

    /**
     * Kennzahlen eines Objekts für die Detailansicht.
     */
    public function getStatistics(Property $property): array
    {
        $stats = ['units' => 0, 'rented' => 0, 'available' => 0, 'other' => 0, 'area' => 0.0, 'rent' => 0.0];

        foreach ($property->getBuildings() as $building) {
            foreach ($building->getUnits() as $unit) {
                $stats['units']++;
                $stats['area'] += (float) $unit->getArea();
                if (in_array($unit->getStatus(), [RentalUnit::RENTED, RentalUnit::NOTICE_GIVEN])) {
                    $stats['rented']++;
                    $current = $unit->getCurrentTenancy();
                    $stats['rent'] += $current ? (float) $current->getColdRent() : 0;
                } elseif ($unit->getStatus() === RentalUnit::AVAILABLE) {
                    $stats['available']++;
                } else {
                    $stats['other']++;
                }
            }
        }
        $stats['vacancy'] = $stats['units'] > 0 ? round(100 * $stats['available'] / $stats['units'], 1) : 0;

        return $stats;
    }
}
