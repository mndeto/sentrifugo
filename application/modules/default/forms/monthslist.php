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

class Default_Form_monthslist extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',DOMAIN.'monthslist/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'monthslist');


        $id = new Zend_Form_Element_Hidden('id');
		
		/*$monthname = new Zend_Form_Element_Text('month_name');
        $monthname->setAttrib('maxLength', 20);
        //$monthname->setAttrib('onblur', 'checkspecialcharactersformember(this.value,this.id);');
        $monthname->addFilter(new Zend_Filter_StringTrim());
        $monthname->setRequired(true);
        $monthname->addValidator('NotEmpty', false, array('messages' => 'Please enter Monthslist.'));  
        /*$monthname->addValidator("regex",true,array(
                           'pattern'=>'/^[a-zA-Z][a-zA-Z0-9\s\[\]\.\-#$@&_*()]*$/', 
                          // 'pattern'=>'/^[a-zA-Z][^(!~^?%`)]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please input Alphanumeric Value.'
                           )
        	));*/
			
		$monthname = new Zend_Form_Element_Select('month_id');
        $monthname->setAttrib('class', 'selectoption');
        $monthname->setRegisterInArrayValidator(false);
        $monthname->setRequired(true);
		$monthname->addValidator('NotEmpty', false, array('messages' => 'Please select month name.'));	
			
		$monthcode = new Zend_Form_Element_Text('monthcode');
        $monthcode->setAttrib('maxLength', 20);
        //$monthcode->setAttrib('onblur', 'checkspecialcharactersformember(this.value,this.id);');
        $monthcode->addFilter(new Zend_Filter_StringTrim());
		
		$monthcode->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_monthslist',
                                                        'field'=>'monthcode',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $monthcode->getValidator('Db_NoRecordExists')->setMessage('Month code already exists.'); 	
		
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');
		//$description->setAttribs(array('style' => 'resize:none;overflow:auto;border:none;'));
		
        $submit = new Zend_Form_Element_Submit('submit');
		// $submit->setLabel('Upload File')
		 $submit->setAttrib('id', 'submitbutton');
		 $submit->setLabel('Save');

		$url = "'monthslist/saveupdate/format/json'";
		$dialogMsg = "''";
		$toggleDivId = "''";
		$jsFunction = "'redirecttocontroller(\'monthslist\');'";;
		 

		 //$submit->setOptions(array('onclick' => "saveDetails($url,$dialogMsg,$toggleDivId,$jsFunction);"
		//));

		 $this->addElements(array($id,$monthname,$monthcode,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}