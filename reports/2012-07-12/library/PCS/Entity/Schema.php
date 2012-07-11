<?php
    
    namespace PCS\Entity;
    
    class Schema extends \PCS\Object {
        
        protected static $_delayed = array(
            'fields' => array('Field', 'Collection')
        );
        
        protected $_fields;
        
    }