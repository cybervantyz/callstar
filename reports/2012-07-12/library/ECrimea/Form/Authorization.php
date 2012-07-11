<?php

    namespace ECrimea\Form;
    
    class Authorization extends \ECrimea\Form {
        
        public function initialize() {
            parent::initialize();
            $this->s('name'  , 'login')
                 ->s('method', static::METHOD_POST);
            $this->s('markup.elements.elements.e.columns.count.maximum', null)
                 ->g('markup.elements')->remove('title'      )
                                       ->remove('description')
                                       ->remove('errors'     );
            $elements = array(
                'registration' => array(
                    'classname' => array('Element', 'Link'),
                    'title' => 'Регистрация'
                ),
                'username' => array(
                    'classname' => array('Element', 'Text', 'Labelless'),
                    'title'     => 'Логин'
                ),
                'password' => array(
                    'classname' => array('Element', 'Password', 'Labelless'),
                    'title'     => 'Пароль'
                ),
                'forgot' => array(
                    'classname' => array('Element', 'Link'),
                    'title' => 'Забыли пароль?'
                )
            );
            $languages = \PCS\Language::gS('registry');
            foreach ($elements as $name => $data) {
                if (is_array($data) == false) {
                    $this->g('markup.elements.elements.e.elements')->append($data, $name);
                    continue;
                }
                $element = static::produce(implode('\\', $data['classname']));
                $element->s('name', $name)
                        ->s('title', $data['title']);
                $this->g('elements')->append($element);
                $this->g('markup.elements.elements.e.elements')->append($element, $name);
            }
            $this->s('elements.registration' . '.e.href', '/account/registration');
            $this->s('elements.forgot'       . '.e.href', '/account/forgot'      );
            $this->g('markup.elements.elements.e.elements')->append($this->g('elements.submit.e'), 'submit');
            $this->g('markup.elements')->remove('controls');
            $this->s('elements.submit.e.title', 'Войти');
            return $this;
        }
        
    }