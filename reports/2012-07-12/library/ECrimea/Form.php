<?php

    namespace ECrimea;
    
    class Form extends \PCS\Form {
        
        protected static $_delayed = array(
            'authorization' => 'Authorization',
            'search'        => 'Search'
        );
        
        protected static $_authorization;
        protected static $_search;
        
    }