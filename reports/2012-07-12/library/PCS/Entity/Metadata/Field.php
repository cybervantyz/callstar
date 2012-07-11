<?php

    namespace PCS\Entity\Metadata;
    
    class Field extends \PCS\Object {
        
        const TYPE_IMAGE  = 'image' ;
        const TYPE_STRING = 'string';
        const TYPE_TEXT   = 'text'  ;
        
        protected $_type;
        
    }