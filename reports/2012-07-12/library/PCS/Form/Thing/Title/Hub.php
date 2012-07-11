<?php

    namespace PCS\Form\Thing\Title;
    
    class Hub extends \PCS\Object\Title\Hub {
        
        protected static $_delayed = array(
            'regular' => array('Entry', 'String')
        );
        
        protected $_regular;
        
    }