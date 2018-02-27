<?php
!defined('IN_SUPU') && exit('Forbidden');

	$tid = (int)getgp('tid');
	$task_info      = $db->find_one("task","AND id='$tid'","*");
	//p($task_info);
	extract(chk_arr($task_info));
	$step = getgp("step");
	if($step == 'basic'){
		
		$up_task = array(
				"auditor_tb_date"	=> $_POST['auditor_tb_date'],
				"auditor_te_date"	=> $_POST['auditor_te_date'],
				);
		$db->update("task",$up_task,array("id"=>$tid));
		$tab = "tab-basic";
	}elseif($step == 'plan'){
		
		$up_task = array(
				"tb_date"	=> $_POST['tb_date'],
				"te_date"	=> $_POST['te_date'],
				"tk_num"			=> $_POST['tk_num'],
				"road_num"			=> $_POST['road_num'],
				);
		$db->update("task",$up_task,array("id"=>$tid));
		$db->update("task_audit_team",array(
										"taskBeginDate" => $_POST['tb_date'],
										"taskEndDate"	=> $_POST['te_date'],
										),array("tid"=>$tid));
		$eid = $db->getField("task","eid",array("id"=>$tid));
		$up_ep = array(
				"person"	=> $_POST['person'],
				"person_tel"	=> $_POST['person_tel'],
				"ep_fax"			=> $_POST['ep_fax'],
				"ep_mail"			=> $_POST['ep_mail'],
				"ep_amount"			=> $_POST['ep_amount'],
				"cta_addrcode"			=> $_POST['cta_addrcode'],
				"cta_addr"			=> $_POST['cta_addr'],
				);
		$db->update("enterprises",$up_ep,array("eid"=>$eid));
		// 处理project
		$count = $db->get_var("SELECT COUNT(*) FROM sp_project WHERE tid = '$tid' AND deleted = 0 AND iso_prod_type = 1");
		$st_num = ($_POST['tk_num'] + $_POST['road_num'])/$count;
		$cost = $st_num*2500;
		$db->update("project",array("st_num"=>$st_num,"cost"=>$cost),array("tid"=>$tid));
		$pids = $db->getCol("project","id",array("tid"=>$tid,"iso_prod_type"=>'1'));
		$db->update("progress",array(	
									"step7"	=> date("Y-m-d H:i:s"),
									"status"	=> "7",
								),array("pid"=>$pids));
		$tab = "tab-plan";
	}elseif($step == 'res'){
		$up_task = array(
				"check_result"	=> $_POST['check_result'],
				"auditor_note"	=> $_POST['auditor_note'],
				"bufuhe"	=> $_POST['bufuhe'],
				);
		if($_POST['check_result'] == '01'){
			$up_task[is_finish] = "1";
			$up_task[task_status] = "4";
		}

		$db->update("task",$up_task,array("id"=>$tid));
		$db->update("enterprises",$up_ep,array("eid"=>$eid));
		$pids = $db->getCol("project","id",array("tid"=>$tid,"iso_prod_type"=>'1'));
		$db->update("progress",array(	
									"step8"	=> date("Y-m-d H:i:s"),
									"status"	=> "8",
								),array("pid"=>$pids));
		if($_POST[clause]){
			foreach($_POST[clause] as $k=>$v){
				if(!$v or !$_POST[note][$k])continue;
				$new_note = array(	"clause" => $v,
									"tid"	=> $tid,
									"note"	=> $_POST[note][$k],
									);
				$db->insert("task_note",$new_note);
				
			}
		}
		$tab = "tab-res";
	}elseif($step == 'rect'){
		$up_task = array(
				"rect_finish"	=> $_POST['rect_finish'],
				"rect_date"	=> date("Y-m-d"),
				"rect_note"	=> $_POST['rect_note'],
				);
		if($_POST['rect_finish'] == '1'){
			$up_task[is_finish] = "1";
			$up_task[task_status] = "4";
		}
		$db->update("task",$up_task,array("id"=>$tid));
		$pids = $db->getCol("project","id",array("tid"=>$tid,"iso_prod_type"=>'1'));
		$db->update("progress",array(	
									"step9"	=> date("Y-m-d H:i:s"),
									"status"	=> "9",
								),array("pid"=>$pids));
		$tab = "tab-rect";
	}elseif($step == 'sample'){
		$data = array(
				"tid"	=> $_POST['tid'],
				"test_org_id"	=> $_POST['test_org_id'],
				"stage"	=> "送报实验室",
				"status"	=> "0",
				);
		if($_POST[certno]){
			foreach($_POST[certid] as $k=>$v){
				if(!$v)continue;
				$new_sample = array(
								"certid" => $v,
								"productname"	=> $_POST[productname][$k],
								"productmodel"	=> $_POST[productmodel][$k],
								"productbase"	=> $_POST[productbase][$k],
								"productnum"	=> $_POST[productnum][$k],
								"productspace"	=> $_POST[productspace][$k],
								"productorder"	=> $_POST[productorder][$k],
								"productdate"	=> $_POST[productdate][$k],
								);
				$new_sample =array_merge($new_sample,$data);
				$db->insert("sample",$new_sample);
			}
		}
		$tab = "tab-sample";
	}elseif($step == 'verify'){
		$up_task = array(
				"vb_date"	=> $_POST['vb_date'],
				"ve_date"	=> $_POST['ve_date'],
				"vk_num"			=> $_POST['vk_num'],
				"vroad_num"			=> $_POST['vroad_num'],
				);
		$db->update("task",$up_task,array("id"=>$tid));
		$count = $db->get_var("SELECT COUNT(*) FROM sp_project WHERE tid = '$tid' AND deleted = 0 AND iso_prod_type = 1");
		$st_num = ($tk_num + $road_num + $_POST['vk_num'] + $_POST['vroad_num'])/$count;
		$cost = $st_num*2500;
		$db->update("project",array("st_num"=>$st_num,"cost"=>$cost),array("tid"=>$tid));
		if($_POST[uid])
			foreach($_POST[uid] as $uid){
				$new_tat = array(
							"tid"	=> $tid,
							"uid"	=> $uid,
							"name"	=> f_username($uid),
							"taskBeginDate"	=> $_POST[vb_date],
							"taskEndDate"	=> $_POST[ve_date],
							// "iso"	=> "B01",
							"ctfrom"	=> $ctfrom,
							"audit_type"	=> $audit_type,
							"is_verify"	=> "1",
							"iso_prod_type"	=> "1"
							);
				$db->insert("task_audit_team",$new_tat);
			}
			$tab = "tab-verify";
	}elseif($step == 'verifyres'){
		$up_task = array(
				"verify_finish"	=> $_POST['verify_finish'],
				"verify_date"	=> date("Y-m-d"),
				"verify_note"	=> $_POST['verify_note'],
				);
		if($_POST['verify_finish'] == '1'){
			$up_task[is_finish] = "1";
			$up_task[task_status] = "4";
		}
		$db->update("task",$up_task,array("id"=>$tid));
		$tab = "tab-verifyres";
	}
    showmsg('success', 'success', "?m=product&c=auditor&a=task_edit&tid=$tid#$tab");