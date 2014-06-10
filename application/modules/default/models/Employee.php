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

class Default_Model_Employee extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_employees';
    protected $_primary = 'id';		

	/*
	   I. This query fetches employees data based on roles.
	   II. If roles are not configured then to eliminate users and other vendors we are using jobtitle clause.
	       As for jobtitle id for vendors and users will always be null.
	*/
    public function getEmployeesData($sort,$by,$pageNo,$perPage,$searchQuery,$managerid='',$loginUserId)
    {
        /*
        $employeesData="";
        $acceptedrolesArr = $this->getUserRole();
        $roles = $acceptedrolesArr[0]['roles'];
     
        $where = "  mu.isactive != 5 AND e.isactive != 5 AND ";
        if($roles != "")
            $where .= " mu.emprole IN(".$roles.") AND mu.userstatus = 'old' AND e.user_id != ".$loginUserId." ";
        else 
            $where .= " mu.userstatus = 'old' AND mu.jobtitle_id is not NULL AND e.user_id != ".$loginUserId." ";  
        if($managerid !='')
            $where .= " AND e.reporting_manager = ".$managerid." ";
        if($searchQuery != '')
            $where .= " AND ".$searchQuery;

        $employeesData = $this->select()
                                ->setIntegrityCheck(false)	                                
                                ->from(array('e' => 'main_employees'),array('id'=>'e.user_id','extn'=>'concat(e.office_number," (ext ",e.extension_number,")")'))
                                ->joinLeft(array('u'=>'main_users'),'e.reporting_manager=u.id',array('rm'=>'u.userfullname',))                                                           
                                ->joinInner(array('mu'=>'main_users'),'e.user_id=mu.id',
                                            array('employeeId'=>'mu.employeeId','userfullname'=>'mu.userfullname',
                                            'emailaddress'=>'mu.emailaddress','contactnumber'=>'mu.contactnumber',
                                            'astatus'=> new Zend_Db_Expr('case when mu.isactive = 0 then "Inactive" when mu.isactive = 1 then "Active" when mu.isactive = 2 then "Resigned"  when mu.isactive = 3 then "Left" when mu.isactive = 4 then "Suspended" end')))
                            ->joinInner(array('r'=>'main_roles'),'mu.emprole=r.id',array('rolename'=>'r.rolename'))
                            ->joinInner(array('j'=>'main_jobtitles'),'e.jobtitle_id=j.id',array('jobtitlename'=>'j.jobtitlename'))
                            ->joinLeft(array('t'=>'tbl_employmentstatus'),'e.emp_status_id=t.id',array('emp_status_id'=>'t.employemnt_status'))
                            ->where($where)
                            ->order("$by $sort") 
                            ->limitPage($pageNo, $perPage);
        */
        //the above code is used to get data of employees with joins
        //the below code is used to get data of employees from summary table.
        $employeesData="";                             
        $where = "  e.isactive != 5 AND e.user_id != ".$loginUserId." ";  
        
        if($managerid !='')
            $where .= " AND e.reporting_manager = ".$managerid." ";
        if($searchQuery != '')
            $where .= " AND ".$searchQuery;

        $employeesData = $this->select()
                                ->setIntegrityCheck(false)	                                
                                ->from(array('e' => 'main_employees_summary'),
                                        array('*','id'=>'e.user_id','extn'=>new Zend_Db_Expr('case when e.extension_number is not null then concat(e.office_number," (ext ",e.extension_number,")") when e.extension_number is null then e.office_number end'),'astatus'=> new Zend_Db_Expr('case when e.isactive = 0 then "Inactive" when e.isactive = 1 then "Active" when e.isactive = 2 then "Resigned"  when e.isactive = 3 then "Left" when e.isactive = 4 then "Suspended" end')
                                            ))                               
                                ->where($where)
                                ->order("$by $sort") 
                                ->limitPage($pageNo, $perPage);
        
        
                    //echo "<br/>".$employeesData."<br/> "; exit;
       
        return $employeesData;       		
    }
	
	public function getUserRole()
	{	
		$db = Zend_Db_Table::getDefaultAdapter();
		/*$where = ' isactive = 1 ';		
		$usersData = $db->query("SELECT id,userfullname FROM main_users WHERE emprole IN(2,3,4,6) AND userstatus = 'new' AND $where;");*/
		$usersData = $db->query("select GROUP_CONCAT(r.id) as roles from main_roles As r Inner join main_groups As g  on r.group_id=g.id 
        where r.isactive=1 AND g.id IN(".MANAGER_GROUP.",".HR_GROUP.",".EMPLOYEE_GROUP.",".SYSTEMADMIN_GROUP.",".MANAGEMENT_GROUP.")");
      
		$usersResult = $usersData->fetchAll();
		//echo "<pre>";print_r($usersResult);die;
		return $usersResult;
	}
	/**
         * This function gives full employee details based on user id.
         * @param integer $id  = id of employee
         * @return array  Array of employee details.
         */
	public function getsingleEmployeeData($id)
	{
		//$row = $this->fetchRow("user_id =".$id."");
            $db = Zend_Db_Table::getDefaultAdapter();
            $empData = $db->query("SELECT e.*,u.*,p.prefix,p.isactive as active_prefix FROM main_employees e 
                INNER JOIN main_users u ON e.user_id = u.id INNER JOIN main_prefix p ON e.prefix_id = p.id
                           WHERE e.user_id = ".$id."   AND  u.isactive IN (1,2,3,4,0) AND u.userstatus ='old'");
            $res = $empData->fetchAll();
            if (isset($res) && !empty($res)) 
            {	//throw new Exception("Could not find row $id");
                return $res;
            }
            else
                return 'norows';
	}
        /**
         * This function is used to get data in employees report.
         * @param array $param_arr   = array of parameters.
         * @param integer $per_page  = no.of records per page
         * @param integer $page_no   = page number
         * @param string $sort_name  = name of the column to be sort
         * @param string $sort_type  = descending or ascending
         * @return array  Array of all employees.
         */
        public function getdata_emp_report($param_arr,$per_page,$page_no,$sort_name,$sort_type)
        {
            $search_str = "isactive != 5 ";
            foreach($param_arr as $key => $value)
            {
                    if($value != '')
                    {
                            if($key == 'date_of_joining')
                            $search_str .= " and ".$key." = '".sapp_Global::change_date ($value,'database')."'";				
                            else
                            $search_str .= " and ".$key." = '".$value."'";
                    }
            }
            $offset = ($per_page*$page_no) - $per_page;
            $db = Zend_Db_Table::getDefaultAdapter();
            $limit_str = " limit ".$per_page." offset ".$offset;
            $count_query = "select count(*) cnt from main_employees_summary where ".$search_str;
            $count_result = $db->query($count_query);
            $count_row = $count_result->fetch();
            $count = $count_row['cnt'];
            $page_cnt = ceil($count/$per_page);
            
            $query = "select * from main_employees_summary where ".$search_str." order by ".$sort_name." ".$sort_type." ".$limit_str;
            $result = $db->query($query);
            $rows = $result->fetchAll();
            return array('rows' => $rows,'page_cnt' => $page_cnt);
        }
    /**
     * This function is used to get data for pop up in groups,roles and employees report
     * @param Integer $group_id    = id of the group
     * @param Integer $role_id     = id of the role
     * @param Integer $page_no     = page number
     * @param String $sort_name    = field name to be sort
     * @param String $sort_type    = sort type like asc,desc
     * @return Array Array of employees of given role and group
     */
    public function emprolesgrouppopup($group_id,$role_id,$page_no,$sort_name,$sort_type,$per_page)
    {
        $offset = ($per_page*$page_no) - $per_page;
        $db = Zend_Db_Table::getDefaultAdapter();
        $limit_str = " limit ".$per_page." offset ".$offset;
        if($group_id == USERS_GROUP)
        {
            if($role_id != '')
            {
                $role_str = " and emprole in (".$role_id.")";
            }
            else 
            {
                $role_str = " and emprole in (select id from main_roles where group_id = ".$group_id." and isactive = 1)";
            }
            $count_query = "select count(*) cnt from main_users where isactive = 1 ".$role_str;
            $count_result = $db->query($count_query);
            $count_row = $count_result->fetch();
            $count = $count_row['cnt'];
            $page_cnt = ceil($count/$per_page);
            $query = "select r.rolename rolename_p,u.userfullname,u.employeeId,u.emailaddress from main_users u,main_roles r where r.id = u.emprole and u.isactive = 1 ".$role_str." order by ".$sort_name." ".$sort_type." ".$limit_str;
            $result = $db->query($query);
            $rows = $result->fetchAll();
            return array('rows' => $rows,'page_cnt' => $page_cnt);
        }
        else 
        {
            if($role_id != '')
            {
                $role_str = " and emprole in (".$role_id.")";
            }
            else 
            {
                $role_str = " and emprole in (select id from main_roles where group_id = ".$group_id." and isactive = 1)";
            }
            $count_query = "select count(*) cnt from main_employees_summary where isactive = 1 ".$role_str;
            $count_result = $db->query($count_query);
            $count_row = $count_result->fetch();
            $count = $count_row['cnt'];
            $page_cnt = ceil($count/$per_page);
            $query = "select * from main_employees_summary where isactive = 1 ".$role_str." order by ".$sort_name." ".$sort_type." ".$limit_str;
            $result = $db->query($query);
            $rows = $result->fetchAll();
            return array('rows' => $rows,'page_cnt' => $page_cnt);
        }
    }
	public function SaveorUpdateEmployeeData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_employees');
			return $id;
		}
		
	}
	
	public function getActiveEmployeeData($id)
    {
    	$result =  $this->select()
    				->setIntegrityCheck(false) 	
    				->from(array('e'=>'main_employees'),array('e.*'))
 	  				->where("e.isactive = 1 AND e.user_id = ".$id);
		//echo "Result > ".$result ;die;			
    	return $this->fetchAll($result)->toArray();
    }
    public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$exParam1='',$exParam2='',$exParam3='',$exParam4='')
    {		
        $searchQuery = '';
        $tablecontent = '';
        $emptyroles=0;
        $empstatus_opt = array();
        $searchArray = array();
        $data = array();
        $id='';
        $dataTmp = array();
		
        if($searchData != '' && $searchData!='undefined')
        {
            $searchValues = json_decode($searchData);
			
            foreach($searchValues as $key => $val)
            {				
                /*if($key == "userfullname" ||$key == "emailaddress" ||$key == "contactnumber" || $key == "employeeId")
                    $searchQuery .= " mu.".$key." like '%".$val."%' AND ";
                else if($key == "rm")
                    $searchQuery .= " u.userfullname like '%".$val."%' AND ";
                else*/ 
                    if($key == 'astatus')
                    $searchQuery .= " e.isactive like '%".$val."%' AND ";
                else if($key == 'extn')					
                    $searchQuery .= " concat(e.office_number,' (ext ',e.extension_number,')') like '%".$val."%' AND ";
                else 
                    $searchQuery .= $key." like '%".$val."%' AND ";				
                $searchArray[$key] = $val;
            }
            $searchQuery = rtrim($searchQuery," AND");					
        }
        $objName = 'employee';
				        
			
        $tableFields = array('action'=>'Action','userfullname'=>'Name','emailaddress'=>'E-mail',
                             'employeeId' =>'Employee ID','astatus' =>'User Status','extn'=>'Work Phone',
                             'jobtitle_name'=>'Job Title','reporting_manager_name'=>'Reporting Manager','contactnumber'=>'Contact Number',
                             'emp_status_name' =>'Employment Status','emprole_name'=>"Role");
		   
        $tablecontent = $this->getEmployeesData($sort,$by,$pageNo,$perPage,$searchQuery,'',$exParam1);  
			
        if($tablecontent == "emptyroles")
        {
            $emptyroles=1;
        }
        else
        {	
            $employmentstatusModel = new Default_Model_Employmentstatus();
            $employmentStatusData = $employmentstatusModel->getempstatuslist();	
            //echo "<pre>";print_r($employmentStatusData);echo "</pre>";
            if(count($employmentStatusData) >0)
            {
                foreach($employmentStatusData as $empsdata)
                {
                    $empstatus_opt[$empsdata['workcodename']] = $empsdata['statusname'];
                }
            }
        }
		
        $dataTmp = array(
                        'userid'=>$id,
                        'sort' => $sort,
                        'by' => $by,
                        'pageNo' => $pageNo,
                        'perPage' => $perPage,				
                        'tablecontent' => $tablecontent,
                        'objectname' => $objName,
                        'extra' => array(),
                        'tableheader' => $tableFields,
                        'jsGridFnName' => 'getAjaxgridData',                        
                        'jsFillFnName' => '',
                        'searchArray' => $searchArray,
                        'menuName' => 'Employees',
                        'dashboardcall'=>$dashboardcall,
                        'add'=>'add',
                        'call'=>$call,
                        'search_filters' => array(
                                                'astatus' => array('type'=>'select',
                                                'filter_data'=>array(''=>'All',1 => 'Active',0 => 'Inactive')),
                                                'emp_status_id'=>array(
                                                                        'type'=>'select',
                                                                        'filter_data' => array(''=>'All')+$empstatus_opt),
                                                ),
                        'emptyroles'=>$emptyroles
                    );	
				
        return $dataTmp;
    }
	
    public function getAutoReportEmp($search_str)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select * from ((select u.id user_id,u.profileimg,concat(u.userfullname,', ',j.jobtitlename) emp_name,
                  case when u.userfullname like '".$search_str."%' then 4  when u.userfullname like '__".$search_str."%' then 2 
                  when u.userfullname like '_".$search_str."%' then 3 when u.userfullname like '%".$search_str."%' then 1 
                  else 0 end emp 
                  from main_users u,main_jobtitles j where 
                  j.id = u.jobtitle_id and u.isactive =1 and u.jobtitle_id is not null 
                  and (u.userfullname like '%".$search_str."%' or j.jobtitlename like '%".$search_str."%') 
                    )
                    union (select u.id user_id,u.profileimg,concat(u.userfullname,', Super Admin') emp_name ,
                    case when u.userfullname like '".$search_str."%' then 4 when u.userfullname like '__".$search_str."%' then 2 when u.userfullname like '_".$search_str."%' then 3 
                    when u.userfullname like '%".$search_str."%' then 1 else 0 end emp from main_users u where u.id = 1 and 
                    (u.userfullname like '%".$search_str."%' or 'Super Admin' like '%".$search_str."%') )
                    ) a
                  order by emp desc
                  limit 0,10";
        $result = $db->query($query);
        $emp_arr = array();
        $emp_arr = $result->fetchAll();
        return $emp_arr;
    }
	
	public function getEmployeesUnderRM($empid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select * from main_employees_summary where reporting_manager = ".$empid." and isactive = 1;";
		$result = $db->query($query);
        $emp_arr = array();
        $emp_arr = $result->fetchAll();
        return $emp_arr;
	}
	
