<?php

    namespace ECrimea;
    
    class Account extends \PCS\Account {
        
        protected static $_delayed = array(
            'ecrimea' => 'ECrimea'
        );
        
        protected $_ecrimea;
        
    }