<?php
require_once('../config.php');
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
	function delete_img(){
		extract($_POST);
		if(is_file($path)){
			if(unlink($path)){
				$resp['status'] = 'success';
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = 'failed to delete '.$path;
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = 'Unkown '.$path.' path';
		}
		return json_encode($resp);
	}
	function save_category(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$v = $this->conn->real_escape_string($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `category_list` set {$data} ";
		}else{
			$sql = "UPDATE `category_list` set {$data} where id = '{$id}' ";
		}
			$save = $this->conn->query($sql);
		if($save){
			$aid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['status'] = 'success';
			$resp['aid'] = $aid;

			if(empty($id))
				$resp['msg'] = "New Category successfully saved.";
			else
				$resp['msg'] = " Category successfully updated.";
			
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
			return json_encode($resp);
	}
	function delete_category(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `category_list` set delete_flag = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Category successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_client(){
		if(empty($_POST['id'])){
			$prefix = date("Ymd");
			$code = sprintf("%'.04d", 1);
			while(true){
				$check = $this->conn->query("SELECT id FROM `client_list` where code = '{$prefix}{$code}' and delete_flag = 0 ".(isset($id) ? " and id !='{$id}' " : "" )." ")->num_rows;
				if($check > 0){
					$code = sprintf("%'.04d", abs($code) + 1);
				}else{
					$_POST['code'] = $prefix.$code;
					break;
				}
			}
		}
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$v = $this->conn->real_escape_string($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT id FROM `client_list` where meter_code = '{$meter_code}' and delete_flag = 0 ".(isset($id) ? " and id !='{$id}' " : "" )." ")->num_rows;
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Meter Code already exist.';
			return json_encode($resp);
		}
		if(empty($id)){
			$sql = "INSERT INTO `client_list` set {$data} ";
		}else{
			$sql = "UPDATE `client_list` set {$data} where id = '{$id}' ";
		}
			$save = $this->conn->query($sql);
		if($save){
			$aid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['status'] = 'success';
			$resp['aid'] = $aid;

			// Fetch the saved client data for immediate table update
			$client_query = $this->conn->query("SELECT *, CONCAT(lastname, ', ', firstname, ' ', COALESCE(middlename,'')) AS fullname FROM client_list WHERE id = '{$aid}'");
			if($client_query && $client_query->num_rows > 0) {
				$resp['client_data'] = $client_query->fetch_assoc();
			}

			if(empty($id))
				$resp['msg'] = "New Client successfully saved.";
			else
				$resp['msg'] = " Client successfully updated.";
			
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_client(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `client_list` set delete_flag = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Client successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function get_previous_reading(){
		extract($_POST);
		
		// Always set previous reading to 0 so computation is based only on current reading
		$resp['status'] = 'success';
		$resp['previous'] = 0;
		
		return json_encode($resp);
	}
	function save_billing(){
		extract($_POST);
		$data = "";
		
		// Check if client already has an unpaid bill (for new bills only)
		if(empty($id)){
			$check_unpaid = $this->conn->query("SELECT COUNT(*) as unpaid_count FROM `billing_list` WHERE client_id = '{$client_id}' AND status = 0");
			$unpaid_result = $check_unpaid->fetch_assoc();
			
			if($unpaid_result['unpaid_count'] > 0){
				$resp['status'] = 'failed';
				$resp['msg'] = 'This client already has an unpaid bill. Please settle the existing bill before creating a new one.';
				return json_encode($resp);
			}
		}
		
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$v = $this->conn->real_escape_string($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}
		
		if(empty($id)){
			$sql = "INSERT INTO `billing_list` set {$data} ";
		}else{
			$sql = "UPDATE `billing_list` set {$data} where id = '{$id}' ";
		}
			$save = $this->conn->query($sql);
		if($save){
			$aid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['status'] = 'success';
			$resp['aid'] = $aid;

			if(empty($id))
				$resp['msg'] = "New Billing Statement has been saved successfully.";
			else
				$resp['msg'] = " Billing Statement has been updated successfully.";
			
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
			return json_encode($resp);
	}
	
	function get_billing_history(){
		extract($_POST);
		$qry = $this->conn->query("SELECT b.*, c.code, concat(c.lastname, ', ', c.firstname, ' ', coalesce(c.middlename,'')) as `name` 
								   FROM `billing_list` b 
								   INNER JOIN client_list c ON b.client_id = c.id 
								   WHERE b.client_id = '{$client_id}' 
								   ORDER BY unix_timestamp(b.reading_date) DESC");
		
		if($qry->num_rows > 0){
			$resp['status'] = 'success';
			$resp['data'] = array();
			while($row = $qry->fetch_assoc()){
				$resp['data'][] = $row;
			}
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = 'No billing history found for this client.';
		}
		return json_encode($resp);
	}
	
	function get_bill_details(){
		extract($_POST);
		$qry = $this->conn->query("SELECT b.*, c.code, c.meter_code, c.contact, c.address,
								   concat(c.lastname, ', ', c.firstname, ' ', coalesce(c.middlename,'')) as `client_name` 
								   FROM `billing_list` b 
								   INNER JOIN client_list c ON b.client_id = c.id 
								   WHERE b.id = '{$id}'");
		
		if($qry->num_rows > 0){
			$resp['status'] = 'success';
			$resp['data'] = $qry->fetch_assoc();
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = 'Bill not found.';
		}
		return json_encode($resp);
	}
	
	function delete_billing(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `billing_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Billing Statement has been deleted successfully.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	
	function save_issue(){
		extract($_POST);
		$data = "";
		
		// Handle image upload
		$image_path = '';
		if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
			$upload_dir = '../uploads/issues/';
			if(!is_dir($upload_dir)){
				mkdir($upload_dir, 0777, true);
			}
			
			$file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
			$new_filename = date('YmdHis') . '_' . uniqid() . '.' . $file_extension;
			$upload_path = $upload_dir . $new_filename;
			
			if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)){
				$image_path = $new_filename;
			}
		}
		
		// If editing and removing image
		if(!empty($id) && isset($remove_image) && $remove_image == 1){
			$old_qry = $this->conn->query("SELECT image_path FROM `client_issue_list` WHERE id = '{$id}'");
			if($old_qry->num_rows > 0){
				$old_row = $old_qry->fetch_assoc();
				if(!empty($old_row['image_path']) && file_exists('../uploads/issues/' . $old_row['image_path'])){
					unlink('../uploads/issues/' . $old_row['image_path']);
				}
			}
			$image_path = '';
		}
		
		foreach($_POST as $k => $v){
			if(!in_array($k,array('id', 'remove_image'))){
				if(!empty($data)) $data .=",";
				$v = $this->conn->real_escape_string($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}
		
		// Add image path to data if we have one
		if(!empty($image_path) || (isset($remove_image) && $remove_image == 1)){
			if(!empty($data)) $data .=",";
			$data .= " `image_path`='{$image_path}' ";
		}
		
		// If status is resolved (1), set date_resolved
		if(isset($_POST['status']) && $_POST['status'] == 1){
			if(!empty($data)) $data .=",";
			$data .= " `date_resolved`=NOW() ";
		}
		
		if(empty($id)){
			$sql = "INSERT INTO `client_issue_list` set {$data} ";
		}else{
			$sql = "UPDATE `client_issue_list` set {$data} where id = '{$id}' ";
		}
		
		$save = $this->conn->query($sql);
		if($save){
			$aid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['status'] = 'success';
			$resp['aid'] = $aid;

			if(empty($id))
				$resp['msg'] = "New Issue successfully saved.";
			else
				$resp['msg'] = "Issue successfully updated.";
			
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	
	function delete_issue(){
		extract($_POST);
		
		// Get image path before deleting
		$img_qry = $this->conn->query("SELECT image_path FROM `client_issue_list` WHERE id = '{$id}'");
		if($img_qry->num_rows > 0){
			$img_row = $img_qry->fetch_assoc();
			if(!empty($img_row['image_path']) && file_exists('../uploads/issues/' . $img_row['image_path'])){
				unlink('../uploads/issues/' . $img_row['image_path']);
			}
		}
		
		$del = $this->conn->query("DELETE FROM `client_issue_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Issue has been deleted successfully.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
	function update_issue_status(){
		extract($_POST);
		
		// If marking as resolved, set the date_resolved to current timestamp
		// If marking as pending, clear the date_resolved
		if($status == 1) {
			$update = $this->conn->query("UPDATE `client_issue_list` SET `status` = '{$status}', `date_resolved` = NOW() WHERE id = '{$id}'");
		} else {
			$update = $this->conn->query("UPDATE `client_issue_list` SET `status` = '{$status}', `date_resolved` = NULL WHERE id = '{$id}'");
		}
		
		if($update){
			$resp['status'] = 'success';
			$status_text = $status == 1 ? 'resolved' : 'pending';
			$this->settings->set_flashdata('success',"Issue has been marked as {$status_text}.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'delete_img':
		echo $Master->delete_img();
	break;
	case 'save_category':
		echo $Master->save_category();
	break;
	case 'delete_category':
		echo $Master->delete_category();
	break;
	case 'save_client':
		echo $Master->save_client();
	break;
	case 'delete_client':
		echo $Master->delete_client();
	break;
	case 'get_previous_reading':
		echo $Master->get_previous_reading();
	break;
	case 'save_billing':
		echo $Master->save_billing();
	break;
	case 'get_billing_history':
		echo $Master->get_billing_history();
	break;
	case 'get_bill_details':
		echo $Master->get_bill_details();
	break;
	case 'delete_billing':
		echo $Master->delete_billing();
	break;
	case 'save_issue':
		echo $Master->save_issue();
	break;
	case 'delete_issue':
		echo $Master->delete_issue();
	break;
	case 'update_issue_status':
		echo $Master->update_issue_status();
	break;
	default:
		// echo $sysset->index();
		break;
}