<?php

    namespace PCS\Form\Element;
    
    class Markup extends \PCS\Markup {
        
        public function initialize() {
            parent::initialize();
            $this->s('columns.count.maximum', 2)
                 ->g('elements')->append('&nbsp;', 'label'      )
                                ->append('&nbsp;', 'element'    )
                                ->append('&nbsp;', 'description')
                                ->append('&nbsp;', 'errors'     );
            return $this;
        }
        
        public function render() {
            if ($this->h('object.description') == false) $this->g('elements')->remove('description');
            if ($this->g('object.errors.count.current') == 0) $this->g('elements')->remove('errors');
            if ($this->h('elements.label'      ) == true) $this->s('elements.label'       . '.e', '<label for="' . $this->g('object.id') . '">' . $this->g('object.title') . '</label>');
            if ($this->h('elements.description') == true) $this->s('elements.description' . '.e', $this->g('object.description'));
            if ($this->h('elements.errors'     ) == true) $this->s('elements.errors'      . '.e', $this->g('object.errors'     ));
            return parent::render();
        }
        
    }