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

class Default_Form_Creditcarddetails extends Zend_Form
{ 
	public function init()
	{
       $this->setMethod('post');		
       $this->setAttrib('id', 'formid');
       $this->setAttrib('name','creditcarddetails');
       //$this->setAttrib('action',DOMAIN.'creditcarddetails/add/');
		
        $id = new Zend_Form_Element_Hidden('id');
        $user_id = new Zend_Form_Element_Hidden('user_id');          
        //Card Type....(only alphabets)
                    
         $cardType = new Zend_Form_Element_Text('card_type');
	     $cardType->addFilter(new Zend_Filter_StringTrim());
         $cardType->setRequired(true);
		 $cardType->setAttrib('maxLength', 50);
         $cardType->addValidator('NotEmpty', false, array('messages' => 'Please enter your card type.')); 
         $cardType->addValidators(array(array('StringLength',false,
                                  array('min' => 2,
                                  		'max' => 50,
                                        'messages' => array(
                                        Zend_Validate_StringLength::TOO_LONG =>
                                        'Card type must contain at most %max% characters.',
                                        Zend_Validate_StringLength::TOO_SHORT =>
                                        'Card type must contain at least %min% characters.')))));
		 $cardType->addValidators(array(
			         array(
			             'validator'   => 'Regex',
			             'breakChainOnFailure' => true,
			             'options'     => array( 
			             'pattern' =>'/^[a-zA-Z\s]+$/i',
			                 'messages' => array(
			                         'regexNotMatch'=>'Please enter only alphabets.'
			                 )
			             )
			         )
			     )); 
     
          //Card Number....
	         $cardNum = new Zend_Form_Element_Text('card_number');
	         $cardNum->addFilters(array('StringTrim', 'StripTags'));
	         $cardNum->setRequired(true);
			 $cardNum->setAttrib("maxlength",16);
	         $cardNum->addValidator('NotEmpty', false, array('messages' => 'Please enter your card number.')); 
	         $cardNum->addValidators(array(array('StringLength',false,
	                                  array('min' => 16,
	                                  		'max' => 16,
	                                        'messages' => array(
	                                        Zend_Validate_StringLength::TOO_LONG =>
	                                        'Card number must contain at most %max% characters.',
	                                        Zend_Validate_StringLength::TOO_SHORT =>
	                                        'Card number must contain at least %min% characters.')))));
											
	      $cardNum->addValidator(new Zend_Validate_Db_NoRecordExists(
	                                            array(  'table'=>'main_empcreditcarddetails',
	                                                     'field'=>'card_number',
	                                                     'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'"',    
	
	                                                      ) ) );
	       $cardNum->getValidator('Db_NoRecordExists')->setMessage('Card number already exists.');
	     $cardNum->addValidators(array(
			         array(
			             'validator'   => 'Regex',
			             'breakChainOnFailure' => true,
			             'options'     => array( 
			             'pattern' =>'/^[0-9]+$/i',
			                 'messages' => array(
			                         'regexNotMatch'=>'Please enter only numbers.'
			                 )
			             )
			         )
			     )); 
		//Name on the card .... (only Alphabets)
		            
         $nameoncard = new Zend_Form_Element_Text('nameoncard');
	     $nameoncard->addFilter(new Zend_Filter_StringTrim());
         $nameoncard->setRequired(true);
		 $nameoncard->setAttrib('maxLength', 50);
         $nameoncard->addValidator('NotEmpty', false, array('messages' => 'Please enter your name on card.')); 
       	
		$nameoncard->addValidators(array(
			         array(
			             'validator'   => 'Regex',
			             'breakChainOnFailure' => true,
			             'options'     => array( 
			             'pattern' =>'/^[a-zA-Z\s]+$/i',
			                 'messages' => array(
			                         'regexNotMatch'=>'Please enter only alphabets.'
			                 )
			             )
			         )
			     )); 
     
         // Card Expiration Date ....
        $card_expired_date = new ZendX_JQuery_Form_Element_DatePicker('card_expiration');
		$card_expired_date->setOptions(array('class' => 'brdr_none'));	
		$card_expired_date->setRequired(true);
		$card_expired_date->setAttrib('readonly', 'true');
		$card_expired_date->setAttrib('onfocus', 'this.blur()'); 
        $card_expired_date->addValidator('NotEmpty', false, array('messages' => 'Please select date.'));
		// Expiration Date should be greater than today's date...
		$card_expired_date->addValidator(new sapp_DateGreaterThanToday());
		         
         //Card Code ...
         $card_code = new Zend_Form_Element_Text('card_code');
	     $card_code->addFilter(new Zend_Filter_StringTrim());
         $card_code->setRequired(true);
		 $card_code->setAttrib('maxLength', 4);
         $card_code->addValidator('NotEmpty', false, array('messages' => 'Please enter card code.')); 
         $card_code->addValidators(array(array('StringLength',false,
                                  array('min' => 3,
                                  		'max' => 4,
                                        'messages' => array(
                                        Zend_Validate_StringLength::TOO_LONG =>
                                        'Card code must contain at most %max% characters.',
                                        Zend_Validate_StringLength::TOO_SHORT =>
                                        'Card code must contain at least %min% characters.')))));
		$card_code->addValidators(array(
			         array(
			             'validator'   => 'Regex',
			             'breakChainOnFailure' => true,
			             'options'     => array( 
			             'pattern' =>'/^[0-9]+$/i',
			                 'messages' => array(
			                         'regexNotMatch'=>'Please enter only numbers.'
			                 )
			             )
			         )
			     ));

		//Card issued by....(company name)
         $card_issuedBy = new Zend_Form_Element_Text('card_issuedby');
	     $card_issuedBy->addFilter(new Zend_Filter_StringTrim());
         $card_issuedBy->setRequired(true);
		 $card_issuedBy->setAttrib('maxLength', 50);
         $card_issuedBy->addValidator('NotEmpty', false, array('messages' => 'Please enter card issued company name.')); 
       	$card_issuedBy->addValidators(array(
			         array(
			             'validator'   => 'Regex',
			             'breakChainOnFailure' => true,
			             'options'     => array( 
			             'pattern' =>'/^[a-zA-Z\s]+$/i',
			                 'messages' => array(
			                         'regexNotMatch'=>'Please enter only alphabets.'
			                 )
			             )
			         )
			     ));
         // Form Submit ......... 
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton');
        $submit->setLabel('Save');

        $this->addElements(array($id,$user_id,$cardType,$cardNum,$nameoncard,$card_expired_date,$card_issuedBy,$card_code,$submit));
		$this->setElementDecorators(array('ViewHelper')); 
		$this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('card_expiration'));
		
        }
}
?>