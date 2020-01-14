<?php
    namespace Framework;

    final class ViewRenderer {
        private function __construct() {
            
        }

        public static function renderView($view, $model) {
            require(MVC::getViewPath() . "/$view.inc");
        }

        private static function htmlOut($string) {
            echo(nl2br(htmlentities($string)));
        }

        private static function beginActionForm($action, $controller, $params = null, $method = 'get', $cssClass = null) {
            $cc = $cssClass != null ? " class=\"$cssClass\"" : "";
            $form = <<<FORM
<form method="$method" action="?"$cc>
    <input type="hidden" name="c" value="$controller">
    <input type="hidden" name="a" value="$action">
FORM;
            echo($form);
            if(is_array($params)) {
                foreach($params as $name => $value) {
                    $form = <<<FORM
<input type="hidden" name="$name" value="$value">
FORM;
                    echo($form);
                }
            }
        }

        private static function endActionForm() {
            echo("</form>");
        }

        private static function actionLink($content, $action, $controller, $params = null, $cssClass = null) {
            $cc = $cssClass != null ? " class=\"$cssClass\"" : "";
            $url = MVC::buildActionLink($action, $controller, $params);
            $link = <<<LINK
<a href="$url"$cc>
LINK;
            echo($link);
            echo($content);
            echo('</a>');
        }

        private static function formField($label, $id, $name, $type, $contents = null, $cssClass = null) {
            $cc = "class=\"form-control" . ($cssClass != null ?  $cssClass : "") . "\"";
            $con = "";
            if ($contents != null) {
                foreach($contents as $c) {
                    $con .= $c;
                }
                $con .= "</$type>";
            }
            $field = <<<FIELD
<div class="form-group">
<label for="$id">$label</label>
<$type $cc id="$id" name="$name">$con
</div>
FIELD;
            echo($field);
        }
    }