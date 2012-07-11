<?php

    namespace ECrimea\Form;
    
    class Search extends \ECrimea\Form {
        
        public function initialize() {
            parent::initialize();
            $this->s('name', 'search');
            $this->g('markup.elements')->remove('title'      )
                                       ->remove('description')
                                       ->remove('errors'     )
                                       ->remove('controls'   );
            $elements = array(
                'query' => array(
                    'classname' => array('Element', 'Text', 'Labelless'),
                    'title'     => 'Поиск по сайту'
                )
            );
            foreach ($elements as $name => $data) {
                $element = static::produce(implode('\\', $data['classname']));
                $element->s('name', $name)
                        ->s('title.instances.first.e.value', $data['title']);
                $this->g('elements')->append($element);
                $this->g('markup.elements.elements.e.elements')->append($element, $name);
            }
            return $this;
        }
        
    }