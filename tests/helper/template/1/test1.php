<?php
use mrbavii\helper\Template;

$case->assertTrue($number == 500);
print "abc";

print Template::get('test', 'test2', array('number' => 600));

$case->assertTrue($number == 500);
print "def";

