<?php
class ArrayToTable
{
	public $html;
    function __construct($data, $attr = "")
    {
		if(empty($data[0]))
		return;
		
		$html = "<table ".$attr.">";
		$html .= "<tr>";
		foreach($data[0] as $header_field => $value) {
			$html .= "<th>".$header_field."</th>";
		}
		$html .= "</tr>";
		
		foreach($data as $row) {
			$html .= "<tr>";
			foreach($row as $field) {
				$html .= "<td>".$field."</td>";
			}
			$html .= "</tr>";
		}
		$html .= "</table>";
		$this->html = $html;
	}
	function show() {
		echo $this->html;
	}
	function get() {
		return $this->html;
	}
}
