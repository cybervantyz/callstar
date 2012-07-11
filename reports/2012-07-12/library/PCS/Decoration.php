<?php

    namespace PCS;

    abstract class Decoration {
        
        public static function render($contents) {
            ob_start(); ?>
            <table width="100%" class="decoration" cellpadding="0" cellspacing="0" border="0">
                <tbody>
                    <tr class="top">
                        <td class="left">&nbsp;</td>
                        <td class="center">&nbsp;</td>
                        <td class="right">&nbsp;</td>
                    </tr>
                    <tr class="middle">
                        <td class="left">&nbsp;</td>
                        <td class="center"><div class="inner"><?php print $contents; ?></div></td>
                        <td class="right">&nbsp;</td>
                    </tr>
                    <tr class="bottom">
                        <td class="left">&nbsp;</td>
                        <td class="center">&nbsp;</td>
                        <td class="right">&nbsp;</td>
                    </tr>
                </tbody>
            </table><?php
            return ob_get_clean();
        }
        
    }