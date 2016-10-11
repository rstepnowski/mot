<?php

namespace Dvsa\Mot\Frontend\RegistrationModuleTest\Step;

use Dvsa\Mot\Frontend\RegistrationModule\Service\RegistrationSessionService;
use Dvsa\Mot\Frontend\RegistrationModule\Step\AddressStep;
use DvsaCommonTest\TestUtils\XMock;
use Zend\InputFilter\InputFilter;

/**
 * Class AddressStepTest.
 *
 * @group VM-11506
 */
class AddressStepTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the constructor.
     *
     * @throws \Exception
     */
    public function testConstructor()
    {
        $step = new AddressStep(
            XMock::of(RegistrationSessionService::class),
            XMock::of(InputFilter::class)
        );

        $this->assertInstanceOf(AddressStep::class, $step);
    }

    /**
     * Placeholder test until validation stories are implemented.
     */
    public function testId()
    {
        $step = new AddressStep(
            XMock::of(RegistrationSessionService::class),
            XMock::of(InputFilter::class)
        );

        $this->assertEquals(AddressStep::STEP_ID, $step->getId());
    }

    /**
     * Test loading data returned from the session.
     *
     * @throws \Exception
     */
    public function testLoad()
    {
        $fixture = $this->getFixture();

        $session = XMock::of(RegistrationSessionService::class);
        $session->expects($this->once())
            ->method('load')
            ->with(AddressStep::STEP_ID)
            ->willReturn($fixture);

        $step = new AddressStep(
            $session,
            XMock::of(InputFilter::class)
        );

        $step->load();

        $this->assertEquals($step->getAddress1(), $fixture['address1']);
        $this->assertEquals($step->getAddress2(), $fixture['address2']);
        $this->assertEquals($step->getAddress3(), $fixture['address3']);
        $this->assertEquals($step->gettownOrCity(), $fixture['townOrCity']);
        $this->assertEquals($step->getpostcode(), strtoupper($fixture['postcode']));
    }

    /**
     * Test extracting values into an array.
     */
    public function testToArray()
    {
        $step = new AddressStep(
            XMock::of(RegistrationSessionService::class),
            XMock::of(InputFilter::class)
        );

        $step->setAddress1('address1');
        $step->setAddress2('address2');
        $step->setAddress3('address3');
        $step->settownOrCity('townOrCity');
        $step->setpostcode('postcode');

        $values = $step->toArray();

        $this->assertEquals('address1', $values['address1']);
        $this->assertEquals('address2', $values['address2']);
        $this->assertEquals('address3', $values['address3']);
        $this->assertEquals('townOrCity', $values['townOrCity']);
        $this->assertEquals('postcode', $values['postcode']);
    }

    /**
     * Test all the property getters and setters.
     */
    public function testGettersSetters()
    {
        $step = new AddressStep(
            XMock::of(RegistrationSessionService::class),
            XMock::of(InputFilter::class)
        );

        $step->setAddress1('address1');
        $step->setAddress2('address2');
        $step->setAddress3('address3');
        $step->settownOrCity('townOrCity');
        $step->setpostcode('postcode');

        $this->assertEquals('address1', $step->getAddress1());
        $this->assertEquals('address2', $step->getAddress2());
        $this->assertEquals('address3', $step->getAddress3());
        $this->assertEquals('townOrCity', $step->gettownOrCity());
        $this->assertEquals('postcode', $step->getpostcode());
    }

    /**
     * @return array
     */
    public function getFixture()
    {
        $fixture = [
            'address1'   => __METHOD__ . '_address1',
            'address2'   => __METHOD__ . '_address2',
            'address3'   => __METHOD__ . '_address3',
            'townOrCity' => __METHOD__ . '_townOrCity',
            'postcode'   => __METHOD__ . '_postcode',
        ];

        return $fixture;
    }
}
