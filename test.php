<?php
class a {
	function __construct($c) {
		$this->c = $c;
	}
	function call() {
		echo $this->c->child();
	}
}
class b extends c {
	
}
class c {
	function child() {
		return "child";
	}
}
$c = new c();
$a = new a($c);
$a->call();

function ref(&$s) {
	$s[0] = "dhange";
}

$d = "s";
$e = array(&$d);
ref($e);
echo $d;

echo print_r(array_diff(array(1,2,3,4),array(2,4,5)));
?>