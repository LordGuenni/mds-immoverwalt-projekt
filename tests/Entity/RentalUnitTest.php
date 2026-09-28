<?php

namespace App\Tests\Entity;

use App\Entity\RentalUnit;
use App\Entity\Tenancy;
use PHPUnit\Framework\TestCase;

class RentalUnitTest extends TestCase
{
    public function testStatusColorIsSetOnSave()
    {
        $unit = new RentalUnit();
        $unit->setStatus(RentalUnit::AVAILABLE);
        $unit->touch();
        $this->assertSame('status-green', $unit->getCssClass());

        $unit->setStatus(RentalUnit::NOTICE_GIVEN);
        $unit->touch();
        $this->assertSame('status-orange', $unit->getCssClass());
    }

    public function testCurrentTenancyIgnoresEndedContracts()
    {
        $unit = new RentalUnit();

        $old = new Tenancy();
        $old->setStatus(Tenancy::ENDED);
        $unit->getTenancies()->add($old);
        $this->assertNull($unit->getCurrentTenancy());

        $current = new Tenancy();
        $current->setStatus(Tenancy::NOTICE_GIVEN);
        $unit->getTenancies()->add($current);
        $this->assertSame($current, $unit->getCurrentTenancy());
    }
}
