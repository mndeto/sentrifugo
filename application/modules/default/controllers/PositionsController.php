<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2014 Sapplica
 *   
 *  Sentrifugo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Sentrifugo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Sentrifugo.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  Sentrifugo Support <support@sentrifugo.com>
 ********************************************************************************/

class Default_PositionsController extends Zend_Controller_Action
{

	private $options;
	public function preDispatch()
	{
			

	}

	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();

	}

	public function indexAction()
	{
		$positionsmodel = new Default_Model_Positions();
		$call = $this->_getParam('call');
		if($call == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		$view = Zend_Layout::getMvcInstance()->getView();
		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$dashboardcall = $this->_getParam('dashboardcall',null);
		$data = array();		$searchQuery = '';		$searchArray = array();		$tablecontent='';

		if($refresh == 'refresh')
		{
			if($dashboardcall == 'Yes')
			$perPage = DASHBOARD_PERPAGE;
			else
			$perPage = PERPAGE;

			$sort = 'DESC';$by = 'p.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';
			$searchArray = array();
			//$sort = 'DESC';$by = 'p.modifieddate';$perPage = 10;$pageNo = 1;$searchData = '';
		}
		else
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'p.modifieddate';
			if($dashboardcall == 'Yes')
			$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else
			$perPage = $this->_getParam('per_page',PERPAGE);

			$pageNo = $this->_getParam('page', 1);
			$searchData = $this->_getParam('searchData');
			$searchData = rtrim($searchData,',');
		}
		$dataTmp = $positionsmodel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall);
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}

	public function addAction()
	{
		$emptyFlag=0;
		$msgarray = array();
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		
	    $popConfigPermission = sapp_Global::_checkprivileges(PREFIX,$loginuserGroup,$loginuserRole,'add');
	    $this->view->popConfigPermission = $popConfigPermission;

			
		$positionsform = new Default_Form_positions();
		$positionsform->setAttrib('action',DOMAIN.'positions/add');
		$jobtitleidmodel = new Default_Model_Jobtitles();
		$jobtitleidmodeldata = $jobtitleidmodel->getJobTitleList();
		if(!empty($jobtitleidmodeldata))
		{
			foreach ($jobtitleidmodeldata as $jobtitleidres)
			{
				$positionsform->jobtitleid->addMultiOption($jobtitleidres['id'],utf8_encode($jobtitleidres['jobtitlename']));
			}
		}
		else
		{
			$msgarray['jobtitleid'] = 'Job titles are not configured yet.';
			$emptyFlag++;
		}
		$this->view->form = $positionsform;
		$this->view->emptyFlag=$emptyFlag;
		if($this->getRequest()->getPost())
		{
			$msgarray = $this->save($positionsform);			
		}
		$this->view->msgarray = $msgarray;	

	}

	public function viewAction()
	{
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();
		$objName = 'positions';
		$positionsform = new Default_Form_positions();
		$positionsform->removeElement("submit");
		$positionsmodel = new Default_Model_Positions();

		$elements = $positionsform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
					$element->setAttrib("disabled", "disabled");
				}
			}
		}
		try
		{
			if($id)
			{
				$data = $positionsmodel->getsinglePositionData($id);
				$jobtitleidmodel = new Default_Model_Jobtitles();
					
				//echo "<pre>";print_r($data);die;
				if(!empty($data) && $data != 'norows')
				{
					$positionsform->populate($data[0]);
					$jobtitleidmodeldata = $jobtitleidmodel->getsingleJobTitleData($data[0]['jobtitleid']);
					if($jobtitleidmodeldata !='norows')
					{
						foreach ($jobtitleidmodeldata as $jobtitleidres)
						{
							$positionsform->jobtitleid->addMultiOption($jobtitleidres['id'],utf8_encode($jobtitleidres['jobtitlename']));
						}
					}
					$positionsform->setDefault("jobtitleid",$data[0]["jobtitleid"]);
					$this->view->form = $positionsform;
					$this->view->controllername = $objName;
					$this->view->id = $id;
					$this->view->ermsg = '';
				}
				else
				{
					$this->view->ermsg = 'norecord';
				}
			}
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}
	}


	public function editAction()
	{
		$emptyFlag=0;
		$msgarray = array();
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		
	    $popConfigPermission = sapp_Global::_checkprivileges(JOBTITLES,$loginuserGroup,$loginuserRole,'add');
	    $this->view->popConfigPermission = $popConfigPermission;
	   
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		$positionsform = new Default_Form_positions();
		$positionsform->submit->setLabel('Update');
		$positionsmodel = new Default_Model_Positions();
		$objName = 'positions';
		try
		{
			if($id)
			{
				$deptModel = new Default_Model_Departments();
				$data = $positionsmodel->getsinglePositionData($id);
				$jobtitleidmodel = new Default_Model_Jobtitles();
				$jobtitleidmodeldata = $jobtitleidmodel->getJobTitleList();
				if(!empty($jobtitleidmodeldata))
				{
					foreach ($jobtitleidmodeldata as $jobtitleidres)
					{
						$positionsform->jobtitleid->addMultiOption($jobtitleidres['id'],utf8_encode($jobtitleidres['jobtitlename']));
					}
				}
				else
				{
					$msgarray['jobtitleid'] = 'Job titles are not configured yet.';
					$emptyFlag++;
				}
				if(!empty($data) && $data != 'norows')
				{
					$positionsform->populate($data[0]);
					$this->view->form = $positionsform;
					$this->view->controllername = $objName;
					$this->view->id = $id;
					$this->view->ermsg = '';
					$positionsform->setAttrib('action',DOMAIN.'positions/edit');
				}
				else
				{
					$this->view->ermsg = 'norecord';
				}
				$this->view->emptyFlag=$emptyFlag;
			}
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}
		if($this->getRequest()->getPost()){
			$result = $this->save($positionsform);
			//echo "<pre>";print_r($result);exit;
			$this->view->msgarray = $result;
		}
	}

	public function addpopupAction()
	{
		$emptyFlag = NULL;
		$msgarray = array();
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$jobtitleid = $this->_request->getParam('jobtitleid');

		$visible = true;
		$controllername = 'positions';
		$positionsform = new Default_Form_positions();
		$positionsmodel = new Default_Model_Positions();

		$jobtitlesmodel = new Default_Model_Jobtitles();

		if($jobtitleid == null){
			$jobtitleidmodeldata = $jobtitlesmodel->getJobTitleList();
			if(!empty($jobtitleidmodeldata))
			{
				foreach ($jobtitleidmodeldata as $jobtitleidres)
				{
					$positionsform->jobtitleid->addMultiOption($jobtitleidres['id'],utf8_encode($jobtitleidres['jobtitlename']));
					$this->view->notdisplayposition = 0;
				}
			}
			else
			{
				$msgarray['jobtitleid'] = 'Job titles are not configured yet.';
					
				$emptyFlag++;
			}
			$this->view->emptyFlag=$emptyFlag;
		}else{
			$jobtitleidres = $jobtitlesmodel->getsingleJobTitleData($jobtitleid);
			$positionsform->jobtitleid->addMultiOption($jobtitleidres[0]['id'],utf8_encode($jobtitleidres[0]['jobtitlename']));
			$positionsform->setDefault('jobtitleid',$jobtitleid);
			$this->view->notdisplayposition = $jobtitleid;
		}


		$positionsform->setAction(DOMAIN.'positions/addpopup');
		if($this->getRequest()->getPost()){
			if($positionsform->isValid($this->_request->getPost())){
				$id = $this->_request->getParam('id');

				$positionname = $this->_request->getParam('positionname');
				$jobtitleidpop = $this->_request->getParam('jobtitleid');
				$description = $this->_request->getParam('description');
				$displayposition = $this->_request->getParam('display');
				$date = new Zend_Date();
				$menumodel = new Default_Model_Menu();
				$actionflag = '';
				$tableid  = '';
				$data = array(
			                 'positionname'=>trim($positionname),
							 'jobtitleid'=>$jobtitleidpop,
			      			 'description'=>trim($description),
							  'modifiedby'=>$loginUserId,
							 'modifieddate'=>gmdate("Y-m-d H:i:s")
				);
				if($id!=''){
					$where = array('id=?'=>$id);
					$actionflag = 2;
				}
				else
				{
					$data['createdby'] = $loginUserId;
					//$data['createddate'] = $date->get('yyyy-MM-dd HH:mm:ss');
					$data['createddate'] = gmdate("Y-m-d H:i:s");
					$data['isactive'] = 1;
					$where = '';
					$actionflag = 1;
				}
				//echo "<pre>";print_r($data);exit;
				$Id = $positionsmodel->SaveorUpdatePositionData($data, $where);
				$tableid = $Id;
				$menuidArr = $menumodel->getMenuObjID('/positions');
				$menuID = $menuidArr[0]['id'];
				//echo "<pre>";print_r($menuidArr);exit;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);



				$opt ='';
				if($displayposition == '0'){
					$this->view->positionlistArr = $opt;
				}else{
					$positionlistArr = $positionsmodel->getPositionList($displayposition);
					foreach($positionlistArr as $record){
						$opt .= sapp_Global::selectOptionBuilder($record['id'], $record['positionname']);
					}					 
					$this->view->positionlistArr = $opt;
				}


				$this->view->eventact = 'added';
				$close = 'close';
				$this->view->popup=$close;
			}else
			{
				$messages = $positionsform->getMessages();
				foreach ($messages as $key => $val)
				{
					foreach($val as $key2 => $val2)
					{
						//echo $key." >> ".$val2;
						$msgarray[$key] = $val2;
						break;
					}
				}
				$this->view->msgarray = $msgarray;

			}
		}
		$this->view->controllername = $controllername;
		$this->view->form = $positionsform;
		$this->view->ermsg = '';
		$this->view->disableselect = $visible;


	}

	public function save($positionsform)
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		//$positionsform = new Default_Form_positions();
		$positionsmodel = new Default_Model_Positions();
		if($positionsform->isValid($this->_request->getPost())){
			$id = $this->_request->getParam('id');
			//$busineesunitid = $this->_request->getParam('busineesunitid');
			//$departmentid = $this->_request->getParam('departmentid');
			$positionname = $this->_request->getParam('positionname');
			$jobtitleid = $this->_request->getParam('jobtitleid');
			$description = $this->_request->getParam('description');
			$date = new Zend_Date();
			$menumodel = new Default_Model_Menu();
			$actionflag = '';
			$tableid  = '';
			$data = array( //'busineesunitid'=>$busineesunitid,
			//'departmentid'=>$departmentid,
				                 'positionname'=>trim($positionname),
								 'jobtitleid'=>$jobtitleid,
				      			 'description'=>trim($description),
								  'modifiedby'=>$loginUserId,
			// 'modifieddate'=>$date->get('yyyy-MM-dd HH:mm:ss')
								 'modifieddate'=>gmdate("Y-m-d H:i:s")

			);
			if($id!=''){
				$where = array('id=?'=>$id);
				$actionflag = 2;
			}
			else
			{
				$data['createdby'] = $loginUserId;
				//$data['createddate'] = $date->get('yyyy-MM-dd HH:mm:ss');
				$data['createddate'] = gmdate("Y-m-d H:i:s");
				$data['isactive'] = 1;
				$where = '';
				$actionflag = 1;
			}
			//echo "<pre>";print_r($data);exit;
			$Id = $positionsmodel->SaveorUpdatePositionData($data, $where);
			if($Id == 'update')
			{
				$tableid = $id;
				// $this->_helper->getHelper("FlashMessenger")->addMessage("Position updated successfully.");
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Position updated successfully."));
			}
			else
			{
				$tableid = $Id;
				// $this->_helper->getHelper("FlashMessenger")->addMessage("Position added successfully.");
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Position added successfully."));
			}
			$menuidArr = $menumodel->getMenuObjID('/positions');
			$menuID = $menuidArr[0]['id'];
			//echo "<pre>";print_r($menuidArr);exit;
			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
			//echo $result;exit;
			$this->_redirect('positions');
		}else
		{
			$messages = $positionsform->getMessages();
			foreach ($messages as $key => $val)
			{
				foreach($val as $key2 => $val2)
				{
					//echo $key." >> ".$val2;
					$msgarray[$key] = $val2;
					break;
				}
			}
			return $msgarray;
			//$this->view->msgarray = $msgarray;

		}

	}

	public function deleteAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->_request->getParam('objid');
		$messages['message'] = ''; $messages['msgtype'] = '';$messages['flagtype'] = '';
		$actionflag = 3;
		if($id)
		{
			$positionsmodel = new Default_Model_Positions();
			$menumodel = new Default_Model_Menu();
			$data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			$where = array('id=?'=>$id);
			$position_data = $positionsmodel->getsinglePositionData($id);
			//print_r($position_data);exit;
			$Id = $positionsmodel->SaveorUpdatePositionData($data, $where);
			if($Id == 'update')
			{
				sapp_Global::send_configuration_mail("Positions", $position_data[0]['positionname']);
				$menuidArr = $menumodel->getMenuObjID('/positions');
				$menuID = $menuidArr[0]['id'];
				//echo "<pre>";print_r($objid);exit;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
				$messages['message'] = 'Position deleted successfully.';
				$messages['msgtype'] = 'success';//$messages['flagtype'] = 'process';
			}
			else
			{	 $messages['message'] = 'Position cannot be deleted.';
			$messages['msgtype'] = 'error';//$messages['flagtype'] = 'process';
			}
		}
		else
		{
			$messages['message'] = 'Position cannot be deleted.';
			$messages['msgtype'] = 'error';//$messages['flagtype'] = 'process';
		}
		$this->_helper->json($messages);

	}



}

