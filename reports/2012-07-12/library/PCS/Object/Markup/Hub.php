<?php

    namespace PCS\Object\Markup;
    
    class Hub extends \PCS\Hub {
        
        protected static $_delayed = array(
            'full'        => 'Full',
            'teaser'      => 'Teaser',
            'option'      => 'Option',
            'checkbox'    => 'Checkbox',
            'breadcrumbs' => 'Breadcrumbs',
            'navigation'  => 'Navigation'
        );
        
        protected $_full;
        protected $_teaser;
        protected $_option;
        protected $_checkbox;
        protected $_breadcrumbs;
        protected $_navigation;
        
    }