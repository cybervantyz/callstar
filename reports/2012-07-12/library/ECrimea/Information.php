<?php

    namespace ECrimea;
    
    class Information extends \ECrimea\Entity {
        
        protected static $_delayed = array(
            'teaser' => array('PCS', 'Entity', 'Entry', 'String'),
            'body'   => array('PCS', 'Entity', 'Entry', 'String')
        );
        
        protected $_teaser;
        protected $_body;
        
    }