<?php
/*
 * Permission class
 *
 * @author			Han Lin Yap
 * @website			http://www.zencodez.net/
 * @created			24rd April 2010
 * @last-modified	24rd April 2010
 * @version			1.0
 * ----------------------------------------
 * Change log:
v1.0 - 24th April 2010
Created
 */

/* 
Depended files:
Blog (Parent class)

Methods:
 */
 
class permission {
	public function control($role) {
		switch ($role) {
			case 'view'		: return array(1);
			case 'add'		: return array(2);
			case 'edit'		: return array(3);
			case 'delete'	: return array(4);
			
			case 'full'		: return range(1,4);
			
			default 		: return $role; // for array_map-function
		}
	}
}
?>