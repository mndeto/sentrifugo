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

class Default_Form_educationlevelcode extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',DOMAIN.'educationlevelcode/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'educationlevelcode');


        $id = new Zend_Form_Element_Hidden('id');
		
		$educationlevelcode = new Zend_Form_Element_Text('educationlevelcode');
        $educationlevelcode->setAttrib('maxLength', 20);
        //$educationlevelcode->addFilter(new Zend_Filter_StringTrim());
        $educationlevelcode->setRequired(true);
        $educationlevelcode->addValidator('NotEmpty', false, array('messages' => 'Please enter education level.'));
		$educationlevelcode->addValidator("regex",true,array(                           
							   //'pattern'=>'/^([a-zA-Z.]+ ?)+$/',
							   'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z ]*)$/',
							   'messages'=>array(
								   'regexNotMatch'=>'Please enter valid education level.'
							   )
					));		
		$educationlevelcode->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_educationlevelcode',
                                                        'field'=>'educationlevelcode',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $educationlevelcode->getValidator('Db_NoRecordExists')->setMessage('Education level already exists.');		
   	
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$educationlevelcode,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}