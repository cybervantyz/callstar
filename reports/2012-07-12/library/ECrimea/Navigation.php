<?php

    namespace ECrimea;
    
    class Navigation extends \PCS\Navigation {
        
        protected static $_delayed = array(
            'top'    => 'Top',
            'left'   => 'Left',
            'bottom' => 'Bottom'
        );
        
        protected static $_top;
        protected static $_left;
        protected static $_bottom;
        
    }