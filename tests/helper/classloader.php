<?php

use mrbavii\helper\ClassLoader;

require_once("simpletest/autorun.php");

class TestClassLoader extends UnitTestCase
{
    public function setUp()
    {
        require_once(__DIR__ . "/../../helper/ClassLoader.php");
        ClassLoader::install();
    }

    public function tearDown()
    {
    }

    public function test_with_underscore()
    {
        ClassLoader::register("TestNS_", __DIR__ . "/classloader/c1", ".class.php");

        $this->assertFalse(class_exists("\\TestNS_Base", FALSE));
        $this->assertFalse(class_exists("\\TestNS_Sub_SubItem", FALSE));

        $this->assertTrue(class_exists("\\TestNS_Base", TRUE));
        $this->assertTrue(class_exists("\\TestNS_Sub_SubItem", TRUE));
        
        $this->assertFalse(class_exists("\\TestNS_", TRUE));
    }

    public function test_with_namespace()
    {
        ClassLoader::register("TestNS\\", __DIR__ . "/classloader/c2", ".class.php");
        
        $this->assertFalse(class_exists("\\TestNS\\Base", FALSE));
        $this->assertFalse(class_exists("\\TestNS\\Sub\\SubItem", FALSE));

        $this->assertTrue(class_exists("\\TestNS\\Base", TRUE));
        $this->assertTrue(class_exists("\\TestNS\\Sub\\SubItem", TRUE));

        $this->assertFalse(class_exists("\\TestNS\\", TRUE));

    }
}
