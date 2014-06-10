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
 * RapidHosts.com Zend Grid View Helper
 *
 * A View Helper that allows you to easily create Grids with Pagination
 *
 * @uses Zend_View_Helper_Abstract
 * @subpackage Grid
 * @copyright Copyright (c) 2010 Eric Haskins <admin@rapidhostsllc.com>
 *
 */

class Zend_View_Helper_Grid extends Zend_View_Helper_Abstract {

	public $view = null;

	public $extra = array();

	private $output; // Container to hold the Grid

	public function setView(Zend_View_Interface $view) {

		$this->view = $view;

		return $this;

	}

	public function grid ($dataArray)
	{
		$view = Zend_Layout::getMvcInstance()->getView();	
		$menu_model = new Default_Model_Menu();
		$session=new Zend_Auth_Storage_Session();
		$data=$session->read();
		$role_id = $data['emprole'];
		$menunamestr = '';$sortStr ='';
		$sortStr = $dataArray['by'];
		$controllers_arr = $menu_model->getControllersByRole($role_id);
		//echo "<pre>";print_r($controllers_arr);echo "</pre>";die;
		//echo "asdasdasdasd >> ".$controllers_arr[$dataArray['objectname']."controller.php"];die;
		if($dataArray['objectname'] == 'processes') $actionsobjname = 'empscreening';
		else $actionsobjname = $dataArray['objectname'];
		if(isset($controllers_arr[$actionsobjname."controller.php"]))
		{
			$actions_arr = $controllers_arr[$actionsobjname."controller.php"]['actions'];
			//echo "Actions Arr<pre>";print_r($actions_arr);echo "</pre>";die;
			$menuName = $actions_arr[sizeof($actions_arr)-1];
			
		}
		else
			$actions_arr = array();
			
			
		           
		if(isset($dataArray['menuName']))
			$menuName = $dataArray['menuName'];	
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($dataArray['tablecontent']));                
		$paginator->setItemCountPerPage($dataArray['perPage'])
		->setCurrentPageNumber($dataArray['pageNo']); 
		if(empty($dataArray['tableheader']))
		{
			$widgetsModel = new Default_Model_Widgets();
			$columnData = $widgetsModel->getTableFields('/'.$dataArray['objectname']);
			$dataArray['tableheader'] = json_decode($columnData['menufields'],true);				
		}
		$msgtitle = $dataArray['objectname'].'_delete';
		$msgtitle = strtoupper($msgtitle);
		$msgflag = constant($msgtitle);
		$msgAr = explode(' ',$msgflag);
		$msgdta = implode('@#$',$msgAr);
		if(isset($dataArray['formgrid']) && $dataArray['formgrid'] == 'true') 
		{			
			$urlString = $_SERVER['REQUEST_URI'];
			$urlData = explode('/',$urlString);$con ='';
			/*if(sizeof($urlData) > 5)
			$con = '/unitId/'.$urlData[5];			*/
			/*if(sizeof($urlData) > 4)
			$con = '/unitId/'.$urlData[4];	*/
			$domainName = trim(DOMAIN,'/');
			if(!in_array($domainName,$urlData))
			{
				if(sizeof($urlData) > 12 && $urlData[11] == 'unitId')
				{	$con = '/unitId/'.$urlData[12];		}
				else if(sizeof($urlData) > 16 && $urlData[15] == 'unitId')
				{	$con = '/unitId/'.$urlData[16];		 }
				else if(sizeof($urlData) > 4 && $urlData[4] != 'html')
				{	$con = '/unitId/'.$urlData[4];				}
				else
				{	$con = '/unitId/'.$dataArray['unitId'];	}
			}
			else
			{
				if(sizeof($urlData) > 13 && $urlData[12] == 'unitId')
				{	$con = '/unitId/'.$urlData[13];		}
				else if(sizeof($urlData) > 17 && $urlData[16] == 'unitId')
				{	$con = '/unitId/'.$urlData[17];		}
				else if(sizeof($urlData) > 5 && $urlData[5] != 'html')
				{	$con = '/unitId/'.$urlData[5];		}
				else
				{		$con = '/unitId/'.$dataArray['unitId']; }
			}
			
			$formgridVal = $dataArray['formgrid'];
			
			if($dataArray['objectname'] == 'departments'){
			  $viewaction = 'view';			
			}else			{
			  $viewaction = 'viewpopup';
			}			
			$editaction = 'editpopup';			
			if(isset($dataArray['menuName']) && $dataArray['menuName'] !='')
			  $menunamestr = $dataArray['menuName'];
			
			$viewpopup_str = '<a onclick="displaydeptform(\''.DOMAIN.$dataArray['objectname'].'/'.$viewaction.'/id/{{id}}'.$con.'/popup/1\',\''.$menunamestr.'\')" name="{{id}}" class="sprite view"  title=\'View\'></a>';
			$editpopup_str = '<a id="edit{{id}}" onclick="displaydeptform(\''.DOMAIN.$dataArray['objectname'].'/'.$editaction.'/id/{{id}}'.$con.'/popup/1\',\''.$menunamestr.'\')" name="{{id}}" class="sprite edit"  title=\'Edit\' ></a>';
			$deletepopup_str = '<a name="{{id}}" id="del{{id}}" onclick= changestatus(\''.$dataArray['objectname'].'\',\'{{id}}\',\''.$msgdta.'\')	href= javascript:void(0) title=\'Delete\' class="sprite delete" ></a>';
			
			
				if(!in_array('view',$actions_arr) && !in_array('edit',$actions_arr) && !in_array('delete',$actions_arr))
				{
				  if($dataArray['objectname'] == 'processes')
				  {	
                                      
					 $extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align">
								<a onclick="displaydeptform(\''.DOMAIN.$dataArray['objectname'].'/'.$viewaction.'/id/{{id}}'.$con.'/popup/1\',\''.$menunamestr.'\')" name="{{id}}" class="sprite view"  title=\'View\'></a>
								<a onclick="displaydeptform(\''.DOMAIN.$dataArray['objectname'].'/'.$editaction.'/id/{{id}}'.$con.'/popup/1\',\''.$menunamestr.'\')" name="{{id}}" class="sprite edit"  title=\'Edit\' ></a>
								<a name="{{id}}" id="{{id}}" onclick= changestatus(\''.$dataArray['objectname'].'\',\'{{id}}\',\''.$msgdta.'\')	href= javascript:void(0) title=\'Delete\' class="sprite delete" ></a>
							</div>'); 
				  }
				  else 
					$extra['action'] =array(); 				  
				}else
				{
				
					if($dataArray['objectname'] ==  'empleavesummary' || $dataArray['objectname'] ==  'empscreening')
					{
						$view_str = '<a href= "'.DOMAIN.$dataArray['objectname'].'/view/id/{{id}}" name="{{id}}" class="sprite view"  title=\'View\'></a>'; 
                        $edit_str = '<a href= "'.DOMAIN.$dataArray['objectname'].'/edit/id/{{id}}" name="{{id}}" class="sprite edit"  title=\'Edit\'></a>';
                        $delete_str = '<a name="{{id}}" onclick= changestatus(\''.$dataArray['objectname'].'\',\'{{id}}\',\''.$msgdta.'\')	href= javascript:void(0) title=\'Delete\' class="sprite delete" ></a>';
						$extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align">
										'.((in_array('view',$actions_arr)?$view_str:'')).'
										'.((in_array('edit',$actions_arr)?$edit_str:'')).'
										'.((in_array('delete',$actions_arr)?$delete_str:'')).'
									</div>');
					}
					else{
					  $extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align">
									'.((in_array('view',$actions_arr)?$viewpopup_str:'')).'
									'.((in_array('edit',$actions_arr)?$editpopup_str:'')).'
									'.((in_array('delete',$actions_arr)?$deletepopup_str:'')).'
								</div>'); //onclick ="javascript:editlocdata(\'{{id}}\')" 
					}
				}
			
			/*$extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align">
									<a onclick="displaydeptform(\''.DOMAIN.$dataArray['objectname'].'/'.$viewaction.'/id/{{id}}'.$con.'/popup/1\')" name="{{id}}" class="sprite view"  title=\'View\'></a>
									<a onclick="displaydeptform(\''.DOMAIN.$dataArray['objectname'].'/'.$editaction.'/id/{{id}}'.$con.'/popup/1\')" name="{{id}}" class="sprite edit"  title=\'Edit\' ></a>
									<a name="{{id}}" onclick= changestatus(\''.$dataArray['objectname'].'\',\'{{id}}\',\''.$msgdta.'\')	href= javascript:void(0) title=\'Delete\' class="sprite delete" ></a>
								</div>'); */			
		}
		else
		{			
			$formgridVal = '';
			            $view_str = '<a href= "'.DOMAIN.$dataArray['objectname'].'/view/id/{{id}}" name="{{id}}" class="sprite view"  title=\'View\'></a>'; 
                        $edit_str = '<a href= "'.DOMAIN.$dataArray['objectname'].'/edit/id/{{id}}" name="{{id}}" class="sprite edit"  title=\'Edit\'></a>';
                        $delete_str = '<a name="{{id}}" onclick= changestatus(\''.$dataArray['objectname'].'\',\'{{id}}\',\''.$msgdta.'\')	href= javascript:void(0) title=\'Delete\' class="sprite delete" ></a>';
			/*$extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align">
									<a href= "'.DOMAIN.$dataArray['objectname'].'/view/id/{{id}}" name="{{id}}" class="sprite view" ></a>
									'.(($role_id != 1)?(in_array('edit',$actions_arr)?$edit_str:''):$edit_str).'
									'.(($role_id != 1)?(in_array('delete',$actions_arr)?$delete_str:''):$delete_str).'
								</div>'); //onclick ="javascript:editlocdata(\'{{id}}\')"
                        */
						if(!in_array('view',$actions_arr) && !in_array('edit',$actions_arr) && !in_array('delete',$actions_arr))
						{
						  $extra['action'] =array(); 
						}else
						{
						  $extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align">
										'.((in_array('view',$actions_arr)?$view_str:'')).'
										'.((in_array('edit',$actions_arr)?$edit_str:'')).'
										'.((in_array('delete',$actions_arr)?$delete_str:'')).'
									</div>'); //onclick ="javascript:editlocdata(\'{{id}}\')" 
						}		
		}
		$extra['options'] = array(); 
        $addaction= '';  		
		if(isset($dataArray['add']) && $dataArray['add'] !='')
		{
		  $addaction = $dataArray['add'];
		}
		else
		{
		  $addaction = '';
		}
		$unitId = '';
		
		if(in_array('add',$actions_arr))
		{
		  $addpermission = "true";
		}
		else
		{
		 $addpermission = "false";
		}
		if(isset($dataArray['unitId'])) $unitId = $dataArray['unitId'];
		return $this->generateGrid($dataArray['objectname'],$dataArray['tableheader'],$paginator,$extra,true,$dataArray['jsGridFnName'], $dataArray['perPage'],$dataArray['pageNo'],$dataArray['jsFillFnName'],$dataArray['searchArray'],$formgridVal,$addaction,$menuName,$unitId,$addpermission,$menunamestr,isset($dataArray['call'])?$dataArray['call']:"",$sortStr,isset($dataArray['search_filters'])?$dataArray['search_filters']:"");
		
	}
	
