<?php

    namespace PCS\Entity;
    
    class Metadata extends \PCS\Object {
        
        protected static $_delayed = array(
            'fields' => array('Field', 'Collection')
        );
        protected static $_denied = array('undefined', 'any');
        
        protected $_fields;
        
        public function initialize() {
            parent::initialize();
            $this->g('fields')->append(static::produce('Field', 'Illustration'), 'illustration')
                              ->append(static::produce('Field', 'String'      ), 'title'       )
                              ->append(static::produce('Field', 'Text'        ), 'description' );
            $this->s('fields.illustration' . '.e.title.instances.first.e.value', 'Иллюстрация')
                 ->s('fields.title'        . '.e.title.instances.first.e.value', 'Название'   )
                 ->s('fields.description'  . '.e.title.instances.first.e.value', 'Описание'   );
            return $this;
        }
        
    }