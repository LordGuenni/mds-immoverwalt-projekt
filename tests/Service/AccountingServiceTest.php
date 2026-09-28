<?php

namespace App\Tests\Service;

use App\Service\AccountingService;
use PHPUnit\Framework\TestCase;

class AccountingServiceTest extends TestCase
{
    public function testBalanceIsCumulative()
    {
        $rows = AccountingService::withBalance([
            ['month' => '2026-01', 'debit' => 1120.0, 'credit' => 1120.0],
            ['month' => '2026-02', 'debit' => 1120.0, 'credit' => 1000.0],
            ['month' => '2026-03', 'debit' => 1120.0, 'credit' => 1120.0],
        ]);

        $this->assertSame(0.0, $rows[0]['balance']);
        $this->assertSame(120.0, $rows[1]['balance']);
        $this->assertSame(120.0, $rows[2]['balance']);
    }
}