	/**
	 *
	 * @param string $name
	 * @param array $fields
	 * @param Zend_Paginator Instance $paginator
	 * @param array $extracolumn
	 * @param Bool  $sorting
	 *
	 * @return string
	 */

	public function generateGrid ($name, $fields = null,$paginator=null,$extracolumn=array(),$sorting=false,$jsGridFnname='', $perPage='5',$page='1', $jsFillFnName='',$searchArray='',$formgrid='false',$addaction='',$menuName='',$unitId,$addpermission,$menunamestr,$call='',$sortStr='',$search_filters = '') {
         
		// Store Extra Columns
		$this->extra = $extracolumn;$sortIconStr = "";

		$sort = Zend_Controller_Front::getInstance()->getRequest()->getParam('sort','DESC');
		// checking and handling sorting.
		if($sort == "")
		{
			$sortIconStr = "<span class='s-ico'>
			<span class='ui-icon-desc ui-state-disabled ui-icon ui-icon-triangle-1-n'></span>
			<span class='ui-icon-asc ui-state-disabled ui-icon ui-icon-triangle-1-s'></span></span>";
		}
		else if ($sort  ==  'ASC')
		{
			$sort = 'DESC';
			//For Sort Icons....
			$sortIconStr = "<span class='s-ico'>
			<span class='ui-icon-desc ui-icon ui-icon-triangle-1-n'></span>
			</span>";
		}  
		else 
		{
			$sort = 'ASC';
			//For Sort Icons....
			$sortIconStr = "<span class='s-ico'>
			<span class='ui-icon-asc  ui-icon ui-icon-triangle-1-s'></span></span>";
		}
			
		if($call != "ajaxcall")		$sortIconStr = "";
		
		/*if ($sort  ==  'ASC') {
			$sort = 'DESC';
		}  else {
			$sort = 'ASC';
		}*/
		
		if($addaction !='')
		{
		  $action = $addaction;
		  $popupaction = 'addpopup';
		} else
		{
		  $action = "edit";
		  $popupaction = 'editpopup';
		}
		//$output = '<script language="JavaScript" type="text/javascript" src="'.MEDIA_PATH.'jquery/js/slimScrollHorizontal.js"></script>';		
		$con ='';
		if($formgrid != '')
		{
			$urlString = $_SERVER['REQUEST_URI'];
			$urlData = explode('/',$urlString);
			if($unitId != '')
			$con = 'unitId/'.$unitId;
			else if(sizeof($urlData) > 4)
			{
				/*if($urlData[4] != 'html')
				$con = 'unitId/'.$urlData[4];*/
				$domainName = trim(DOMAIN,'/');
				if(!in_array($domainName,$urlData))
				{
					if(sizeof($urlData) > 12 && $urlData[11] == 'unitId')
					{	$con = 'unitId/'.$urlData[11];		}
					else if(sizeof($urlData) > 16 && $urlData[15] == 'unitId')
						$con = 'unitId/'.$urlData[16];
					else if(sizeof($urlData) > 4 && $urlData[4] != 'html')
						$con = 'unitId/'.$urlData[4];
					else
						$con = 'unitId/'.$unitId;;	
				}
				else
				{
					if(sizeof($urlData) > 13 && $urlData[12] == 'unitId')
					{	$con = 'unitId/'.$urlData[13];		}
					else if(sizeof($urlData) > 17 && $urlData[16] == 'unitId')
						$con = 'unitId/'.$urlData[17];
					else if(sizeof($urlData) > 5 && $urlData[5] != 'html')
						$con = 'unitId/'.$urlData[5];
					else
							$con = 'unitId/'.$unitId;;	
				}
			}
			/*else if(sizeof($urlData) > 5)
			{
				if($urlData[5] != 'html')
				$con = 'unitId/'.$urlData[5];					
			}*/
			if($name == 'empscreening')
			{		
				$empaction = 'add';
				$output ="<div class='table-header'><span>".$menuName."</span><input type='button' onclick='window.location.href=\"".DOMAIN.$name.'/'.$empaction."\"' value='Add Record' class='sprite addrecord' /></div>";
			}
			else
			$output ="<div class='table-header'><span>".$menuName."</span><input type='button'   onclick='displaydeptform(\"".DOMAIN.$name.'/'.$popupaction."/$con/popup/1\",\"".$menunamestr."\")' value='Add Record' class='sprite addrecord' /></div>";
		}
		else
		{
		  	$output ="<div class='table-header'><span>".$menuName."</span><input type='button' onclick='window.location.href=\"".DOMAIN.$name.'/'.$action."\"' value='Add Record' class='sprite addrecord' /></div>";
		} 
		
		if($addpermission == 'false')
		{
		  /*if($name == 'processes')
		  $output ="<div class='table-header'><span>".$menuName."</span><input type='button'   onclick='displaydeptform(\"".DOMAIN.$name.'/'.$popupaction."/$con/popup/1\")' value='Add Record' class='sprite addrecord' /></div>";
		  else */
		  $output ="<div class='table-header'><span>".$menuName."</span></div>";
		}
		$output .="<div id='".$name."' class='details_data_display_block newtablegrid'>";
		$output .= "<table class='grid' align='center'  width='100%' cellspacing='0' cellpadding='4' border='0'><thead><tr>";
		// this foreach loop display the column header  in �th� tag.
		$colinr = 0;
		if(!empty($fields)) 
		{
			$tabindx = 0;
			foreach ($fields as $key => $value) {
				//echo"<pre>";print_r($value);
				if(isset($value['align'])) $align = (@$value['align'] != '')? 'align="'.$value['align'].'" ':'';
				if(isset($value['sortkey']))$sortkey = (@$value['sortkey'] != '')? 'align="'.$value['sortkey'].'" ':'';
				
				if(isset($value['style']))$style = (@$value['style'] != '')? 'style="'.$value['style'].'" ':'';
				
				$value = (is_array($value) && !isset($value['sortkey']))? $value['value']:$value;	
				if($value == 'Action') $width = 'width=90'; else $width =  '';//'width='.$eachColumnWidth;
				$output .= "<th ".$width.">";
				// Check if Sorting is set to True
				if($sorting) {

					// Disable Sorting if Key is in Extra Columns
					if(@$this->extra[$key]['name'] != '' && !is_array($value)) {
						if($value == "Action")	
							$output .= "<span class='action-text'>Action</span>";
						else
							$output .= $value;
						
					} else {
						if(is_array($value)){
							$key = $value['sortkey'];
							$value = $value['value'];
						} 
						$welcome = 'false';
						$urlString = $_SERVER['REQUEST_URI'];
						if (strpos($urlString,'welcome') !== false) {
							$welcome = 'true';
						}
					
						if($formgrid=='true')
						{
							$output .= "<a href='javascript:void(0);' onclick=javascript:paginationndsorting('".DOMAIN.$name."/index/sort/".$sort."/by/".$key."/objname/".$name."/page/".$page."/per_page/".$perPage."/call/ajaxcall/$con/');>".$value."</a>";
							//For Sort Icons....
								if($key == $sortStr)
									$output .= $sortIconStr;
						}
						else if($welcome == 'true')
						{	
							$output .= "<a href='javascript:void(0);' onclick=javascript:paginationndsorting('".DOMAIN.$name."/index/sort/".$sort."/by/".$key."/objname/".$name."/page/".$page."/per_page/".$perPage."/call/ajaxcall/$con/');>".$value."</a>";
							//For Sort Icons....
								if($key == $sortStr)
									$output .= $sortIconStr;
						}
						else 
						{
							$output .= "<a href='javascript:void(0);' onclick=javascript:paginationndsorting('".$this->view->url(array('sort'=>$sort,'by'=>$key,'objname'=> $name,'page' => $page,'per_page'=>$perPage))."');>".$value."</a>";
							//For Sort Icons....
							if($key == $sortStr)
									$output .= $sortIconStr;
						}
						if($key != 'id')
						{
							$sText = '';
							//$output .= "<input type='text' class='searchtxtbox' value='' onkeyup=javascript:paginationndsorting('".$this->view->url(array('sort'=>$sort,'by'=>$key,'')). style='display:none;' />";
							if(!empty($searchArray)) $display = 'display: block;'; else $display = 'display: none;';
							if(is_array($searchArray)) { if(array_key_exists($key,$searchArray)) $sText = $searchArray[$key]; else $sText = ''; }
							//$output .= "<input type='text' name='searchbox' id='$key' style='$display' class='searchtxtbox' value='$sText' onkeyup='getsearchdata(\"$key\",this.value,\"$name\")' />";
                                                        if(isset($search_filters[$key]))
                                                        {
                                                            
                                                           $output .= sapp_Global::grid_data($search_filters,$key,$name,$display,$sText,$tabindx);
                                                        }
                                                        else
                                                            $output .= "<input tabIndex=$tabindx type='text' name='$name' id='$key' style='$display' class='searchtxtbox_$name table_inputs' value='$sText' onkeyup='getsearchdata(\"$name\",\"\",this.id)' />";
						}
					}
				}  else {
					//For Sort Icons....
					if($key == $sortStr)
						$output .= $sortIconStr;
					$output .= $value;

				}

				$output .= "</th>";
				$colinr++;
				$tabindx++;
			}//end of for each loop
		}
		$output .= "</tr>

        </thead>";

		$output .="<tbody>";

		// Start Looping Data
		$ii=0;
                
		foreach($paginator as $p) {
			$cell_color = ($ii % 2 == 0 ? "row1" : "row2");
			$ii++;$bodyCount = 0;
			$output.="<tr onclick='selectrow($name,this);' class='$cell_color'>";
			// Reset Fields Array to Top
			if(!empty($fields)) 
			{ 
				reset($fields); 
				foreach($fields AS $k=>$v) {
								$tdclass = '';
					// Look for additional attributes
					$characterlimit = 40;
					if(is_array($v)) {
						$class = (@$v['class'] != '')? 'class="'.$v['class'].'" ':'';
						$align = (@$v['align'] != '')? 'align="'.$v['align'].'" ':'';
						$valign = (@$v['valign'] != '')? 'valign="'.$v['valign'].'" ':'';
						if(isset($v['characterlimit']))
							$characterlimit = $v['characterlimit'];
						$output .= "<td {$tdclass}{$align}{$valign}>";
					} else {
						if($k == 'description' && $menuName == 'Screening Type')
							$characterlimit = 80;
						$output .= "<td {$tdclass}>";
					}
					// Check to see if this Field is in Extra Columns
					if(isset($this->extra[$k]['value'])) {
						$output .= $this->_parseExtra($k,$p);
					} else {					
						if( $bodyCount== 0 && $jsFillFnName != '')
						{
							$valToInclude = (strlen($p[$k])>$characterlimit)? substr($p[$k],0,$characterlimit)."..":$p[$k];
							$output .= "<a onclick= ".$jsFillFnName."(\"/id/$p[id]\") href= 'javascript:void(0)' title='".addslashes (htmlspecialchars(strip_tags ($p[$k])))."' >".addslashes (htmlspecialchars(strip_tags ($valToInclude)))."</a>";
						}
						else{
                                                    
							$p = (array)$p;
							if(isset($p[$k])) {
							 $valToInclude = (strlen($p[$k])>$characterlimit)? substr($p[$k],0,$characterlimit)."..":$p[$k];
							//$output .= "<span  title='".addslashes (htmlspecialchars (strip_tags ($p[$k])))."' >".addslashes (htmlspecialchars (strip_tags($valToInclude)))."</span>";
							if($k == 'isactive' && $p[$k] == 'Inactive' && $menuName == 'Background check Process')
							{
								echo "<script>
										$(document).ready(function() { 
										$('#del'+".$p['id'].").remove();
										$('#edit'+".$p['id'].").remove();
										});
										</script>";
							}
							if($k == 'status' && $p[$k] == 'Complete' && $menuName == 'Employee Screening') $dataclass = 'class="greendata"'; else $dataclass = '';						
                                                         $output .= "<span ".$dataclass." title='".htmlentities(addslashes($p[$k]), ENT_QUOTES, "UTF-8")."' >".htmlentities(addslashes($valToInclude), ENT_QUOTES, "UTF-8")."</span>";
							}
							//$output .= $p[$k];
						}
					}

					$output .= "</td>";
					$bodyCount++;
				}
			}
			// Close the Table Row
			$output.="</tr>";

		}
		if($ii == 0){
			$output.= "<tr><td colspan='$colinr' class='no-data-td'><p class='no-data'>No data found</p></td></tr>";
		}
		$output .= "</tbody>";
		$output .="</table></div>";
		/*if($ii == 0){
			$output .="<div style='height:50px;'>&nbsp;</div>";	
		}*/
		// Attach Pagination
		if($paginator) {

			//$output .="<tfoot>";

			// $output .="<td align='center' colspan='".count($fields)."'>";
			$params = array();
			$params['jsGridFnName'] = $jsGridFnname;
			$params['perPage'] = $perPage;
			$params['objname'] = $name;
			$params['searchArray'] = $searchArray;			
			$params['formgrid'] = $formgrid;
			$params['con'] = $con;
			
			$output.= $this->view->paginationControl($paginator,

                    'Sliding',

                    'partials/pagination.phtml',$params);

			//$output .="</tfoot>";
		}
		$output .= "<script>$('#$name').slimScrollHorizontal({
									  alwaysVisible: false,
									  start: 'left',
									  position: 'bottom',
									 
									}).css({ background: '#ccc', paddingBottom: '10px' }); </script>";
		$output .= "<script>
						var id = $('#columnId').val();
						var coldata = $('#'+id).val();
						var focusID = $('#columnId').val();						
						$('#'+focusID).focus().val('').val(coldata);
					</script>";
		return $output;
	}

	/**
	 * Function that Parses Extra Column info
	 *
	 * Regex looks for {{field_name}}
	 *
	 * @param string $column
	 * @param array $p
	 * @return string
	 */
	public function _parseExtra($column,$p) {

		if(isset ($this->extra[$column])) {
			$val = '';

			$characterlimit = 15;
			if(isset($this->extra[$column]['characterlimit']))
						$characterlimit = $this->extra[$column]['characterlimit'];
			preg_match_all('/\{\{(.*?)\}\}/', $this->extra[$column]['value'], $matches);
			if(count($matches[1]) > 0) {
				$matches[1] = array_unique($matches[1]);
				$a = $this->extra[$column]['value'];
				//echo"<pre>";print_r($matches[1]);die;
				foreach($matches[1] AS $match) {
					$p = (array)$p;
					$a = str_replace('{{'.$match.'}}',$p[$match], $a);
					preg_match_all('/\[\[(.*?)\]\]/', $a, $newMaches);
					if(count($newMaches[1]) > 0) {
						foreach($newMaches[1] AS $matchNew) {

							$valToInclude = (strlen($p[$matchNew])>$characterlimit)? substr($p[$matchNew],0,$characterlimit)."..":$p[$matchNew];
							$a = str_replace('[['.$matchNew.']]',$valToInclude, $a);
						}
					}

				}
			}
			$val = $a;
			return $val;
		}

		return '';
	}
}
?>
