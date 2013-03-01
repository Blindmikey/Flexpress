<?php

	function hello(){
		echo 'hello';
	}
	function world(){
		echo ' world!';
	}
	function itWorked($bool = null){
		if($bool){
			echo '<br /> This is a functioning Action/Hook system!';	
		}
		else {
			echo '<br /> It worked!';	
		}
	}

	addAction('testHook', 'hello');
	addAction('testHook', 'world');
	addAction('testHook', array('itWorked', false));