<?php

    namespace PCS\Form\Element\Text;

    class Labelless extends \PCS\Form\Element\Text {
        
        public function render() {
            if ($this->h('value') == false) $this->s('value', $this->g('title'));
            ob_start(); ?>
            <script type="text/javascript">
                $('input[id="<?php print $this->g('id'); ?>"]').each(function() {
                    $(this).focus(function() {
                        if ($(this).val() == '<?php print $this->g('title'); ?>') $(this).val('').css('text-align', 'left');
                    }).blur(function() {
                        if ($(this).val() == '') $(this).val('<?php print $this->g('title'); ?>').css('text-align', 'center');
                    });
                    if ($(this).val() == '<?php print $this->g('title'); ?>') $(this).css('text-align', 'center');
                });
            </script><?php
            return parent::render() . ob_get_clean();
        }
        
    }