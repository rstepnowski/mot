<?php

namespace CoreTest\Presenter;

use Core\Presenter\MastheadPresenter;

class MastheadPresenterTest extends \PHPUnit_Framework_TestCase
{
    protected $presenter;

    protected $expectedUrl = 'http://www.smartsurvey.co.uk/s/MOTFeedback/';

    protected function setUp()
    {
        $this->presenter = new MastheadPresenter;
    }

    protected function tearDown()
    {
        unset($this->presenter);
    }

    public function testGetFeedbackUrl()
    {
        $this->assertEquals($this->presenter->getFeedbackUrl(), $this->expectedUrl);
    }
}
