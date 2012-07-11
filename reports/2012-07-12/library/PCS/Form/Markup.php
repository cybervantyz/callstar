<?php

    namespace PCS\Form;
    
    class Markup extends \PCS\Markup {
        
        public function render() {
            foreach ($this->g('elements') as $name => $element) {
                $contents = $element->g('e');
                if ((is_string($contents) == true) && ($this->h($contents) == true)) $contents = $this->g($contents);
                if (empty($contents) == false) continue;
                $this->g('elements')->remove($name);
            }
            return parent::render();
        }
        
    }