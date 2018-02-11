<?php

namespace League\CLImate\Tests;

use League\CLImate\CLImate;
use League\CLImate\Decorator\Style;
use League\CLImate\Logger;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class LoggerTest extends TestCase
{
    private $cli;
    private $logger;

    public function setUp()
    {
        $this->cli = Mockery::mock(CLImate::class);

        $style = Mockery::mock(Style::class);
        $style->shouldReceive("get")->andReturn(true);
        $this->cli->style = $style;

        $this->logger = new Logger(LogLevel::DEBUG, $this->cli);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testEmergency()
    {
        $this->cli->shouldReceive("emergency")->once()->with("Testing emergency");
        $this->logger->emergency("Testing emergency");
    }


    public function testAlert()
    {
        $this->cli->shouldReceive("alert")->once()->with("Testing alert");
        $this->logger->alert("Testing alert");
    }


    public function testCritical()
    {
        $this->cli->shouldReceive("critical")->once()->with("Testing critical");
        $this->logger->critical("Testing critical");
    }


    public function testError()
    {
        $this->cli->shouldReceive("error")->once()->with("Testing error");
        $this->logger->error("Testing error");
    }


    public function testWarning()
    {
        $this->cli->shouldReceive("warning")->once()->with("Testing warning");
        $this->logger->warning("Testing warning");
    }


    public function testNotice()
    {
        $this->cli->shouldReceive("notice")->once()->with("Testing notice");
        $this->logger->notice("Testing notice");
    }


    public function testInfo()
    {
        $this->cli->shouldReceive("info")->once()->with("Testing info");
        $this->logger->info("Testing info");
    }


    public function testDebug()
    {
        $this->cli->shouldReceive("debug")->once()->with("Testing debug");
        $this->logger->debug("Testing debug");
    }


    public function testLog()
    {
        $this->cli->shouldReceive("critical")->once()->with("Testing log");
        $this->logger->log("critical", "Testing log");
    }


    public function testLevelEmergency()
    {
        $this->cli->shouldReceive("emergency")->once()->with("Testing log");
        $this->logger->setLogLevel(LogLevel::EMERGENCY)->emergency("Testing log");
    }


    public function testLevelAlert()
    {
        $this->cli->shouldReceive("alert")->never();
        $this->logger->setLogLevel(LogLevel::EMERGENCY)->alert("Testing log");
    }


    public function testLevelNotice()
    {
        $this->cli->shouldReceive("notice")->once()->with("Notice");
        $this->logger->setLogLevel("notice")->notice("Notice");
    }


    public function testLevelDebug()
    {
        $this->cli->shouldReceive("debug")->once()->with("Debug");
        $this->logger->setLogLevel("DEBUG")->debug("Debug");
    }


    public function testNumericLevel()
    {
        $this->cli->shouldReceive("emergency")->once()->with("Some Info");
        $this->logger->setLogLevel(5)->emergency("Some Info");
    }


    public function testTooHighLevel()
    {
        $this->cli->shouldReceive("debug")->once()->with("Some Info");
        $this->logger->setLogLevel(15)->debug("Some Info");
    }


    public function testTooLowLevel()
    {
        $this->cli->shouldReceive("debug")->never();
        $this->logger->setLogLevel(0)->debug("Some Info");
    }


    public function testInvalidLevel()
    {
        $this->cli->shouldReceive("emergency")->once()->with("Invalid Stuff");
        $this->cli->shouldReceive("info")->never();
        $this->logger->setLogLevel("INVALID");
        $this->logger->emergency("Invalid Stuff");
        $this->logger->info("Nope");
    }


    public function testContext()
    {
        $this->cli->shouldReceive("info")->once()->with("With context");

        $this->cli->shouldReceive("tab")->with(1)->once()->andReturn($this->cli);
        $this->cli->shouldReceive("info")->once()->andReturn($this->cli);
        $this->cli->shouldReceive("inline")->once()->with("context: ");
        $this->cli->shouldReceive("info")->once()->with("CONTEXT");

        $this->logger->info("With context", [
            "context"   =>  "CONTEXT",
        ]);
    }


    public function testEmptyContext()
    {
        $this->cli->shouldReceive("info")->once()->with("No context");
        $this->logger->info("No context", []);
    }



    public function testPlaceholders()
    {
        $this->cli->shouldReceive("info")->once()->with("I am Spartacus!");
        $this->logger->info("I am {username}!", [
            "username"  =>  "Spartacus",
        ]);
    }


    public function testPlaceholdersAndContext()
    {
        $this->cli->shouldReceive("info")->once()->with("I am Spartacus!");

        $this->cli->shouldReceive("tab")->with(1)->once()->andReturn($this->cli);
        $this->cli->shouldReceive("info")->once()->andReturn($this->cli);
        $this->cli->shouldReceive("inline")->once()->with("date: ");
        $this->cli->shouldReceive("info")->once()->with("2015-03-01");

        $this->logger->info("I am {username}!", [
            "username"  =>  "Spartacus",
            "date"      =>  "2015-03-01",
        ]);
    }


    public function testRecursiveContext()
    {
        $this->cli->shouldReceive("info")->once()->with("INFO");

        $this->cli->shouldReceive("tab")->with(1)->once()->andReturn($this->cli);
        $this->cli->shouldReceive("info")->once()->andReturn($this->cli);
        $this->cli->shouldReceive("inline")->once()->with("data: ");
        $this->cli->shouldReceive("info")->once()->with("[");

        $this->cli->shouldReceive("tab")->with(2)->once()->andReturn($this->cli);
        $this->cli->shouldReceive("info")->once()->andReturn($this->cli);
        $this->cli->shouldReceive("inline")->once()->with("field1: ");
        $this->cli->shouldReceive("info")->once()->with("One");

        $this->cli->shouldReceive("tab")->with(2)->once()->andReturn($this->cli);
        $this->cli->shouldReceive("info")->once()->andReturn($this->cli);
        $this->cli->shouldReceive("inline")->once()->with("field2: ");
        $this->cli->shouldReceive("info")->once()->with("Two");

        $this->cli->shouldReceive("tab")->with(2)->once()->andReturn($this->cli);
        $this->cli->shouldReceive("info")->once()->andReturn($this->cli);
        $this->cli->shouldReceive("inline")->once()->with("extra: ");
        $this->cli->shouldReceive("info")->once()->with("[");

        $this->cli->shouldReceive("tab")->with(3)->once()->andReturn($this->cli);
        $this->cli->shouldReceive("info")->once()->andReturn($this->cli);
        $this->cli->shouldReceive("inline")->once()->with("0: ");
        $this->cli->shouldReceive("info")->once()->with("Three");

        $this->cli->shouldReceive("tab")->with(3)->once()->andReturn($this->cli);
        $this->cli->shouldReceive("info")->once()->andReturn($this->cli);
        $this->cli->shouldReceive("inline")->once()->with("1: ");
        $this->cli->shouldReceive("info")->once()->with("Four");

        $this->cli->shouldReceive("tab")->with(2)->once()->andReturn($this->cli);
        $this->cli->shouldReceive("info")->once()->with("]");

        $this->cli->shouldReceive("tab")->with(1)->once()->andReturn($this->cli);
        $this->cli->shouldReceive("info")->once()->with("]");

        $this->logger->info("INFO", [
            "data"      =>  [
                "field1"    =>  "One",
                "field2"    =>  "Two",
                "extra"     =>  ["Three", "Four"],
            ],
        ]);
    }
}
