<?php

use mrbavii\helper\Template;

$case->assertTrue($number == 600);
print "123";

$level = ob_get_level();

try
{
    print Template::get('test/test4', array('number' => 700));
}
catch(\Exception $e)
{
    print "error";
}

$case->assertTrue(ob_get_level() == $level);

print Template::get('test/test3');

