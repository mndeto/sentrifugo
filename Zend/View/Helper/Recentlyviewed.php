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

/**
* Recentlyviewed View Helper
*
* A View Helper that creates the menu
*
*
*/

class Zend_View_Helper_Recentlyviewed extends Zend_View_Helper_Abstract
{

    public function recentlyviewed()
    {
        $request = Zend_Controller_Front::getInstance();
        $params = $request->getRequest()->getParams();
        $moduleName = $request->getRequest()->getModuleName();
        $controllerName = $request->getRequest()->getControllerName();
        $actionName = $request->getRequest()->getActionName();
        $mparams['module'] = $params['module'];
        $mparams['controller'] = $params['controller'];
        $mparams['action'] = $params['action'];
        $actionurl = '';
        $id_name = 'yes';
        
        $burl = $controllerName."/".$actionName;
        
        if($actionName !='')
        {
        	$actionurl = strstr($_SERVER['REQUEST_URI'], $actionName);
        	$actionurl = str_replace($actionName, '', $actionurl);
        }
        else if($controllerName !='')
        {
        	$actionurl = strstr($_SERVER['REQUEST_URI'], $controllerName);
        	$actionurl = str_replace($actionName, '', $actionurl);
        }
        else
        {
        	$actionurl = strstr($_SERVER['REQUEST_URI'], $moduleName);
        	$actionurl = str_replace($actionName, '', $actionurl);
        }

        $burl = $burl.$actionurl;
        
       /* $id_params = array_diff($params,$mparams);
        $id_name = '';
        if(is_array($id_params) && !empty($id_params))
        {            
            foreach($id_params as $key => $value)
            {
                $burl .= "/".$key."/".$value;
            }
            $id_name = "yes";
        }*/
        $tmpPageLink = explode("/",$_SERVER['REQUEST_URI']);
        $pageName = $controllerName;
        $pageLink = $burl;
        //echo "<br/>".DOMAIN;
        
        
        
        $reportsArr = array('leavesreport'=>'-Leaves',
		                    'leavemanagementreport'=>'-Leave Management',
		                    'holidaygroupreports'=>'-Holidays',
                            'activeuser'=>'-Active Users',
							'employeereport'=>'-Employees',
							'rolesgroup'=>'-Roles',
							'emprolesgroup'=>'-Employee Roles',
							'userlogreport'=>'-User Logs',
                            'activitylogreport'=>'-Activity Logs',
							'requisitionstatusreport'=>'-Requisition',
							'candidatesreport'=>'-Candidates',
							'interviewrounds'=>'-Interview Rounds',
							'agencylistreport'=>'-Agency List',
							'empscreening'=>'-Employee Screening',
							'businessunits'=>'-Business Units',
							'departments'=>'-Departments'
		                    );
		$emptabarr = array(
                                'dependencydetails'=> 'Dependency Details',
                                'creditcarddetails'=> 'Corporate Card Details',
                                'visaandimmigrationdetails'=> 'Visa and Immigration Details',
                                'workeligibilitydetails'=> 'Work Eligibility Details',
                                'disabilitydetails'=> 'Disability Details',
                                'empcommunicationdetails'=> 'Communication Details',
                                'empskills'=> 'Employee Skills',
                                'empleaves'=> 'Leaves',
                                'empholidays'=> 'Holidays',
                                'medicalclaims'=> 'Medical Claims',
                                'educationdetails'=> 'Education Details',
                                'experiencedetails'=> 'Experience Details',
                                'trainingandcertificationdetails'=> 'Training & Certification Details',
                                'emppersonaldetails'=> 'Personal Details',
                                'empperformanceappraisal'=> 'Performance Appraisal',
                                'emppayslips'=> 'Pay Slips',
                                'empbenefits'=> 'Benefits',
                                'emprenumerationdetails'=> 'Remuneration Details',
                                'emprequisitiondetails'=> 'Requisition Details',
                                'empadditionaldetails'=> 'Additional Details',
                                'empsecuritycredentials'=> 'Security Credentials',
                                'empsalarydetails'=> 'Salary Details',
                                'empjobhistory'=> 'Job History',
                                'mydetails' => "",
                                "myemployees" => "My Team",
                                "userloginlog" => "User Log",
                                "logmanager" => "Activity Log",
                                "empconfiguration" => "Employee Tabs",
                                //"managemenus" => "Manage Modules",
                                
                            );	
           							
                $myemployees_arr = array(
                    'view'=> '-View',
                    'trainingview'=> '-Training & Certification Details',
                    'comview'=> '-Communication Details',
                    'skillsview'=> '-Employee Skills',
                    'eduview'=> '-Education Details',
                    'expview'=> '-Experience Details',
                    'perview'=> '-Personal Details',
                    'additionaldetailsview'=> '-Additional Details',
                    'jobhistoryview'=> '-Job History',
                                
                                
                );
               	$myDetailsEmployeesarr = array('mydetails','myemployees');																																																																																																																																																																	
                $mydetails_arr = array(
                    'communicationdetailsview'=> 'Communication Details-View',
                    'communication'=> 'Communication Details-Edit',
                    'disabilitydetailsview'=> 'Disability Details-View',
                    'disability'=> 'Disability Details-Edit',
                    'workeligibilitydetailsview'=> 'Work Eligibility Details-View',                    
                    'workeligibility'=> 'Work Eligibility Details-Edit',                    
                    'visadetailsview'=> 'Visa and Immigration Details-View',                    
                    'visa'=> 'Visa and Immigration Details-Edit',                    
                    'creditcarddetailsview'=> 'Corporate Card Details-View',
                    'creditcard' => "Corporate Card Details-Edit",
                    "additionaldetails" => "Additional Details-Edit",
                    "additionaldetailsview" => "Additional Details-View",
                    "salarydetails" => "Salary Details-Edit",
                    "salarydetailsview" => "Salary Details-View",
                    "personaldetailsview" => "Personal Details-View",
                    "personal" => "Personal Details-Edit",
                    "jobhistory" => "Job History",
                    "certification" => "Training & Certification Details",
                    "experience" => "Experience Details",
                    "education" => "Education Details",
                    "medicalclaims" => "Medical Claims",
                    "leaves" => "Leaves",
                    "skills" => "Employee Skills",
                    "dependency" => "Dependency Details",
                    "index" => "Employee Details-View",
                    "edit" => "Employee Details-Edit",                                                    
                );
		    	
		//The Logic used behind this functionality is we are using the object of zend session to store the action
		$recentlyViewed = new Zend_Session_Namespace('recentlyViewed'); // Creating a new session with namespace
		if(!empty($recentlyViewed->recentlyViewedObject))
		{
                    echo '<div class="recentviewd"><label id="recentviewtext">Recently viewed</label><ul>';
                    $rvSize = 0;
                    if(sizeof($recentlyViewed->recentlyViewedObject) > 3)
                    {
                        $rvSize = 3;
                        $recentlyViewed->recentlyViewedObject = array_slice($recentlyViewed->recentlyViewedObject,1);
                    }
                    else
                    {
                        $rvSize = sizeof($recentlyViewed->recentlyViewedObject);
                    }

			//echo "before<pre>";print_r($recentlyViewed->recentlyViewedObject);echo "</pre>";//exit;
                    $menuName = '';	$pagesplitName='';
                    for($i=0;$i<$rvSize;$i++)
                    {
                        $pagesplit = $recentlyViewed->recentlyViewedObject[$i];
                        $pagesplitName = isset($pagesplit['controller_name'])?$pagesplit['controller_name']:"";
                        $pagesplitLink = isset($pagesplit['url'])?$pagesplit['url']:"";
                        $pagesplit_action = isset($pagesplit['action_name'])?$pagesplit['action_name']:"";
                        $pagesplit_idname = isset($pagesplit['id_name'])?$pagesplit['id_name']:"";

                        // Instead of url - display menu name for each list item
                        if($pagesplitName != 'dashboard' && $pagesplitName != 'welcome' && $pagesplitName != 'viewsettings')
                        {					
                            if(array_key_exists($pagesplitName,$emptabarr) !== false)
                            {
                                $menuName = $emptabarr[$pagesplitName];
                            }																			
                            else
                            {
                                $selectQuery1 = "select m.menuName from main_menu m where m.url = '/".$pagesplitName."'";
                                $db = Zend_Db_Table::getDefaultAdapter();
                                $sql=$db->query($selectQuery1);
                                $resultarray = $sql->fetchAll();
        
                                if(!empty($resultarray))
                                    $menuName = ucfirst($resultarray[0]['menuName']);
                                else
                                    $menuName = ucfirst($pagesplitName);
                            }
                        }
                        else
                        {
                            if($pagesplitName == 'viewsettings')
                            {
                                $flagnumber = substr($pagesplitLink, -1);
                                if($flagnumber !='')
                                {
                                    if($flagnumber == 1)
                                        $menuName = "Settings-Widgets";
                                    else if($flagnumber == 2)
                                        $menuName = "Settings-Shortcuts";
                                }
                                else
                                    $menuName = "Settings";
                            }
                            else
                                $menuName = ucfirst($pagesplitName);
                        }
					
				// Display of add, edit or view in each list item                                                                                                       				
				// Checking condition for my employee and my details static controllers
                        if($pagesplitName !='' && in_array($pagesplitName,$myDetailsEmployeesarr))
                        {
                            if($pagesplit_action != '')
                            {
                                        //$menuName .= '-'.ucfirst($urldata[$flag]);
                                if($pagesplitName == 'myemployees')
                                {
                                    if(array_key_exists($pagesplit_action, $myemployees_arr) !== false)
                                        $menuName .= $myemployees_arr[$pagesplit_action];															
                                }
                                else
                                {
                                    if(array_key_exists($pagesplit_action, $mydetails_arr) !== false)
                                        $menuName .= $mydetails_arr[$pagesplit_action];
                                }
                            }
                            else if($pagesplit_action == '')
                            {
                                if($pagesplitName == 'mydetails')
                                    $menuName .= "Employee Details-View";
                            }					
                            else
                            {
                                $menuName .= '';
                            }
                        }
				// For Reports Module checking with global array and printing with key value of that array
                        else if($pagesplitName != '' && $pagesplitName == 'reports')
                        {
                            if($pagesplit_action != '')
                            {
                                if(array_key_exists($pagesplit_action,$reportsArr) !== false)
                                    $menuName .=$reportsArr[$pagesplit_action]; 
                            }		    		  
                        }
                        else
                        {				    
                            if($pagesplit_action != '' && $pagesplitName !='reports')
                            {
                                if($pagesplit_action == 'add')
                                    $menuName .= '-Add';
                                else if($pagesplit_action == 'edit' && $pagesplit_idname == 'yes')
                                    $menuName .= '-Edit';
                                else if($pagesplit_action == 'edit')
                                    $menuName .= '-Add';
                                else if($pagesplit_action == 'view')
                                    $menuName .= '-View';
                                else if($pagesplit_action == 'viewsettings')
                                    $menuName = 'Settings';
                                else if($pagesplit_action == 'viewprofile')
                                    $menuName = 'Profile';
                                else if($pagesplit_action == 'changepassword')
                                    $menuName = 'Change password';
                                else if($pagesplit_action == 'emailsettings')
                                    $menuName = 'Email Settings';
                            }
					
                        }
                        if($menuName)
                        {
                            echo '<li><span id="redirectlink"  title = "'.$menuName.'" onclick ="redirecttolink(\''.$pagesplitLink.'\');">'.$menuName.'</span><a href="javascript:void(0);" onClick="closetab(this,\''.$pagesplitName.'\',\''.$pagesplitLink.'\')"></a></li>';
                        }
                    }
			//echo "MENUNAME".$menuName;
		}//end of display
                                                                
        if(isset($recentlyViewed->recentlyViewedObject))
        {                
            if(sizeof($recentlyViewed->recentlyViewedObject) > 3 && $pageLink != DOMAIN && !in_array($pageName."!@#".$pageLink, $recentlyViewed->recentlyViewedObject))
            {
                array_shift($recentlyViewed->recentlyViewedObject);
            }
            if($pageName != 'public' && $pageName != 'welcome' && $controllerName !='error' )
            {
                if(!in_array('PIE.htc', $tmpPageLink))
                {                        
                    if($pageLink != DOMAIN && $controllerName !='index' && $actionName != 'welcome')
                    {                        
                        if($this->recentlyviewed_helper($pageLink, $recentlyViewed->recentlyViewedObject) === true)
                            array_push($recentlyViewed->recentlyViewedObject,array('url' => $burl,'controller_name' => $controllerName,'action_name' => $actionName,'id_name' => $id_name));
                    }
                }
            }
        }
        else
        {
            $recentlyViewed->recentlyViewedObject = array();                                                
            if($pageLink != DOMAIN && $controllerName !='index' && $actionName != 'welcome'  && $controllerName !='error' && !in_array('PIE.htc', $tmpPageLink))    
            {
                if($this->recentlyviewed_helper($pageLink, $recentlyViewed->recentlyViewedObject) === true)
                    array_push($recentlyViewed->recentlyViewedObject,array('url' => $burl,'controller_name' => $controllerName,'action_name' => $actionName,'id_name' => $id_name));
            }
        }
        //echo "AFTER<pre>";print_r($recentlyViewed->recentlyViewedObject);echo "</pre>";
        
        echo '</ul></div>';
    }//end of recently view function
    
    public function recentlyviewed_helper($url,$recently_arr)
    {
        if(!empty($recently_arr) && $url != '')
        {
            $k = 0;
            foreach($recently_arr as $rarr)
            {
                if($rarr['url'] == $url)
                    $k++;
            }
            if($k > 0)
                return false;
        }
        return true;
    }
}//end of class
