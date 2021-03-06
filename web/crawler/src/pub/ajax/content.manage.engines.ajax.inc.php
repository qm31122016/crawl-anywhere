<?php
//============================================================================
// (c) 2009-2010, Eolya - All Rights Reserved.
// This source code is the property of Eolya.
// The license applying to this source code is available at :
// http://www.crawl-anywhere.com/licenses/
//============================================================================
require_once("../../init_gpc.inc.php");
require_once("../../init.inc.php");

$action = POSTGET("action");

if ($action=="showenginelist") {

	$mg = mg_connect ($config, "", "", "");
	if ($mg)
	{
		$res = "<h2>Engines</h2>";

		$stmt = new mg_stmt_select($mg, "engines");
		$stmt->setFields (array("id" => "true", "name" => "true"));
		$stmt->setSort(array( "name" => 1 ));
		$count = $stmt->execute();
		if ($count>0) {
			$cursor = $stmt->getCursor();		
			while ($cursor->hasNext()) {
				$rs = $cursor->getNext();
				$engine = $rs["name"];
				$res .= $engine;
				$res .= "&nbsp;<a href='#' onClick='editEngine(" . $rs["id"] . ");return false;' title='Edit'><img src='images/button_edit.png'></a>";
				$res .= "<br />";
			}
		}
	}

	$res .= "<br /><br /><br /><br /><br /><br />Add new engine<a href='#' onClick='displayAddEngine(); return false;'><img src='images/edit_add_32.png'></a>&nbsp;&nbsp;";

	print $res;
	exit();
}

if ($action=="displayengine") {

	$res = "<br /><br /><br />";

	$id = $_GET["id"];
	if ($id=="")
	{
		print ("");
		exit();
	}

	$mg = mg_connect ($config, "", "", "");
	if ($mg)
	{
		$stmt = new mg_stmt_select($mg, "engines");
		$query = array ("id" => intval($id));
		$stmt->setQuery ($query);
		$count = $stmt->execute();
		if ($count==0) {
			print $s;
			exit();
		}
		
		$cursor = $stmt->getCursor();
		$rs = $cursor->getNext();
	
		$res .= "<form name='engine_edit' id='engine_edit' action=''><center><table border='0' cellspacing='0' cellpadding='0'>";
		$res .= "<tbody>";

		$res .= "<tr>";
		$res .= "<td class='head'>Id</td>";
		$res .= "<td>" . $rs["id"] . "</td>";
		$res .= "</tr>";

		$res .= "<tr>";
		$res .= "<td class='head'>Name</td>";
		if ($id=="1")
		$res .= "<td>" . $rs["name"] . "</td>";
		else
		$res .= "<td><input class='editInputText' type='text' name='engine_name' id='engine_name' value='" . fi($rs["name"]) . "'></td>";
		$res .= "</tr>";

		$res .= "</table></center>";

		$res .= "<br/>";

		$res .= "<input type='hidden' id='engine_id' name ='engine_id' value='". $rs["id"] ."'>";
		$res .= "<input type='hidden' id='action' name ='action' value='saveengine'>";

		$res .= "<div class='menu_button_on_right'><span id='engine_save_result'></span>";
		$res .= "<a href='#' onClick='cancelEngine();return false;'><img src='images/button_cancel_32.png'></a>&nbsp;&nbsp;";
			
		if ($id!="1")
		$res .= "<a href='#' onClick='saveEngine();return false;'><img src='images/button_ok_32.png'></a>&nbsp;&nbsp;";

		if ($rs["id"]!=1) {
			$count_account = mg_row_count($mg, "accounts", array("id_engine" => intval($rs["id"])));
			if ($count_account==0) $res .= "<a href='#' onClick='deleteEngine();return false;'><img src='images/trash_32.png'></a>&nbsp;&nbsp;";
		}

		$res .= "</div></form>";
	}
	print $res;
	exit();
}

if ($action=="display_add_engine")
{
	$res = "<br /><br /><br />";

	$res .= "<form name='engine_add' id='engine_add'>";

	$res .= "<center><table border='0' cellspacing='0' cellpadding='0'>";
	$res .= "<tbody>";

	$res .= "<tr>";
	$res .= "<td class='head'>Name</td>";
	$res .= "<td><input class='editInputText' type='text' name='engine_name' id='engine_name'></td>";
	$res .= "</tr>";

	$res .= "</table></center>";

	$res .= "<input type='hidden' id='action' name ='action' value='createengine'>";

	$res .= "</form>";

	$res .= "<div class='menu_button_on_right'><span id='engine_save_result'></span>";
	$res .= "<a href='#' onClick='cancelEngine();return false;'><img src='images/button_cancel_32.png'></a>&nbsp;&nbsp;";
	$res .= "<a href='#' onClick='createEngine();return false;'><img src='images/button_ok_32.png'></a>&nbsp;&nbsp;&nbsp;";
	$res .= "</div>";

	print $res;
	exit();
}

if ($action=="createengine")
{
	$mg = mg_connect ($config, "", "", "");
	if ($mg)
	{
		$stmt = new mg_stmt_insert($mg, "engines", $mg_engine_defaults);
	
		$stmt->addColumnValueDate("createtime");
		$stmt->addColumnValue("name", $_POST["engine_name"]);

		if (!$stmt->checkNotNull ($mg_engine_not_null)) {
			$res = "Error&nbsp;&nbsp;&nbsp;";
		} else {
			$stmt->execute();
			$res = "Success&nbsp;&nbsp;&nbsp;";
		}
		$res = "Success&nbsp;&nbsp;&nbsp;";
	}

	print ($res);
	exit();
}

if ($action=="saveengine")
{
	$id = $_POST["engine_id"];
	if ($id=="")
	{
		$res = "Error&nbsp;&nbsp;&nbsp;";
		print ($res);
		exit();
	}
	
	$mg = mg_connect ($config, "", "", "");
	if ($mg)
	{
		$stmt = new mg_stmt_update($mg, "engines");
		$query = array ("id" => intval($id));
		$stmt->setQuery ($query);
		$stmt->addColumnValue("name", $_POST["engine_name"]);
	
		$stmt->execute();
		$res = "Success&nbsp;&nbsp;&nbsp;";
	}
	print ($res);
	exit();
}

if ($action=="deleteengine")
{
	$id = $_POST["engine_id"];
	if ($id=="")
	{
		$res = "Error&nbsp;&nbsp;&nbsp;";
		print ($res);
		exit();
	}
	
	$mg = mg_connect ($config, "", "", "");
	if ($mg)
	{
		$stmt = new mg_stmt_delete($mg, "engines");
		$query = array ("id" => intval($id));
		$stmt->setQuery ($query);
		
		$stmt->execute();
		$res = "Success&nbsp;&nbsp;&nbsp;";
	}
	print ($res);
	exit();
}

?>
