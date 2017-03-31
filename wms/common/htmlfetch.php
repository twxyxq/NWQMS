<?php 

namespace App\htmlfetch;


/**
* 
*/
class html_tag
{
	public $tag_id = "";
	public $tag_name = "";
	public $tag_by = "";
	public $tag_type = "";

	function __construct($tag_id,$tag_name="span",$tag_by="id",$tag_type="content")
	{
		$this->tag_id = $tag_id;
		$this->tag_name = $tag_name;
		$this->tag_by = $tag_by;
		$this->tag_type = $tag_type;
	}

	function tag_name($tag_name){
		$this->tag_name = $tag_name;
	}

	function tag_type($tag_type){
		$this->tag_type = $tag_type;
	}

	function tag_by($tag_by){
		$this->tag_by = $tag_by;
	}
}
/**
* 
*/
class html_info
{
	protected $html = "";
	public $tag = array();
	protected $tag_value = array();

	function __construct($url)
	{
		$this->html = file_get_contents($url);
	}

	function add_tag($tag_id){
		$idt = array_push($this->tag,new html_tag($tag_id))-1;
		return $this->tag[$idt];
	}

	function get_attr($tag_id,$by="id"){
		$match_word = array();
		preg_match("/<[^<>]*".$by."=[\"']".$tag_id."[\"'][^<>]*>/", $this->html, $match_word);
		$r_word = array();
		preg_match("/src=[\"'][^\"']*[\"']/",$match_word[0],$r_word);
		return substr($r_word[0],5,strlen($r_word[0])-6);
		//$img_pos = strpos($html,$tag_id);
		//$img_path = substr($html,0,$img_pos-6);
		//$img_start = strrpos($img_path,"src");
		//$img_path = substr($img_path,$img_start+5);
	}

	function get_content($tag_id,$tag_name="span",$by="id"){
		$match_word = array();
		preg_match("/<".$tag_name."[^<>]*".$by."=[\"']".$tag_id."[\"'][^<>]*>[^<>]*<\/".$tag_name.">/", $this->html, $match_word);
		$start = strpos($match_word[0],">")+1;
		$end = strpos($match_word[0],"</");
		return substr($match_word[0],$start,$end-$start);
	}

	function get_table($title){
		$match_word = array();
		preg_match("/".$title."[^<>]*<\/td>[^<>]*<td[^<>]*>[\s\S]{0,20}<\/td>/", $this->html, $match_word);
		//$match_word[0] = substr($match_word[0],strpos($match_word[0],"</td>")+5);
		//$start = strpos($match_word[0],">")+1;
		//$end = strpos($match_word[0],"</");
		//return substr($match_word[0],$start,$end-$start);
		return $match_word[0];
	}

	function get_value(){
		for ($i=0; $i < sizeof($this->tag); $i++) { 
			if ($this->tag[$i]->tag_type == "content") {
				$this->tag_value[$this->tag[$i]->tag_id] = $this->get_content($this->tag[$i]->tag_id,$this->tag[$i]->tag_name,$this->tag[$i]->tag_by);
			} else if ($this->tag[$i]->tag_type == "table"){
				$this->tag_value[$this->tag[$i]->tag_id] = $this->get_table($this->tag[$i]->tag_id);
			} else {
				$this->tag_value[$this->tag[$i]->tag_id] = $this->get_attr($this->tag[$i]->tag_id,$this->tag[$i]->tag_by);
			}
		}
		return $this->tag_value;
	}
}




