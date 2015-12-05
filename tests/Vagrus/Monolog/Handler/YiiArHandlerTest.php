<?php

namespace Vagrus\Monolog\Handler;

use Monolog\Logger;

class YiiArHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Vagrus\Monolog\Handler\YiiArHandler::__construct
     * @covers Vagrus\Monolog\Handler\YiiArHandler::setMapping
     */
    public function testMappingContainsRequiredParam()
    {
        $this->setExpectedException('\InvalidArgumentException');

        new YiiArHandler('FakeModel', array('contextVar' => 'modelProperty'));
    }

    /**
     * @covers Vagrus\Monolog\Handler\YiiArHandler::__construct
     * @covers Vagrus\Monolog\Handler\YiiArHandler::write
     */
    public function testModelSaved()
    {
        $this->getHandler()
            ->handle($this->getRecord());

        $this->assertEquals(true, MockTestModel::$isSaved);
    }

    /**
     * @covers Vagrus\Monolog\Handler\YiiArHandler::__construct
     * @covers Vagrus\Monolog\Handler\YiiArHandler::write
     */
    public function testModelNotValidatedOnSaving()
    {
        $this->getHandler()
            ->handle($this->getRecord());

        $this->assertEquals(false, MockTestModel::$isValidating);
    }

    /**
     * @covers Vagrus\Monolog\Handler\YiiArHandler::__construct
     * @covers Vagrus\Monolog\Handler\YiiArHandler::write
     */
    public function testLogMessageMappedOnCorrectField()
    {
        $this->getHandler()
            ->handle($this->getRecord());

        $this->assertArrayHasKey('message', MockTestModel::$attributes);
        $this->assertEquals(MockTestModel::$attributes['message'], 'Test');
    }

    /**
     * @covers Vagrus\Monolog\Handler\YiiArHandler::__construct
     * @covers Vagrus\Monolog\Handler\YiiArHandler::write
     */
    public function testContextVarMappedOnCorrectField()
    {
        $mapping = array(
            '*' => 'message',
            'someContextVar' => 'fieldForContextVar',
        );

        $this->getHandler($mapping)
            ->handle($this->getRecord());

        $this->assertArrayHasKey('fieldForContextVar', MockTestModel::$attributes);
        $this->assertEquals(MockTestModel::$attributes['fieldForContextVar'], 'val');
    }

    /**
     * @covers Vagrus\Monolog\Handler\YiiArHandler::__construct
     * @covers Vagrus\Monolog\Handler\YiiArHandler::write
     */
    public function testUnmappedContextVarIgnored()
    {
        $this->getHandler()
            ->handle($this->getRecord());

        $this->assertArrayNotHasKey('fieldForContextVar', MockTestModel::$attributes);
    }

    /**
     * @covers Vagrus\Monolog\Handler\YiiArHandler::__construct
     * @covers Vagrus\Monolog\Handler\YiiArHandler::write
     */
    public function testMappedButNonexistentContextVarIgnored()
    {
        $mapping = array(
            '*' => 'message',
            'otherContextVar' => 'fieldForContextVar',
        );

        $this->getHandler($mapping)
            ->handle($this->getRecord());

        $this->assertArrayNotHasKey('fieldForContextVar', MockTestModel::$attributes);
    }

    /**
     * @return \Monolog\Formatter\FormatterInterface
     */
    private function getIdentityFormatter()
    {
        $formatter = $this->getMock('Monolog\\Formatter\\FormatterInterface');
        $formatter->expects($this->any())
            ->method('format')
            ->will($this->returnCallback(function ($record) {
                return $record['message'];
            }));

        return $formatter;
    }

    /**
     * @return array
     */
    private function getRecord()
    {
        return array(
            'message' => 'Test',
            'context' => array(
                'someContextVar' => 'val',
            ),
            'level' => Logger::DEBUG,
            'level_name' => Logger::getLevelName(Logger::DEBUG),
            'channel' => 'test',
            'datetime' => \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true))),
            'extra' => array(),
        );
    }

    /**
     * @param string|array $mapping
     * @return YiiArHandler
     */
    private function getHandler($mapping = 'message')
    {
        $modelName = 'Vagrus\Monolog\Handler\MockTestModel';

        $handler = new YiiArHandler($modelName, $mapping);
        $handler->setFormatter($this->getIdentityFormatter());

        return $handler;
    }
}