public function getCurrentOrgHead()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select user_id from main_employees where is_orghead = 1 and isactive = 1";
		$result = $db->query($query);
        $emp_arr = array();
        $emp_arr = $result->fetchAll();
        return $emp_arr;
	}
	
	public function changeRM($oldRM,$newRM,$status,$ishead)
	{		
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$db = Zend_Db_Table::getDefaultAdapter();	
		$db->beginTransaction();
		$oldRMData = $this->getsingleEmployeeData($oldRM);
		try
		{				
			if($status == 'active')
			{
				$data = array(
					'isactive' => 1,
					'emptemplock' => 0,
					'modifieddate' => gmdate("Y-m-d H:i:s"),
					'modifiedby' => $loginUserId
				);
                            $Query1 = "UPDATE main_employees SET isactive = 1, modifieddate = '".gmdate("Y-m-d H:i:s")."', modifiedby = ".$loginUserId." WHERE user_id=".$oldRM." ;";				
                            $db->query($Query1);
			}
			else if($status == 'inactive')
			{
				$data = array(
					'isactive' => 0,
					'emptemplock' => 1,
					'modifieddate' => gmdate("Y-m-d H:i:s"),
					'modifiedby' => $loginUserId
				);
			}
			$where = "id = ".$oldRM;
			$user_model =new Default_Model_Usermanagement();
			$result = $user_model->SaveorUpdateUserData($data, $where);
			
			if($status == 'inactive')
			{
				$empQuery1 = "UPDATE main_employees SET reporting_manager = ".$newRM.", modifieddate = '".gmdate("Y-m-d H:i:s")."', modifiedby = ".$loginUserId." WHERE reporting_manager=".$oldRM." and isactive = 1 AND user_id <> ".$newRM.";";
				
				$empQuery2 = "UPDATE main_employees SET reporting_manager = ".$oldRMData[0]['reporting_manager'].", modifieddate = '".gmdate("Y-m-d H:i:s")."', modifiedby = ".$loginUserId." WHERE reporting_manager=".$oldRM." and isactive = 1 AND user_id = ".$newRM.";";
				
				
				if($ishead == '1')
				{
					$orgQuery1 = "UPDATE main_employees SET is_orghead = 0,isactive = 0, reporting_manager= ".$newRM.", modifieddate = '".gmdate("Y-m-d H:i:s")."', modifiedby = ".$loginUserId." WHERE user_id=".$oldRM." ;";				
					$db->query($orgQuery1);
					
					$orgQuery2 = "UPDATE main_employees SET is_orghead = 1,reporting_manager= 0, modifieddate = '".gmdate("Y-m-d H:i:s")."', modifiedby = ".$loginUserId." WHERE user_id=".$newRM." ;";				
					$db->query($orgQuery2);
				}
				$db->query($empQuery1);
				$db->query($empQuery2);
			}
			$db->commit();
			return 'success';
		}
		catch(Exception $e)
		{			
			return 'failed';
			$db->rollBack();
		}
	}
	
	public function getEmployeesForOrgHead($userid = '')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		if($userid == '')
		{
			$qry_str = " SELECT u.id, concat(u.userfullname,' , ',j.jobtitlename) as name,u.profileimg 
                                FROM main_users u 
                                INNER JOIN main_roles r ON u.emprole = r.id 
                                INNER JOIN main_employees e ON u.id = e.user_id 
                                inner join main_jobtitles j on j.id = e.jobtitle_id 
                                WHERE  r.group_id IN (".MANAGEMENT_GROUP.")  AND u.userstatus='old' AND u.isactive=1 AND r.isactive=1 order by name asc";
		}
		else
		{
			$qry_str = " SELECT u.id, concat(u.userfullname,' , ',j.jobtitlename) as name,u.profileimg 
                                FROM main_users u 
                                INNER JOIN main_roles r ON u.emprole = r.id 
                                INNER JOIN main_employees e ON u.id = e.user_id 
                                inner join main_jobtitles j on j.id = e.jobtitle_id 
                                WHERE  r.group_id IN (".MANAGEMENT_GROUP.")  AND u.userstatus='old' AND u.isactive=1 AND r.isactive=1 AND u.id <> ".$userid." order by name asc";
		}
		$reportingManagersData = $db->query($qry_str);
        $res = $reportingManagersData->fetchAll();
		return $res;
	}
}
?>