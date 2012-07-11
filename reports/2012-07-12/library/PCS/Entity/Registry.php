<?php

    namespace PCS\Entity;
    
    class Registry extends \PCS\Entity\Collection {
        
        protected $_classname;
        protected $_limit;
        protected $_temp;
        
        public function save() {
            if ($this->g('modified') == false) return $this;
            parent::save();
            $classname = $this->g('classname');
            $schema = $classname::gS('schema');
            $languages = \PCS\Language::gS('registry');
            $tablename = mb_strtolower(str_replace('\\', '_', $classname));
            mysql_query('drop table if exists `' . $tablename . '`');
            if ($languages->g('count.current') == 0) return $this;
            if ($schema->g('fields.count.current') == 0) return $this;
            $query = 'create table `' . $tablename . '` (';
            $query .=    '`index` varchar(255) character set utf8 collate utf8_general_ci, ';
            $query .= '`language` varchar(255) character set utf8 collate utf8_general_ci, ';
            $counter = 0;
            foreach ($schema->g('fields') as $field) {
                $field = $field->g('e');
                if ($this->h('first') == true) {
                    $value = $this->g('first.e.' . $field->g('query'));
                    if ((is_object($value) == true) && ($value::isInstanceOf('PCS', 'Hub') == true)) {
                        $properties = $value::gS('properties');
                        foreach ($properties as $name) {
                            $property = $value->g($name);
                            if (is_object($property) == false) continue;
                            if ($property::isInstanceOf('PCS', 'Entity', 'Entry') == false) continue;
                            if ($counter > 0) $query .= ', ';
                            $query .= '`' . $field->g('name') . '.' . $name . '` text character set utf8 collate utf8_general_ci';
                            $counter ++;
                        }
                        continue;
                    }
                }
                if ($counter > 0) $query .= ', ';
                $query .= '`' . $field->g('name') . '` text character set utf8 collate utf8_general_ci';
                $counter ++;
            }
            $query .= ')';
            mysql_query($query);
            $query = 'insert into `' . $tablename . '` (`index`, `language`';
            foreach ($schema->g('fields') as $field) {
                $field = $field->g('e');
                if ($this->h('first') == true) {
                    $value = $this->g('first.e.' . $field->g('query'));
                    if ((is_object($value) == true) && ($value::isInstanceOf('PCS', 'Hub') == true)) {
                        $properties = $value::gS('properties');
                        foreach ($properties as $name) {
                            $property = $value->g($name);
                            if (is_object($property) == false) continue;
                            if ($property::isInstanceOf('PCS', 'Entity', 'Entry') == false) continue;
                            $query .= ', `' . $field->g('name') . '.' . $name . '`';
                        }
                        continue;
                    }
                }
                $query .= ', `' . $field->g('name') . '`';
            }
            $query .= ') values ';
            $counter = 0;
            $elements = $this->_elements;
            foreach ($elements as $element) {
                if (is_object($element) == false) $element = \PCS\Object\Registry::g($element);
                $element = $element->g('e');
                $languageElement = $languages->g('first');
                while (true) {
                    $language = $languageElement->g('e');
                    // now we will look for language-specific settings
                    // if any field has language-specific value, we must create row for that
                    $rowneeded = false;
                    foreach ($schema->g('fields') as $field) {
                        $field = $field->g('e');
                        $value = $element->g($field->g('query'));
                        if (is_object($value) == false) {
                            $rowneeded = true;
                            break;
                        }
                        if ($value::isInstanceOf('PCS', 'Hub') == true) {
                            $properties = $value::gS('properties');
                            foreach ($properties as $name) {
                                $property = $value->g($name);
                                if (is_object($property) == false) continue;
                                if ($property::isInstanceOf('PCS', 'Entity', 'Entry') == false) continue;
                                foreach ($property->g('instances') as $instance) {
                                    $instance = $instance->g('e');
                                    if ($instance->g('language.index') != $language->g('index')) continue;
                                    $rowneeded = true;
                                    break(3);
                                }
                            }
                            break;
                        }
                        foreach ($value->g('instances') as $instance) {
                            $instance = $instance->g('e');
                            if ($instance->g('language.index') != $language->g('index')) continue;
                            $rowneeded = true;
                            break(2);
                        }
                    }
                    if ($rowneeded == true) {
                        if ($counter > 0) $query .= ', ';
                        $query .= '("' . $element->g('index') . '", "' . $language->g('index') . '"';
                        foreach ($schema->g('fields') as $field) {
                            $field = $field->g('e');
                            $value = $element->g($field->g('query'));
                            if (is_object($value) == false) {
                                $query .= ', "' . htmlspecialchars($value, ENT_QUOTES) . '"';
                                continue;
                            }
                            if ($value::isInstanceOf('PCS', 'Hub') == true) {
                                $properties = $value::gS('properties');
                                foreach ($properties as $name) {
                                    $property = $value->g($name);
                                    if (is_object($property) == false) continue;
                                    if ($property::isInstanceOf('PCS', 'Entity', 'Entry') == false) continue;
                                    $v = '';
                                    foreach ($property->g('instances') as $instance) {
                                        $instance = $instance->g('e');
                                        if ($instance->g('language.index') != $language->g('index')) continue;
                                        $v = $instance->g('value');
                                        break;
                                    }
                                    $query .= ', "' . $v . '"';
                                }
                                continue;
                            }
                            $v = '';
                            foreach ($value->g('instances') as $instance) {
                                $instance = $instance->g('e');
                                if ($instance->g('language.index') != $language->g('index')) continue;
                                $v = $instance->g('value');
                                break;
                            }
                            $query .= ', "' . htmlspecialchars($v, ENT_QUOTES) . '"';
                        }
                        $query .= ')';
                        $counter ++;
                    }
                    if ($languageElement->h('next') == false) break;
                    $languageElement = $languageElement->g('next');
                }
            }
            mysql_query($query);
            return $this;
        }
        
        public function filter($filters) {
            $classname = $this->g('classname');
            $schema = $classname::gS('schema');
            $languages = \PCS\Language::gS('registry');
            $tablename = mb_strtolower(str_replace('\\', '_', $classname));
            $temp = array();
            foreach ($filters as $filter) {
                list($query, $comparement, $value) = $filter;
                foreach ($schema->g('fields') as $field) {
                    if ($field->g('e.query') != $query) continue;
                    $temp[$field->g('e.name')] = $filter;
                    break;
                }
            }
            $filters = $temp;
            $query = 'select `index` from `' . $tablename . '` where `language` in ("' . \PCS\Language::gS('any.index') . '", "' . \PCS\Language::gS('current.index') . '") ';
            foreach ($filters as $filter) {
                list($q, $comparement, $value) = $filter;
                $query .= ' and `' . $q . '` ' . $comparement . ' "' . $value . '"';
            }
            $result = mysql_query($query);
            $entries = array();
            while ($entry = mysql_fetch_assoc($result)) array_push($entries, $entry['index']);
            $this->_temp = array();
            foreach ($entries as $key => $index) $this->_temp[$key] = $index;
            return $this;
        }
        
        public function order($orderings) {
            if (count($orderings) == 0) return $this;
            if ($this->g('count.current') == 0) return $this;
            $directions = array(
                \PCS\Entity\Schema\Ordering::ASCENDING,
                \PCS\Entity\Schema\Ordering::DESCENDING
            );
            $classname = $this->g('classname');
            $schema = $classname::gS('schema');
            $languages = \PCS\Language::gS('registry');
            $tablename = mb_strtolower(str_replace('\\', '_', $classname));
            $temp = array();
            foreach ($orderings as $query => $direction) {
                if (in_array($direction, $directions, true) == false) continue;
                foreach ($schema->g('fields') as $field) {
                    if ($field->g('e.query') != $query) continue;
                    $temp[$field->g('e.name')] = $direction;
                    break;
                }
            }
            $orderings = $temp;
            if (count($orderings) == 0) return $this;
            $indexes = array();
            if (is_null($this->_temp) == true) {
                $this->_temp = $this->_elements;
                foreach ($this->_temp as $index => $element) {
                    if (is_object($element) == false) $element = \PCS\Object\Registry::g($element);
                    $this->_temp[$index] = $element->g('e.index');
                }
            }
            foreach ($this->_temp as $index) array_push($indexes, '"' . $index . '"');
            $query = 'select distinct `index` from `' . $tablename . '` where `index` in (' . implode(', ', $indexes) . ') order by ';
            $counter = 0;
            foreach ($orderings as $name => $direction) {
                if ($counter > 0) $query .= ', ';
                $query .= '`' . $name . '` ' . $direction;
                $counter ++;
            }
            $result = mysql_query($query);
            $entries = array();
            while ($entry = mysql_fetch_assoc($result)) array_push($entries, $entry['index']);
            $this->_temp = array();
            foreach ($entries as $key => $index) $this->_temp[$key] = $index;
            return $this;
        }
        
        protected function finalize() {
            parent::finalize();
            $this->s('temp', null);
            return $this;
        }
        
    }