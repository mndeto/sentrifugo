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

class Default_DisabilitydetailsController extends Zend_Controller_Action
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
		//echo "Here in controller";//die;
	}

	public function addAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('disabilitydetails',$empOrganizationTabs)){
		 	$msgarray = array();
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity())
		 	{
		 		$loginUserId = $auth->getStorage()->read()->id;
		 	}
		 	$empDisabilitydetailsform = new Default_Form_Disabilitydetails();

		 	$this->view->form = $empDisabilitydetailsform;
		 	$this->view->msgarray = $msgarray;

		 	if($this->getRequest()->getPost())
		 	{
		 		//check validation for other disability type field ...
		 		$description =$this->_request->getParam('disability_description');
		 		$disability_name=$this->_request->getParam('disability_name');
		 		$disability_type=$this->_request->getParam('disability_type');
		 		if($disability_type != "" && $disability_type == "other impairments")
		 		{
		 			$other_disability_type=$this->_request->getParam('other_disability_type');
		 			if($other_disability_type == "")
		 			{
		 				$msgarray['other_disability_type']= "Please enter disability type";
		 			}
		 		}
		 		if(empty($msgarray))
		 		{
		 			$result = $this->save($empDisabilitydetailsform);
		 			$this->view->form = $empDisabilitydetailsform;
		 			$this->view->msgarray = $result;
		 		}
		 		else
		 		{
		 			$this->view->msgarray = $msgarray;
		 		}
		 	}
		 }else{
		 	$this->_redirect('error');
		 }
		}else{
			$this->_redirect('error');
		}
	}

	public function editAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('disabilitydetails',$empOrganizationTabs)){
		 	$employeeData=array();
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity())
		 	{
		 		$loginUserId = $auth->getStorage()->read()->id;
		 	}
		 	$id = $this->getRequest()->getParam('userid');
		 	$employeeModal = new Default_Model_Employee();
		 	try
		 	{
			    if($id && is_numeric($id) && $id>0 && $id!=$loginUserId)
				{ 
					$isrowexist = $employeeModal->getsingleEmployeeData($id);
					if($isrowexist == 'norows')
					$this->view->rowexist = "norows";
					else
					$this->view->rowexist = "rows";

					$empdata = $employeeModal->getActiveEmployeeData($id);
					if(!empty($empdata))
					{
						$empDisabilitydetailsform = new Default_Form_Disabilitydetails();
						$empDisabilitydetailsModel = new Default_Model_Disabilitydetails();
						if($id)
						{	//TO dispaly EMployee Profile information.....
							$usersModel = new Default_Model_Users();
							$employeeData = $usersModel->getUserDetailsByIDandFlag($id);
							//echo "Employee Data : <pre>";print_r($employeeData);die;
							$data = $empDisabilitydetailsModel->getempDisabilitydetails($id);
							//echo "<pre>Edit data :: ";print_r($data);die;
							if(!empty($data))
							{
								$empDisabilitydetailsform->setDefault("id",$data[0]["id"]);
								$empDisabilitydetailsform->setDefault("disability_name",$data[0]["disability_name"]);
								$empDisabilitydetailsform->setDefault("disability_type",$data[0]["disability_type"]);
								$empDisabilitydetailsform->setDefault("disability_description",$data[0]["disability_description"]);
								$empDisabilitydetailsform->setDefault("other_disability_type",$data[0]["other_disability_type"]);

							}
							$empDisabilitydetailsform->setAttrib('action',DOMAIN.'disabilitydetails/edit/userid/'.$id);
							$this->view->id = $id;
							$this->view->employeedata = $employeeData[0];
						}
						$this->view->employeedata = $employeeData[0];
						$this->view->form = $empDisabilitydetailsform;
						if($this->getRequest()->getPost())
						{
							$result = $this->save($empDisabilitydetailsform);
							$this->view->msgarray = $result;
						}
					}
					$this->view->empdata = $empdata;
					$this->view->messages = $this->_helper->flashMessenger->getMessages();
				}
				else
				{
				  $this->view->rowexist = "norows";
				}
		 	}
		 	catch(Exception $e)
		 	{
		 		$this->view->rowexist = "norows";
		 	}
		 }else{
		 	$this->_redirect('error');
		 }
		}else{
			$this->_redirect('error');
		}
	}

	public function save($empDisabilitydetailsform)
	{
		$result ="";
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$date = new Zend_Date();
		if($empDisabilitydetailsform->isValid($this->_request->getPost()))
		{
			$empDisabilitydetailsModel = new Default_Model_Disabilitydetails();
			$id = $this->_request->getParam('id');
			$user_id = $this->getRequest()->getParam('userid');
			//Post values ..
			$disability_type = $this->_request->getParam('disability_type');
			$disabiity_name =$this->_request->getParam('disability_name');
			$description =$this->_request->getParam('disability_description');
			$other_disability_type=$this->_request->getParam('other_disability_type');

			$data = array(  'other_disability_type'=>$other_disability_type,
								'disability_type'=>$disability_type,
								'disability_name'=>$disabiity_name,
								'disability_description'=>$description,
								'user_id'=>$user_id,
								'modifiedby'=>$loginUserId,
			                    'modifieddate'=>gmdate("Y-m-d H:i:s")
			  				//'modifieddate'=>$date->get('yyyy-MM-dd HH:mm:ss')
			);

			//echo "<pre> Post vals ";print_r($data);die;
			if($id!='')
			{
				$where = array('user_id=?'=>$user_id);
				$actionflag = 2;
			}
			else
			{
				$data['createdby'] = $loginUserId;
				$data['createddate'] = gmdate("Y-m-d H:i:s");
				//$data['createddate'] = $date->get('yyyy-MM-dd HH:mm:ss');
				$where = '';
				$actionflag = 1;
			}
			$Id = $empDisabilitydetailsModel->SaveorUpdateEmpdisabilityDetails($data, $where);
			if($Id == 'update')
			{
				$tableid = $id;
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>" Employee disability details updated successfully."));
			}
			else
			{
				$tableid = $Id;
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>" Employee disability details added successfully."));
			}
			$menumodel = new Default_Model_Menu();
			$menuidArr = $menumodel->getMenuObjID('/employee');
			$menuID = $menuidArr[0]['id'];
			//echo "<pre>";print_r($menuidArr);exit;
			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);
			//echo $result;exit;
			$this->_redirect('disabilitydetails/edit/userid/'.$user_id);
		}
		else
		{
			$messages = $empDisabilitydetailsform->getMessages();

			foreach ($messages as $key => $val)
			{
				foreach($val as $key2 => $val2)
				{
					$msgarray[$key] = $val2;
					break;
				}
			}
			//echo "<br/>msgArr <pre>";print_r($msgarray);die;
			return $msgarray;
		}

	}
	public function viewAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('disabilitydetails',$empOrganizationTabs)){
		    $auth = Zend_Auth::getInstance();$emptyFlag=0;
				if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginUserGroup = $auth->getStorage()->read()->group_id;
					$loginUserRole = $auth->getStorage()->read()->emprole;
				} 
		 	$id = $this->getRequest()->getParam('userid');
		 	$employeeModal = new Default_Model_Employee();
		 	try
		 	{
			    if($id && is_numeric($id) && $id>0 && $id!=$loginUserId)
				{
					$isrowexist = $employeeModal->getsingleEmployeeData($id);
					if($isrowexist == 'norows')
					$this->view->rowexist = "norows";
					else
					$this->view->rowexist = "rows";

					$empdata = $employeeModal->getActiveEmployeeData($id);
					if(!empty($empdata))
					{
						$callval = $this->getRequest()->getParam('call');
						if($callval == 'ajaxcall')
						$this->_helper->layout->disableLayout();
						$objName = 'disabilitydetails';$employeeData=array();
						$empDisabilitydetailsform = new Default_Form_Disabilitydetails();
						$empDisabilitydetailsModel = new Default_Model_Disabilitydetails();
						$empDisabilitydetailsform->removeElement("submit");
						$elements = $empDisabilitydetailsform->getElements();
						if(count($elements)>0)
						{
							foreach($elements as $key=>$element)
							{
								if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
									$element->setAttrib("disabled", "disabled");
								}
							}
						}
						$data = $empDisabilitydetailsModel->getempDisabilitydetails($id);
						//echo "<pre>";print_r($data);exit;
						//TO dispaly EMployee Profile information.....
						$usersModel = new Default_Model_Users();
						$employeeData = $usersModel->getUserDetailsByIDandFlag($id);
						//echo "Employee Data : <pre>";print_r($employeeData);die;
						if(!empty($data))
						{
							$empDisabilitydetailsform->setDefault('user_id',$data[0]['user_id']);
							$empDisabilitydetailsform->setDefault('disability_name',$data[0]['disability_name']);
							$empDisabilitydetailsform->setDefault('disability_type',$data[0]['disability_type']);
							$empDisabilitydetailsform->setDefault('other_disability_type',$data[0]['other_disability_type']);
							$empDisabilitydetailsform->setDefault('disability_description',$data[0]['disability_description']);
						}
						$this->view->controllername = $objName;
						$this->view->id = $id;
						$this->view->data = $data;
						$this->view->employeedata = $employeeData[0];
						$this->view->form = $empDisabilitydetailsform;
		 		}
		 		$this->view->empdata = $empdata;
				}
				else
				{
				  $this->view->rowexist = "norows";
				}
		 	}
		 	catch(Exception $e)
		 	{
		 		$this->view->rowexist = "norows";
		 	}
		 }else{
		 	$this->_redirect('error');
		 }
		}
	}

}

