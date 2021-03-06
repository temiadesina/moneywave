<?php

namespace Emmanix2002\Moneywave\Tests\Service;

use Emmanix2002\Moneywave\Enum\Environment;
use Emmanix2002\Moneywave\Moneywave;
use Emmanix2002\Moneywave\Service\WalletBalance;
use PHPUnit\Framework\TestCase;

class WalletBalanceTest extends TestCase
{
    /** @var  WalletBalance */
    private $serviceObject;
    
    public function setUp()
    {
        $moneywave = new Moneywave(ACCESS_TOKEN, API_KEY, SECRET_KEY, Environment::STAGING);
        $this->serviceObject = $moneywave->createWalletBalanceService();
    }
    
    public function testRequestMethod()
    {
        $this->assertEquals('get', strtolower($this->serviceObject->getRequestMethod()));
    }
    
    public function testPassValidation()
    {
        $this->assertTrue($this->serviceObject->validatePayload());
    }
}