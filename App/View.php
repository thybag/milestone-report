<?php
namespace App;

/**
 * Quick view wrapper
 */
class View 
{
	private $file;
	protected $params;

	public function __construct($file, $params = [])
	{
		$this->file = $file;	
		$this->params = $params;
	}

	public function render()
	{
		// Make vars visible
		extract($this->params);

		// Buffer and output results.
		ob_start();
		include($this->file. '.tpl.php');
		return ob_get_clean();
	}

	public function view($file, $params = array()){
		$view = new View(dirname($this->file) . '/' . $file, $params);
		echo $view->render();
	}

	public function __toString()
	{
		return $this->render();
	}
}