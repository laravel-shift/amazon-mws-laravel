<?php

use KeithBrink\AmazonMws\AmazonServiceStatus;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-12 at 13:17:14.
 */
class AmazonCoreTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var AmazonServiceStatus
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        resetLog();
        $this->object = new AmazonServiceStatus('testStore', 'Inbound', true, null);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @return array
     */
    public function mockProvider()
    {
        return [
            [true, null, 'Mock Mode set to ON'],
            [false, null, null],
            [true, 'test', 'Mock Mode set to ON', 'Single Mock File set: test'],
            [true, ['test'], 'Mock Mode set to ON', 'Mock files array set.'],
            [false, 'test', 'Single Mock File set: test'],
            [false, ['test'], 'Mock files array set.'],
            ['no', null, null],
        ];
    }

    /**
     * @covers AmazonCore::setMock
     * @dataProvider mockProvider
     */
    public function testSetMock($a, $b, $c, $d = null)
    {
        resetLog();
        $this->object->setMock($a, $b);
        $check = parseLog();
        if ((is_bool($a) && $a) || $b) {
            $this->assertEquals($c, $check[0]);
        }
        if ($d) {
            $this->assertEquals($d, $check[1]);
        }
    }

    /**
     * @covers AmazonCore::setConfig
     * Test setting a custom configuration
     */
    public function testSetConfig()
    {
        $config = [
            'merchantId'       => 'TEST123',
            'marketplaceId'    => 'TESTMARKETPLACE',
            'keyId'            => 'TESTKEYID',
            'secretKey'        => 'TESTSECRETID',
            'amazonServiceUrl' => 'http://test.com',
            'muteLog'          => true,
        ];

        $this->object->setConfig($config);

        $o = $this->object->getOptions();

        $this->assertInternalType('array', $o);
        $this->assertArrayHasKey('SellerId', $o);
        $this->assertArrayHasKey('AWSAccessKeyId', $o);
        $this->assertEquals('TEST123', $o['SellerId']);
        $this->assertEquals('TESTKEYID', $o['AWSAccessKeyId']);
        $this->assertEquals('TESTMARKETPLACE', $o['MarketplaceId']);
        $this->assertTrue($this->readAttribute($this->object, 'muteLog'));
    }

    /**
     * @covers AmazonCore::setStore
     * Test that a store not in config file generates error
     */
    public function testSetStoreNotInConfig()
    {
        $this->expectException(Exception::class, 'Store no does not exist!');
        $this->object->setStore('no');
    }

    /**
     * @covers AmazonCore::setStore
     * Test that a store set in config but with incomplete info generates warnings in log.
     */
    public function testSetStoreInConfigWithMissingInfoThrowsException()
    {
        $this->expectException(Exception::class, 'Store bad configuration values not set correctly. See log for details.');
        $this->object->setStore('bad');
    }

    /**
     * @covers AmazonCore::setStore
     * Test that a store set in config but with incomplete info generates warnings in log.
     */
    public function testSetStoreInConfigWithMissingInfoLogsDetails()
    {
        resetLog();

        try {
            $this->object->setStore('bad');
        } catch (Exception $e) {
            // Continue processing
        }
        $bad = parseLog();
        $this->assertEquals('Merchant ID is missing!', $bad[0]);
        $this->assertEquals('Marketplace ID is missing!', $bad[1]);
        $this->assertEquals('Access Key ID is missing!', $bad[2]);
        $this->assertEquals('Secret Key is missing!', $bad[3]);
    }

    public function testGetOptions()
    {
        $o = $this->object->getOptions();
        $this->assertInternalType('array', $o);
        $this->assertArrayHasKey('SellerId', $o);
        $this->assertArrayHasKey('AWSAccessKeyId', $o);
        $this->assertArrayHasKey('SignatureVersion', $o);
        $this->assertArrayHasKey('SignatureMethod', $o);
        $this->assertArrayHasKey('Version', $o);
    }

    public function testLog()
    {
        resetLog();

        $reflector = new ReflectionClass(get_class($this->object));
        $method = $reflector->getMethod('log');
        $method->setAccessible(true);

        $method->invokeArgs($this->object, ['test log']);

        $this->assertEquals('test log', parseLog()[0]);
    }

    public function testMuteLog()
    {
        resetLog();

        $config = [
            'merchantId'       => 'TEST123',
            'marketplaceId'    => 'TESTMARKETPLACE',
            'keyId'            => 'TESTKEYID',
            'secretKey'        => 'TESTSECRETID',
            'amazonServiceUrl' => 'http://test.com',
            'muteLog'          => true,
        ];

        $this->object->setConfig($config);

        $reflector = new ReflectionClass(get_class($this->object));
        $method = $reflector->getMethod('log');
        $method->setAccessible(true);

        $method->invokeArgs($this->object, ['test log']);

        $this->assertArrayNotHasKey(0, parseLog());
    }
}

require_once __DIR__.'/../helperFunctions.php';
