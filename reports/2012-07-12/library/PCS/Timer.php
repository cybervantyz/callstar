<?php

    namespace PCS;

    abstract class Timer {
        
        protected static $_spent;
        protected static $_called;
        
        protected static $_current;
        
        public static function start($classname = 'overall', $methodname = '') {
            self::$_current[$classname][$methodname] = microtime(true);
        }
        
        public static function finish($classname = 'overall', $methodname = '') {
            if (isset(self::$_current[$classname][$methodname]) == true) {
                if (isset(self::$_spent [$classname][$methodname]) == false) self::$_spent [$classname][$methodname] = 0;
                if (isset(self::$_called[$classname][$methodname]) == false) self::$_called[$classname][$methodname] = 0;
                self::$_spent [$classname][$methodname] += microtime(true) - self::$_current[$classname][$methodname];
                self::$_called[$classname][$methodname] ++;
                unset(self::$_current[$classname][$methodname]);
            }
        }
        
        public static function report() {
            ob_start(); ?>
            <br />
            <table style="color:#FFFFFF;" border="1" cellpadding="3" cellspacing="0">
                <thead>
                    <tr>
                        <th>Classname</th>
                        <th>Methodname</th>
                        <th>Time (overall)</th>
                        <th>Time (%)</th>
                        <th>Called</th>
                        <th>Effectiveness</th>
                    </tr>
                </thead>
                <tbody><?php
                    foreach (self::$_spent as $classname => $methods) {
                        foreach ($methods as $methodname => $time) { ?>
                            <tr><?php
                                if ($classname == 'overall') { ?>
                                    <td colspan="2"><?php print $classname ; ?></td><?php
                                } else { ?>
                                    <td><?php print $classname ; ?></td>
                                    <td><?php print $methodname; ?></td><?php
                                } ?>
                                <td><?php
                                    if ($classname == 'overall') {
                                        print $time . ' (' . round(1 / $time) . ')';
                                    } else {
                                        print $time;
                                    } ?></td>
                                <td><?php
                                    if ($classname == 'overall') {
                                        print '-';
                                    } else {
                                        print round(($time / self::$_spent['overall']['']) * 100, 2);
                                    } ?>
                                </td>
                                <td><?php print self::$_called[$classname][$methodname]; ?></td>
                                <td><?php
                                    if ($classname == 'overall') {
                                        print '-';
                                    } else {
                                        print round(self::$_called[$classname][$methodname] / $time, 2);
                                    } ?>
                                </td>
                            </tr><?php
                        }
                    } ?>
                </tbody>
            </table><?php
        }
        
    }