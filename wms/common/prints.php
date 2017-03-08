<?php

namespace App\prints;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
/**
* 
*/
class prints
{
	public $size = "A4";

	public $count = 1;

	public $page_index = 0;

	public $width = 960;

	public $height = 1200;

	public $current_height = 0;

	public $html = "";

	function __construct(){
		$this->html = $this->top($this->page_index);
	}

	function html($html,$height){
		if ($this->height_add($height)) {
			$this->html .= $html;
		}
	}

	protected function height_add($height){
		if ($height > $this->height) {
			return false;
		} else if ($this->current_height + $height > $this->height) {
			$this->page_index++;
			$this->count++;
			$this->html .= $this->bottom($this->page_index).$this->top($this->page_index);
			$this->current_height = 0;
		} else {
			$this->current_height += $height;
		}
		return true;

	}

	protected function top($para=""){
		return "";
	}

	protected function bottom($para=""){
		return "";
	}

	function render(){
		return $html.$this->bottom($this->page_index);
	}	

}