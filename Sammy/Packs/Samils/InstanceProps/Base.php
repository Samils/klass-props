<?php
/**
 * @version 2.0
 * @author Sammy
 *
 * @keywords Samils, ils, php framework
 * -----------------
 * @package Sammy\Packs\Samils\InstanceProps
 * - Autoload, application dependencies
 *
 * MIT License
 *
 * Copyright (c) 2020 Ysare
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace Sammy\Packs\Samils\InstanceProps {
  use Sammy\Packs\Samils\KlassProps\ErrorHelper;
  /**
   * Make sure the module base internal trait is not
   * declared in the php global scope defore creating
   * it.
   * It ensures that the script flux is not interrupted
   * when trying to run the current command by the cli
   * API.
   */
  if (!trait_exists('Sammy\Packs\Samils\InstanceProps\Base')){
  /**
   * @trait Base
   * Base internal trait for the
   * Samils\InstanceProps module.
   * -
   * This is (in the ils environment)
   * an instance of the php module,
   * wich should contain the module
   * core functionalities that should
   * be extended.
   * -
   * For extending the module, just create
   * an 'exts' directory in the module directory
   * and boot it by using the ils directory boot.
   * -
   */
  trait Base {

    public function setProp ($prop = null, $value = null) {
      # verify if '$prop' is an array
      # and map each key:property and value
      # pair inside it in order creating
      # new properties.
      if (is_array ($prop)) {
        # map each key-value pairs
        # inside the '$prop' array.
        foreach ($prop as $key => $value) {
          $this->setProp ($key, $value);
        }

        return $this;
      }

      $propNameRe = '/^([a-zA-Z_])([a-zA-Z0-9_]+)$/';

      if (!!(preg_match ($propNameRe, $prop))) {
        # Stop executing the function
        # if the current has not the
        # static property '$props'
        # declared.
        if (isset ($this->props) &&
          is_array ($this->props)) {
          $this->props [ $prop ] = $value;
        }
      }

      return $this;
    }

    public function getProp ($prop = null) {
      if (!isset ($this->props) &&
        is_array ($this->props)) {
        return;
      }

      $prefixes = array_merge ([''], ['#']);

      for ($i = 0; $i < count ($prefixes); $i++) {
        $propName = $prefixes [$i] . (string) ($prop);

        if (isset ($this->props [$propName])) {
          return $this->props [$propName];
        }
      }

      # Verify if the '$prop' string parameter is a valid
      # variable name and it's inside the '$props' array
      # contained in the current class context.
      if (!(isset ($this->props [$prop]))) {
        if (method_exists ($this, '__getProp')) {
          return $this->__getProp ($prop);
        } else {
          return null;
        }
      }
    }

    public function mergeProp ($prop, $altPropContent = []) {
      if (!isset ($this->props) &&
        is_array ($this->props)) {
        return;
      }

      # Verify if the '$prop' string parameter is a valid
      # variable name and it's inside the '$props' array
      # contained in the current class context.
      if (!isset ($this->props [$prop]) ||
        !is_array ($this->props [$prop])) {
        $this->props [$prop] = [];
      }

      $altPropContent = !is_array ($altPropContent) ? [] : (
        $altPropContent
      );

      $this->props [ $prop ] = array_merge (
        $this->props [ $prop ],
        $altPropContent
      );

      return $this;
    }

    public function asign ($propList = []) {
      if (!!(is_array ($propList) && $propList)) {
        foreach ($propList as $prop => $value) {
          $this->setProp ($prop, $value);
        }
      }
    }

    public function __get ($prop = null) {
      return $this->getProp ($prop);
    }

    public function __set ($prop = null, $value = null) {
      return $this->setProp ($prop, $value);
    }

    public function __call ($method, $arguments) {
      $methodProperty = $this->getProp ($method);

      if (is_object ($methodProperty) &&
        is_callable ($methodProperty)) {
        return call_user_func_array ($methodProperty, $arguments);
      }

      ErrorHelper::NoMethod ();
    }
    public function __toString () {
      return json_encode ($this->props);
    }
  }}
}
