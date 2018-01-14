<?php
class Hook extends HookCore {

  public static function exec(
        $hook_name,
        $hook_args = array(),
        $id_module = null,
        $array_return = false,
        $check_exceptions = true,
        $use_push = false,
        $id_shop = null,
        $chain = false
    ){
    $controller_obj = Context::getContext()->controller;
    // dump($controller_obj);die();
    if($controller_obj && !$controller_obj->ajax){
      echo '<span style="background:red;color:white;padding:1px;">';
      echo "Hook::exec(\$hook_name=$hook_name, , \$id_module=$id_module)<br/>\n";
      echo '</span>';
    }
    return parent::exec($hook_name,
    $hook_args,
    $id_module,
    $array_return,
    $check_exceptions,
    $use_push,
    $id_shop,
    $chain);
  }
}
