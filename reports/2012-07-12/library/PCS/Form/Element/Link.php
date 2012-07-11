<?php

    namespace PCS\Form\Element;

    class Link extends \PCS\Form\Element {
        
        protected $_type = self::TYPE_ELEMENT_LINK;
        protected $_href = '#';
        
        public function render() {
            ob_start();
            $classname = 'formelement type-' . $this->g('type') . ' ' . $this->g('name') . '';
            if ($this->g('decorated') == true) $contents = \PCS\Decoration::render($contents); ?>
            <div class="<?php print $classname; ?>"><a href="<?php print $this->g('href'); ?>" title="<?php print $this->g('description'); ?>"><?php print $this->g('title'); ?></a></div><?php
            return ob_get_clean();
        }
        
    }