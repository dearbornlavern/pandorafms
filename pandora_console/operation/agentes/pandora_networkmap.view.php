<?php
// ______                 __                     _______ _______ _______
//|   __ \.---.-.-----.--|  |.-----.----.---.-. |    ___|   |   |     __|
//|    __/|  _  |     |  _  ||  _  |   _|  _  | |    ___|       |__     |
//|___|   |___._|__|__|_____||_____|__| |___._| |___|   |__|_|__|_______|
//
// ============================================================================
// Copyright (c) 2007-2010 Artica Soluciones Tecnologicas, http://www.artica.es
// This code is NOT free software. This code is NOT licenced under GPL2 licence
// You cannnot redistribute it without written permission of copyright holder.
// ============================================================================

// Load global variables
global $config;

// Check user credentials
check_login();

require_once ('/include/functions_pandora_networkmap.php');
require_once ('enterprise/include/functions_policies.php');
require_once ('include/functions_modules.php');

//--------------INIT AJAX-----------------------------------------------
if (is_ajax ()) {
	$update_node = (bool)get_parameter('update_node', false);
	$erase_node = (bool)get_parameter('erase_node', false);
	$update_node_color = (bool)get_parameter('update_node_color',
		false);
	$set_relationship = (bool)get_parameter('set_relationship', false);
	$update_refresh_state = (bool)get_parameter('update_refresh_state',
		false);
	$add_agent = (bool)get_parameter('add_agent', false);
	$set_center = (bool)get_parameter('set_center', false);
	$erase_relation = (bool)get_parameter('erase_relation', false);
	$search_agents = (bool) get_parameter ('search_agents');
	$get_agent_pos_search = (bool)get_parameter('get_agent_pos_search',
		false);
	$get_shape_node = (bool)get_parameter('get_shape_node', false);
	$set_shape_node = (bool)get_parameter('set_shape_node', false);
	$get_info_module = (bool)get_parameter('get_info_module', false);
	$get_tooltip_content = (bool)get_parameter('get_tooltip_content',
		false);
	$get_agents_in_group = (bool)get_parameter('get_agents_in_group',
		false);
	$add_several_agents = (bool)get_parameter('add_several_agents',
		false);
	$create_fictional_point = (bool)get_parameter(
		'create_fictional_point', false);
	$update_fictional_point = (bool)get_parameter(
		'update_fictional_point', false);
	$update_z = (bool)get_parameter('update_z', false);
	$module_get_status = (bool)get_parameter('module_get_status', false);
	$delete_node = (bool)get_parameter('delete_node', false);
	$delete_link = (bool)get_parameter('delete_link', false);
	$change_shape = (bool)get_parameter('change_shape', false);
	$get_intefaces = (bool)get_parameter('get_intefaces', false);
	$update_link = (bool)get_parameter('update_link', false);
	$update_fictional_node = (bool)get_parameter('update_fictional_node', false);
	$refresh_holding_area = (bool)get_parameter('refresh_holding_area', false);
	
	if ($refresh_holding_area) {
		$networkmap_id = (int)get_parameter('id', 0);
		
		$return = array();
		$return['correct'] = false;
		$return['holding_area'] = array();
		
		// ACL for the network map
		$id_group = db_get_value('id_group', 'tnetworkmap_enterprise', 'id', $networkmap_id);
		// $networkmap_read = check_acl ($config['id_user'], $id_group, "MR");
		$networkmap_write = check_acl ($config['id_user'], $id_group, "MW");
		$networkmap_manage = check_acl ($config['id_user'], $id_group, "MM");
		
		if (!$networkmap_write && !$networkmap_manage) {
			db_pandora_audit("ACL Violation",
				"Trying to access networkmap enterprise");
			echo json_encode($return);
			return;
		}
		
		$data = networkmap_refresh_holding_area($networkmap_id);
		
		if (!empty($data)) {
			$return['correct'] = true;
			$return['holding_area'] = $data;
		}
		
		echo json_encode($return);
		
		return;
	}
	
	if ($update_fictional_node) {
		$networkmap_id = (int)get_parameter('networkmap_id', 0);
		$node_id = (int)get_parameter('node_id', 0);
		$name = get_parameter('name', "");
		$networkmap_to_link = (int)get_parameter('networkmap_to_link', 0);
		
		$return = array();
		$return['correct'] = false;
		
		// ACL for the network map
		$id_group = db_get_value('id_group', 'tnetworkmap_enterprise', 'id', $networkmap_id);
		// $networkmap_read = check_acl ($config['id_user'], $id_group, "MR");
		$networkmap_write = check_acl ($config['id_user'], $id_group, "MW");
		$networkmap_manage = check_acl ($config['id_user'], $id_group, "MM");
		
		if (!$networkmap_write && !$networkmap_manage) {
			db_pandora_audit("ACL Violation",
				"Trying to access networkmap enterprise");
			echo json_encode($return);
			return;
		}
		
		$node = db_get_row('tnetworkmap_enterprise_nodes', 'id',
			$node_id);
		$node['options'] = json_decode($node['options'], true);
		$node['options']['text'] = $name;
		$node['options']['networkmap'] = $networkmap_to_link;
		$node['options'] = json_encode($node['options']);
		
		$return['correct'] = (bool)db_process_sql_update(
			'tnetworkmap_enterprise_nodes', $node,
			array('id' => $node_id));
		
		echo json_encode($return);
		
		return;
	}
	
	if ($update_link) {
		$networkmap_id = (int)get_parameter('networkmap_id', 0);
		$id_link = (int)get_parameter('id_link', 0);
		$interface_source = (int)get_parameter('interface_source', 0);
		$interface_target = (int)get_parameter('interface_target', 0);
		
		$return = array();
		$return['correct'] = false;
		
		// ACL for the network map
		$id_group = db_get_value('id_group', 'tnetworkmap_enterprise', 'id', $networkmap_id);
		// $networkmap_read = check_acl ($config['id_user'], $id_group, "MR");
		$networkmap_write = check_acl ($config['id_user'], $id_group, "MW");
		$networkmap_manage = check_acl ($config['id_user'], $id_group, "MM");
		
		if (!$networkmap_write && !$networkmap_manage) {
			db_pandora_audit("ACL Violation",
				"Trying to access networkmap enterprise");
			echo json_encode($return);
			return;
		}
		
		$result = networkmap_update_link
			($networkmap_id, $id_link, $interface_source, $interface_target);
		
		if (is_bool($result)) {
			$return['correct'] = $result;
		}
		else
			$return = $result;
		
		echo json_encode($return);
		
		return;
	}
	
	if ($get_intefaces) {
		$id_agent = (int)get_parameter('id_agent', 0);
		
		$return = array();
		$return['correct'] = true;
		$return['interfaces'] = array();
		
		$return['interfaces'] = modules_get_interfaces($id_agent,
			array('id_agente_modulo', 'nombre'));
		
		echo json_encode($return);
		
		return;
	}
	
	if ($change_shape) {
		$networkmap_id = (int)get_parameter('networkmap_id', 0);
		$id = (int)get_parameter('id', 0);
		$shape = get_parameter('shape', 'circle');
		
		$return = array();
		$return['correct'] = false;
		
		// ACL for the network map
		$id_group = db_get_value('id_group', 'tnetworkmap_enterprise', 'id', $networkmap_id);
		// $networkmap_read = check_acl ($config['id_user'], $id_group, "MR");
		$networkmap_write = check_acl ($config['id_user'], $id_group, "MW");
		$networkmap_manage = check_acl ($config['id_user'], $id_group, "MM");
		
		if (!$networkmap_write && !$networkmap_manage) {
			db_pandora_audit("ACL Violation",
				"Trying to access networkmap enterprise");
			echo json_encode($return);
			return;
		}
		
		$node = db_get_row_filter('tnetworkmap_enterprise_nodes',
			array('id_networkmap_enterprise' => $networkmap_id,
				'id' => $id));
		
		$node['options'] = json_decode($node['options'], true);
		$node['options']['shape'] = $shape;
		$node['options'] = json_encode($node['options']);
		
		$return['correct'] = db_process_sql_update('tnetworkmap_enterprise_nodes',
			$node, array('id_networkmap_enterprise' => $networkmap_id,
				'id' => $id));
		echo json_encode($return);
		
		return;
	}
	
	if ($delete_link) {
		$source_id = (int)get_parameter('source_id', 0);
		$source_module_id = (int)get_parameter('source_module_id', 0);
		$target_id = (int)get_parameter('target_id', 0);
		$target_module_id = (int)get_parameter('target_module_id', 0);
		$networkmap_id = (int)get_parameter('networkmap_id', 0);
		$id_link = (int)get_parameter('id_link', 0);
		
		
		$return = array();
		$return['correct'] = false;
		
		// ACL for the network map
		$id_group = db_get_value('id_group', 'tnetworkmap_enterprise', 'id', $networkmap_id);
		// $networkmap_read = check_acl ($config['id_user'], $id_group, "MR");
		$networkmap_write = check_acl ($config['id_user'], $id_group, "MW");
		$networkmap_manage = check_acl ($config['id_user'], $id_group, "MM");
		
		if (!$networkmap_write && !$networkmap_manage) {
			db_pandora_audit("ACL Violation",
				"Trying to access networkmap enterprise");
			echo json_encode($return);
			return;
		}
		
		$return['correct'] = networkmap_delete_link(
			$networkmap_id,
			$source_id,
			$source_module_id,
			$target_id,
			$target_module_id,
			$id_link
			);
		
		echo json_encode($return);
		
		return;
	}
	
	if ($delete_node) {
		$id = (int)get_parameter('id', 0);
		
		$return = array();
		$return['correct'] = false;
		
		$return['correct'] = erase_node(array('id' => $id));
		
		echo json_encode($return);
		
		return;
	}
	
	if ($module_get_status) {
		$id = (int)get_parameter('id', 0);
		
		$return = array();
		$return['correct'] = true;
		$return['status'] = modules_get_agentmodule_status(
			$id, false, false, null);
		
		echo json_encode($return);
		return;
	}
	
	if ($update_z) {
		$node = (int)get_parameter('node', 0);
		
		$return = array();
		$return['correct'] = false;
		
		$z = db_get_value('z', 'tnetworkmap_enterprise_nodes', 'id',
			$node);
		
		$z++;
		
		$return['correct'] = (bool)db_process_sql_update(
			'tnetworkmap_enterprise_nodes', array('z' => $z),
			array('id' => $node));
		
		echo json_encode($return);
		
		return;
	}
	
	if ($update_fictional_point) {
		$id_node = (int)get_parameter('id_node', 0);
		$name = io_safe_output(get_parameter('name', ''));
		$shape = get_parameter('shape', 0);
		$radious = (int)get_parameter('radious', 20);
		$color = get_parameter('color', 0);
		$networkmap = (int)get_parameter('networkmap', 0);
		
		$return = array();
		$return['correct'] = false;
		
		$row = db_get_row('tnetworkmap_enterprise_nodes', 'id',
			$id_node);
		$row['options'] = json_decode($row['options'], true);
		$row['options']['shape'] = $shape;
		//WORK AROUND FOR THE JSON ENCODE WITH FOR EXAMPLE Ñ OR Á
		$row['options']['text'] = 'json_encode_crash_with_ut8_chars';
		$row['options']['color'] = $color;
		$row['options']['networkmap'] = $networkmap;
		$row['options']['width'] = $radious * 2;
		$row['options']['height'] = $radious * 2;
		$row['options'] = json_encode($row['options']);
		$row['options'] = str_replace(
			'json_encode_crash_with_ut8_chars', $name, $row['options']);
		
		$return['correct'] = (bool)db_process_sql_update(
			'tnetworkmap_enterprise_nodes', $row,
			array('id' => $id_node));
		
		if ($return['correct']) {
			$return['id_node'] = $id_node;
			$return['shape'] = $shape;
			$return['width'] = $radious * 2;
			$return['height'] = $radious * 2;
			$return['text'] = $name;
			$return['color'] = $color;
			$return['networkmap'] = $networkmap;
			
			$return['message'] = __('Success be updated.');
		}
		else {
			$return['message'] = __('Could not be updated.');
		}
		
		echo json_encode($return);
		
		return;
	}
	
	
	if ($create_fictional_point) {
		$id = (int)get_parameter('id', 0);
		$x = (int)get_parameter('x', 0);
		$y = (int)get_parameter('y', 0);
		$name = io_safe_output(get_parameter('name', ''));
		$shape = get_parameter('shape', 0);
		$radious = (int)get_parameter('radious', 20);
		$color = get_parameter('color', 0);
		$networkmap = (int)get_parameter('networkmap', 0);
		
		$return = array();
		$return['correct'] = false;
		
		
		$data = array();
		$data['id_networkmap_enterprise'] = $id;
		$data['x'] = $x;
		$data['y'] = $y;
		$data['id_agent'] = -2; //The id for the fictional points.
		$data['parent'] = 0;
		$options = array();
		$options['shape'] = $shape;
		$options['image'] = '';
		$options['width'] = $radious * 2;
		$options['height'] = $radious * 2;
		//WORK AROUND FOR THE JSON ENCODE WITH FOR EXAMPLE Ñ OR Á
		$options['text'] = 'json_encode_crash_with_ut8_chars';
		$options['color'] = $color;
		$options['networkmap'] = $networkmap;
		$data['options'] = json_encode($options);
		$data['options'] = str_replace(
			'json_encode_crash_with_ut8_chars', $name,
			$data['options']);
		
		$id_node = db_process_sql_insert('tnetworkmap_enterprise_nodes',
			$data);
		
		$return['correct'] = (bool)$id_node;
		
		if ($return['correct']) {
			$return['id_node'] = $id_node;
			$return['id_agent'] = -2; //The finctional point id
			$return['parent'] = 0;
			$return['shape'] = $shape;
			$return['image'] = '';
			$return['width'] = $radious * 2;
			$return['height'] = $radious * 2;
			$return['text'] = $name;
			$return['color'] = $color;
			$return['networkmap'] = $networkmap;
		}
		
		echo json_encode($return);
		
		return;
	}
	
	if ($add_several_agents) {
		$id = (int)get_parameter('id', 0);
		$x = (int)get_parameter('x', 0);
		$y = (int)get_parameter('y', 0);
		$id_agents = io_safe_output(get_parameter('id_agents', ''));
		
		$id_agents = json_decode($id_agents, true);
		if ($id_agents === null)
			$id_agents = array();
		
		$return = array();
		$return['correct'] = true;
		
		$count = 0;
		foreach ($id_agents as $id_agent) {
			$id_node = add_agent_networkmap($id, '',
				$x + ($count * 20), $y + ($count * 20), $id_agent);
			
			if ($id_node !== false) {
				$node = db_get_row('tnetworkmap_enterprise_nodes', 'id',
					$id_node);
				$options = json_decode($node['options'], true);
				
				$data = array();
				$data['id_node'] = $id_node;
				$data['id_agent'] = $node['id_agent'];
				$data['parent'] = $node['parent'];
				$data['shape'] = $options['shape'];
				$data['image'] = $options['image'];
				$data['width'] = $options['width'];
				$data['height'] = $options['height'];
				$data['text'] = $options['text'];
				$data['x'] = $node['x'];
				$data['y'] = $node['y'];
				$data['status'] = get_status_color_networkmap(
					$id_agent);
				$return['nodes'][] = $data;
			}
			$count++;
		}
		
		echo json_encode($return);
		
		return;
	}
	
	if ($get_agents_in_group) {
		$id = (int)get_parameter('id', 0);
		$group = (int)get_parameter('group', -1);
		
		$return = array();
		$return['correct'] = false;
		
		if ($group != -1) {
			$where_id_agente = ' 1=1 ';
			
			$agents_in_networkmap = db_get_all_rows_filter(
				'tnetworkmap_enterprise_nodes',
				array('id_networkmap_enterprise' => $id,
					'deleted' => 0));
			if ($agents_in_networkmap !== false) {
				$ids = array();
				foreach ($agents_in_networkmap as $agent) {
					$ids[] = $agent['id_agent'];
				}
				$where_id_agente = ' id_agente NOT IN (' .
					implode(',', $ids) . ')';
			}
			
			
			$sql = 'SELECT id_agente, nombre
				FROM tagente
				WHERE id_grupo = ' . $group . ' AND ' .
					$where_id_agente . ' 
				ORDER BY nombre ASC';
			
			$agents = db_get_all_rows_sql($sql);
			
			if ($agents !== false) {
				$return['agents'] = array();
				foreach ($agents as $agent) {
					$return['agents'][$agent['id_agente']] =
						$agent['nombre'];
				}
				
				$return['correct'] = true;
			}
		}
		
		echo json_encode($return);
		
		return;
	}
	
	if ($get_tooltip_content) {
		$id = (int)get_parameter('id', 0);
		
		
		// Get all module from agent
		switch ($config["dbtype"]) {
			case "mysql":
			case "postgresql":
				$sql = sprintf ("
					SELECT *
					FROM tagente_estado, tagente_modulo
						LEFT JOIN tmodule_group
						ON tmodule_group.id_mg = tagente_modulo.id_module_group
					WHERE tagente_modulo.id_agente_modulo = " . $id . "
						AND tagente_estado.id_agente_modulo = tagente_modulo.id_agente_modulo
						AND tagente_modulo.disabled = 0
						AND tagente_modulo.delete_pending = 0
						AND tagente_estado.utimestamp != 0");
				break;
			// If Dbms is Oracle then field_list in sql statement has to be recoded. See oracle_list_all_field_table()
			case "oracle":
				$fields_tagente_estado = oracle_list_all_field_table(
					'tagente_estado', 'string');
				$fields_tagente_modulo = oracle_list_all_field_table(
					'tagente_modulo', 'string');
				$fields_tmodule_group = oracle_list_all_field_table(
					'tmodule_group', 'string');
				
				$sql = sprintf ("
					SELECT " . $fields_tagente_estado . ', ' .
						$fields_tagente_modulo . ', ' .
						$fields_tmodule_group .
					" FROM tagente_estado, tagente_modulo
						LEFT JOIN tmodule_group
						ON tmodule_group.id_mg = tagente_modulo.id_module_group
					WHERE tagente_modulo.id_agente_modulo = " . $id . "
						AND tagente_estado.id_agente_modulo = tagente_modulo.id_agente_modulo
						AND tagente_modulo.disabled = 0
						AND tagente_modulo.delete_pending = 0
						AND tagente_estado.utimestamp != 0");
				break;
		}
		
		$modules = db_get_all_rows_sql ($sql);
		if (empty ($modules)) {
			$module = array ();
		}
		else {
			$module = $modules[0];
		}
		
		
		
		$return = array();
		$return['correct'] = true;
		
		$return['content'] = '<div style="border: 1px solid black;">
			<div style="width: 100%; text-align: right;"><a style="text-decoration: none; color: black;" href="javascript: hide_tooltip();">X</a></div>
			<div style="margin: 5px;">
			';
		
		$return['content'] .=
			"<b>" . __('Name: ') . "</b>" .
			ui_print_string_substr($module["nombre"], 30, true) .
			"<br />";
		
		if ($module["id_policy_module"]) {
			$linked = policies_is_module_linked(
				$module['id_agente_modulo']);
			$id_policy = db_get_value_sql('
				SELECT id_policy
				FROM tpolicy_modules
				WHERE id = ' . $module["id_policy_module"]);
			
			if ($id_policy != "")
				$name_policy = db_get_value_sql(
					'SELECT name
					FROM tpolicies
					WHERE id = ' . $id_policy);
			else
				$name_policy = __("Unknown");
			
			$policyInfo = policies_info_module_policy(
				$module["id_policy_module"]);
			
			$adopt = false;
			if (policies_is_module_adopt($module['id_agente_modulo'])) {
				$adopt = true;
			}
			
			if ($linked) {
				if ($adopt) {
					$img = 'images/policies_brick.png';
					$title = __('(Adopt) ') . $name_policy;
				}
				else {
					$img = 'images/policies.png';
						$title = $name_policy;
				}
			}
			else {
				if ($adopt) {
					$img = 'images/policies_not_brick.png';
					$title = __('(Unlinked) (Adopt) ') . $name_policy;
				}
				else {
					$img = 'images/unlinkpolicy.png';
					$title = __('(Unlinked) ') . $name_policy;
				}
			}
			
			$return['content'] .=
				"<b>" . __('Policy: ') . "</b>" . $title . "<br />";
		}
		
		$status = STATUS_MODULE_WARNING;
		$title = "";
		
		if ($module["estado"] == 1) {
			$status = STATUS_MODULE_CRITICAL;
			$title = __('CRITICAL');
		}
		elseif ($module["estado"] == 2) {
			$status = STATUS_MODULE_WARNING;
			$title = __('WARNING');
		}
		elseif ($module["estado"] == 0) {
			$status = STATUS_MODULE_OK;
			$title = __('NORMAL');
		}
		elseif ($module["estado"] == 3) {
			$last_status =  modules_get_agentmodule_last_status(
				$module['id_agente_modulo']);
			switch($last_status) {
				case 0:
					$status = STATUS_MODULE_OK;
					$title = __('UNKNOWN') . " - " . __('Last status') .
						" " . __('NORMAL');
					break;
				case 1:
					$status = STATUS_MODULE_CRITICAL;
					$title = __('UNKNOWN') . " - " . __('Last status') .
						" " . __('CRITICAL');
					break;
				case 2:
					$status = STATUS_MODULE_WARNING;
					$title = __('UNKNOWN') . " - " . __('Last status') .
						" " . __('WARNING');
					break;
			}
		}
		
		if (is_numeric($module["datos"])) {
			$title .= ": " . format_for_graph($module["datos"]);
		}
		else {
			$title .= ": " . substr(io_safe_output($module["datos"]), 0,
				42);
		}
		
		$return['content'] .=
			"<b>" . __('Status: ') . "</b>" .
			ui_print_status_image($status, $title, true) . "<br />";
		
		
		
		if ($module["id_tipo_modulo"] == 24) { // log4x
			switch($module["datos"]) {
				case 10:
					$salida = "TRACE";
					$style = "font-weight:bold; color:darkgreen;";
					break;
				case 20:
					$salida = "DEBUG";
					$style = "font-weight:bold; color:darkgreen;";
					break;
				case 30:
					$salida = "INFO";
					$style = "font-weight:bold; color:darkgreen;";
					break;
				case 40:
					$salida = "WARN";
					$style = "font-weight:bold; color:darkorange;";
					break;
				case 50:
					$salida = "ERROR";
					$style = "font-weight:bold; color:red;";
					break;
				case 60:
					$salida = "FATAL";
					$style = "font-weight:bold; color:red;";
					break;
			}
			$salida = "<span style='$style'>$salida</span>";
		}
		else {
			if (is_numeric($module["datos"])) {
				$salida = format_numeric($module["datos"]);
			}
			else {
				$salida = ui_print_module_string_value(
					$module["datos"], $module["id_agente_modulo"],
					$module["current_interval"], $module["module_name"]);
			}
		}
		
		$return['content'] .=
				"<b>" . __('Data: ') . "</b>" . $salida . "<br />";
		
		$return['content'] .=
				"<b>" . __('Last contact: ') . "</b>" .
				ui_print_timestamp ($module["utimestamp"], true,
					array('style' => 'font-size: 7pt')) .
				"<br />";
		
		$return['content'] .= '
			</div>
		</div>';
		
		echo json_encode($return);
		
		return;
	}
	
	if ($set_shape_node) {
		$id = (int)get_parameter('id', 0);
		$shape = get_parameter('shape', 'circle');
		
		$return = array();
		$return['correct'] = false;
		
		$node = db_get_row_filter('tnetworkmap_enterprise_nodes',
			array('id' => $id));
		$options = json_decode($node['options'], true);
		
		$options['shape'] = $shape;
		$options = json_encode($options);
		
		$return['correct'] = db_process_sql_update(
			'tnetworkmap_enterprise_nodes',
			array('options' => $options), array('id' => $id));
		
		
		echo json_encode($return);
		
		return;
	}
	
	if ($get_shape_node) {
		$id = (int)get_parameter('id', 0);
		
		$return = array();
		$return['correct'] = true;
		
		$node = db_get_row_filter('tnetworkmap_enterprise_nodes',
			array('id' => $id));
		$node['options'] = json_decode($node['options'], true);
		
		$return['shape'] = $node['options']['shape'];
		
		echo json_encode($return);
		
		return;
	}
	
	if ($get_agent_pos_search) {
		$id = (int)get_parameter('id', 0);
		$name = io_safe_output((string)get_parameter('name', 0));
		
		$return = array();
		$return['correct'] = true;
		
		$node = db_get_row_filter('tnetworkmap_enterprise_nodes',
			array('id_networkmap_enterprise' => $id,
				'options' => '%\"text\":\"%' . $name . '%\"%'));
		$return['x'] = $node['x']; 
		$return['y'] = $node['y']; 
		
		
		echo json_encode($return);
		
		return;
	}
	
	if ($search_agents) {
		require_once ('include/functions_agents.php');
		
		$id = (int)get_parameter('id', 0);
		/* q is what autocomplete plugin gives */
		$string = io_safe_output((string) get_parameter ('q'));
		
		$agents = db_get_all_rows_filter('tnetworkmap_enterprise_nodes',
			array('id_networkmap_enterprise' => $id,
				'options' => '%\"text\":\"%' . $string . '%\"%'));
		
		if ($agents === false)
			$agents = array();
		
		$data = array();
		foreach ($agents as $agent) {
			$options = json_decode($agent['options'], true);
			$data[] = array('name' => io_safe_output($options['text']));
		}
		
		echo json_encode($data);
		
		return;
 	}
	
	if ($update_node) {
		$node_json = io_safe_output(get_parameter('node', ''));
		
		$node = json_decode($node_json, true);
		
		echo json_encode(update_node($node));
		
		return;
	}
	
	if ($erase_node) {
		$node_json = io_safe_output(get_parameter('node', ''));
		
		$node = json_decode($node_json, true);
		
		$return = array();
		$return['correct'] = false;
		
		$return['correct'] = erase_node($node['id']);
		$return['old_id'] = $node['id'];
		
		echo json_encode($return);
		
		return;
	}
	
	if ($update_node_color) {
		$id = (int)get_parameter('id', 0);
		
		
		$id_agent = db_get_value('id_agent',
			'tnetworkmap_enterprise_nodes', 'id', $id);
		
		$return = array();
		$return['correct'] = true;
		if ($id_agent != -2) {
			$return['color'] = get_status_color_networkmap($id_agent);
		}
		else {
			$options = db_get_value('options',
				'tnetworkmap_enterprise_nodes', 'id', $id);
			$options = json_decode($options, true);
			if ($options['networkmap'] == 0) {
				$return['color'] = $options['color'];
			}
			else {
				$return['color'] =
					get_status_color_networkmap_fictional_point(
						$options['networkmap']);
			}
		}
		
		echo json_encode($return);
		
		return;
	}
	
	if ($set_relationship) {
		$id = (int)get_parameter('id');
		$child = (int)get_parameter('child'); 
		$parent = (int)get_parameter('parent');
		
		$correct = db_process_sql_insert(
			'tnetworkmap_ent_rel_nodes',
			array('id_networkmap_enterprise' => $id,
				'parent' => $parent,
				'child' => $child));
		
		$return = array();
		$return['correct'] = false;
		
		if ($correct) {
			$return['correct'] = true;
			$return['id'] = $correct;
			$return['child'] = $child;
			$return['parent'] = $parent;
		}
		
		echo json_encode($return);
		
		return;
	}
	
	if ($update_refresh_state) {
		$refresh_state = (int)get_parameter('refresh_state', 60);
		$id = (int)get_parameter('id', 0);
		
		$options = db_get_value('options', 'tnetworkmap_enterprise',
			'id', $id);
		$options = json_decode($options, true);
		$options['refresh_state'] = $refresh_state;
		$options = json_encode($options);
		
		$correct = db_process_sql_update('tnetworkmap_enterprise',
			array('options' => $options), array('id' => $id));
		
		$return = array();
		$return['correct'] = false;
		
		if ($correct)
			$return['correct'] = true;
		
		echo json_encode($return);
		
		return;
	}
	
	if ($add_agent) {
		$id = (int)get_parameter('id', 0);
		$agent = get_parameter('agent', '');
		$x = (int)get_parameter('x', 0);
		$y = (int)get_parameter('y', 0);
		$id_agent = (int)get_parameter('id_agent', -1);
		if ($id_agent == -1)
			$id_agent = agents_get_agent_id($agent);
		
		$return = array();
		$return['correct'] = false;
		
		$id_node = add_agent_networkmap($id, $agent, $x, $y, $id_agent);
		
		if ($id_node !== false) {
			$return['correct'] = true;
			
			$node = db_get_row('tnetworkmap_enterprise_nodes', 'id',
				$id_node);
			$options = json_decode($node['options'], true);
			
			$return['id_node'] = $id_node;
			$return['id_agent'] = $node['id_agent'];
			$return['parent'] = $node['parent'];
			$return['shape'] = $options['shape'];
			$return['image'] = $options['image'];
			$return['image_url'] = html_print_image(
				$options['image'], true, false, true);
			$return['width'] = $options['width'];
			$return['height'] = $options['height'];
			$return['text'] = $options['text'];
			$return['x'] = $x;
			$return['y'] = $y;
			$return['status'] = get_status_color_networkmap($id_agent);
		}
		
		echo json_encode($return);
		
		return;
	}
	
	if ($set_center) {
		$id = (int)get_parameter('id', 0);
		$x = (int)get_parameter('x', 0);
		$y = (int)get_parameter('y', 0);
		
		$networkmap = db_get_row('tnetworkmap_enterprise', 'id', $id);
		
		// ACL for the network map
		// $networkmap_read = check_acl ($config['id_user'], $networkmap['id_group'], "MR");
		$networkmap_write = check_acl ($config['id_user'], $networkmap['id_group'], "MW");
		$networkmap_manage = check_acl ($config['id_user'], $networkmap['id_group'], "MM");
		
		if (!$networkmap_write && !$networkmap_manage) {
			db_pandora_audit("ACL Violation",
				"Trying to access networkmap enterprise");
			echo json_encode($return);
			return;
		}
		
		$options = json_decode($networkmap['options'], true);
		$options['center_x'] = $x;
		$options['center_y'] = $y;
		$networkmap['options'] = json_encode($options);
		db_process_sql_update('tnetworkmap_enterprise',
			array('options' => $networkmap['options']),
			array('id' => $id));
		
		$return = array();
		$return['correct'] = true;
		
		echo json_encode($return);
		
		return;
	}
	
	if ($erase_relation) {
		$id = (int)get_parameter('id', 0);
		$child = (int)get_parameter('child', 0);
		$parent = (int)get_parameter('parent', 0);
		
		$where = array();
		$where['id_networkmap_enterprise'] = $id;
		$where['child'] = $child;
		$where['parent'] = $parent;
		
		$return = array();
		$return['correct'] = db_process_sql_delete(
			'tnetworkmap_ent_rel_nodes', $where);
		
		echo json_encode($return);
		
		return;
	}
	
	
	//Popup
	$get_status_node = (bool)get_parameter('get_status_node', false);
	$get_status_module = (bool)get_parameter('get_status_module',
		false);
	$check_changes_num_modules = (bool)get_parameter(
		'check_changes_num_modules', false);
	
	if ($get_status_node) {
		$id = (int)get_parameter('id', 0);
		
		$return = array();
		$return['correct'] = true;
		
		$return['status_agent'] = get_status_color_networkmap($id);
		
		echo json_encode($return);
		
		return;
	}
	
	if ($get_status_module) {
		$id = (int)get_parameter('id', 0);
		
		$return = array();
		$return['correct'] = true;
		$return['id'] = $id;
		$return['status_color'] = get_status_color_module_networkmap(
			$id);
		
		echo json_encode($return);
		
		return;
	}
	
	if ($check_changes_num_modules) {
		$id = (int)get_parameter('id', 0);
		
		$modules = agents_get_modules($id);
		
		$return = array();
		$return['correct'] = true;
		$return['count'] = count($modules);
		
		echo json_encode($return);
		
		return;
	}
}
//--------------END AJAX------------------------------------------------



$id = (int) get_parameter('id_networkmap', 0);

$networkmap = db_get_row('tnetworkmap_enterprise', 'id', $id);

if ($networkmap === false) {
	ui_print_page_header(__('Networkmap enterprise'),
		"images/bricks.png", false, "network_map_enterprise", false);
	ui_print_error_message(__('Not found networkmap.'));
	
	return;
}
else {
	// ACL for the network map
	$networkmap_read = check_acl ($config['id_user'], $networkmap['id_group'], "MR");
	$networkmap_write = check_acl ($config['id_user'], $networkmap['id_group'], "MW");
	$networkmap_manage = check_acl ($config['id_user'], $networkmap['id_group'], "MM");
	
	if (!$networkmap_read && !$networkmap_write && !$networkmap_manage) {
		db_pandora_audit("ACL Violation",
			"Trying to access networkmap enterprise");
		require ("general/noaccess.php");
		return;
	}
	
	$user_readonly = !$networkmap_write && !$networkmap_manage;
	
	$pure = (int) get_parameter ('pure', 0);
	
	/* Main code */
	if ($pure == 1) {
		$buttons['screen'] = array('active' => false,
			'text' => '<a href="index.php?sec=networkmapconsole&amp;' .
				'sec2=operation/agentes/pandora_networkmap&amp;' .
				'tab=view&amp;id_networkmap=' . $id . '">' . 
				html_print_image("images/normal_screen.png", true,
					array ('title' => __('Normal screen'))) .
				'</a>');
	}
	else {
		$buttons['screen'] = array('active' => false,
			'text' => '<a href="index.php?sec=networkmapconsole&amp;' .
				'sec2=operation/agentes/pandora_networkmap&amp;' .
				'pure=1&amp;tab=view&amp;id_networkmap=' . $id . '">' . 
				html_print_image("images/full_screen.png", true,
					array ('title' => __('Full screen'))) .
				'</a>');
		$buttons['list'] = array('active' => false,
			'text' => '<a href="index.php?sec=networkmapconsole&amp;' .
				'sec2=operation/agentes/pandora_networkmap">' . 
				html_print_image("images/list.png", true,
					array ('title' => __('List of networkmap Enterprise'))) .
				'</a>');
	}
	
	ui_print_page_header(sprintf(__('Networkmap - %s'),
		io_safe_output($networkmap['name'])), "images/bricks.png",
		false, "network_map_enterprise", false, $buttons);
	
	$numNodes = (int)db_get_num_rows('
		SELECT *
		FROM tnetworkmap_enterprise_nodes
		WHERE id_networkmap_enterprise = ' . $id . ';');
	
	if ($numNodes == 0) {
		networkmap_process_networkmap($id);
	}
	
	show_networkmap($id, $user_readonly);
}
?>
