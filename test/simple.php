<?php

$a = 1;
$b = 'string';
$c = 1.2;
$g = 'lol';
$w = $g = $asd = 'ads';

class A {
    public $g;

}

//$a = new A();
$c = $a;

if (1) {
    $a = 'string';
} elseif(2) {
    $a = new A();
}

const DSA = 3;

if (true) {
    $w = 1 * 2 * 3 * 4;
}
