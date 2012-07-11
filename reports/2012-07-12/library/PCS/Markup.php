<?php

    namespace PCS;
    
    class Markup extends \PCS\Object {
        
        protected static $_denied  = array('children', 'illustration', 'title', 'description');
        protected static $_delayed = array(
            'elements' => array('Element', 'Collection'),
            'columns'  => array('Column' , 'Collection'),
            'rows'     => array('Row'    , 'Collection')
        );
        
        protected $_elements;
        protected $_columns;
        protected $_rows;
        protected $_object;
        
        protected $_classname;
        protected $_decorated;
        
        public function __toString() {
            return $this->render();
        }
        
        public function render() {
            $matrix = array();
            $current = array('row' => 0, 'column' => 0);
            $minimum = array(
                'rows'    => $this->g(   'rows.count.minimum'),
                'columns' => $this->g('columns.count.minimum')
            );
            $maximum = array(
                'rows'    => $this->g(   'rows.count.maximum'),
                'columns' => $this->g('columns.count.maximum')
            );
            foreach ($this->g('elements') as $key => $element) {
                $matrix[$current['row']][$current['column']] = $key;
                for ($i = 0; $i < $element->g('rowspan'); $i ++) {
                    for ($j = 0; $j < $element->g('colspan'); $j ++) {
                        if (($i == 0) && ($j == 0)) continue;
                        $matrix[$current['row'] + $i][$current['column'] + $j] = null;
                    }
                }
                $current['column'] += $element->g('colspan');
                if ((is_null($maximum['columns']) == false) && (count($matrix[$current['row']]) >= $maximum['columns'])) {
                    $current['column'] = 0;
                    if (isset($matrix[$current['row']]) == false) $matrix[$current['row']] = array();
                    while (array_key_exists($current['column'], $matrix[$current['row']]) == true) {
                        $current['column'] ++;
                        if ($current['column'] > $maximum['columns'] - 1) {
                            $current['row'] ++;
                            $current['column'] = 0;
                        }
                        if (isset($matrix[$current['row']]) == false) $matrix[$current['row']] = array();
                    }
                }
            }
            $lastindex = count($matrix) - 1;
            if (isset($matrix[$lastindex]) && (count($matrix[$lastindex]) == 0)) unset($matrix[$lastindex]);
            for ($i = 0; $i < max($minimum['rows'], count($matrix)); $i ++) {
                if (isset($matrix[0]) == false) $matrix[0] = array();
                for ($j = 0; $j < max($minimum['columns'], count($matrix[0])); $j ++) {
                    if (isset($matrix[$i]) == false) $matrix[$i] = array();
                    if (array_key_exists($j, $matrix[$i]) == true) continue;
                    $matrix[$i][$j] = '';
                }
            }
            $classname = 'markup';
            if ($this->h('classname') == true) $classname .= ' ' . $this->g('classname');
            ob_start(); ?>
            <table class="<?php print $classname; ?>" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tbody><?php
                    foreach ($matrix as $rowkey => $row) {
                        if ($rowkey > 0) { ?>
                            <tr><?php
                                foreach ($row as $columnkey => $element) {
                                    if (is_null($element) == true) continue;
                                    if ($columnkey > 0) { ?>
                                        <td class="divider node">&nbsp;</td><?php
                                    }
                                    $element = $this->g('elements.' . $element);
                                    if (is_object($element) == false) continue;
                                    for ($i = 0; $i < $element->g('colspan'); $i++) {
                                        if ($i > 0) { ?>
                                            <td class="divider node">&nbsp;</td><?php
                                        } ?>
                                        <td class="divider horizontal">&nbsp;</td><?php
                                    }
                                } ?>
                            </tr><?php
                        } ?>
                        <tr><?php
                            foreach ($row as $columnkey => $element) {
                                if (is_null($element) == true) continue;
                                $classname = 'element';
                                if (is_numeric($element) == true) {
                                    $classname .= ' element-' . $element;
                                } else {
                                    $classname .= ' ' . $element;
                                }
                                $classname = 'class="' . $classname . '"';
                                $element = $this->g('elements.' . $element);
                                if (is_object($element) == false) continue;
                                if ($columnkey > 0) { ?>
                                    <td class="divider vertical"><div class="inner">&nbsp;</div></td><?php
                                }
                                $current['column'] ++;
                                if (empty($element) == true) { ?>
                                    <td class="empty"><div class="inner">&nbsp;</div></td><?php
                                    continue;
                                }
                                $colspan = '';
                                $rowspan = '';
                                if ($element->g('colspan') > 1) $colspan = 'colspan="' . ($element->g('colspan') * 2 - 1) . '"';
                                if ($element->g('rowspan') > 1) $rowspan = 'rowspan="' . ($element->g('rowspan') * 2 - 1) . '"'; ?>
                                <td <?php print $classname; ?> <?php print $colspan; ?> <?php print $rowspan; ?>><?php
                                    $contents = $element->g('e');
                                    if ((in_array(gettype($contents), array('string', 'array'), true) == true) && ($this->h($contents) == true)) $contents = $this->g($contents);
                                    switch (gettype($contents)) {
                                        case 'array' :
                                            $contents = '<pre>' . print_r($contents, true) . '</pre>';
                                            break;
                                        case 'object':
                                            if ($contents::isInstanceOf('PCS', 'Illustration') == true) {
                                                $instance = $contents->g('instances.first.e');
                                                $w = $element->g('width' );
                                                $h = $element->g('height');
                                                $contents = '<img src="' . $instance->g($w . 'Æ' . $h . '.value') . '" alt="' . $instance->g('alternative') . '" />';
                                            } else {
                                                $contents = $contents->render();
                                            }
                                            break;
                                    }
                                    if ($element->g('decorated') == true) $contents = \PCS\Decoration::render($contents); ?>
                                    <div class="inner"><?php print $contents; ?></div>
                                </td><?php
                            } ?>
                        </tr><?php
                    } ?>
                </tbody>
            </table><?php
            $result = ob_get_clean();
            if ($this->g('decorated') == true) $result = \PCS\Decoration::render($result);
            return $result;
        }
        
    }