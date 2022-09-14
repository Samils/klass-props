<?php
/**
 * @version 2.0
 * @author Sammy
 *
 * @keywords Samils, ils, php framework
 * -----------------
 * @package Sammy\Packs\Samils\KlassProps
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
namespace Sammy\Packs\Samils\KlassProps {
  /**
   * Make sure the module base internal trait is not
   * declared in the php global scope defore creating
   * it.
   * It ensures that the script flux is not interrupted
   * when trying to run the current command by the cli
   * API.
   */
  if (!trait_exists('Sammy\Packs\Samils\KlassProps\Base')){
  /**
   * @trait Base
   * Base internal trait for the
   * Samils\KlassProps module.
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
    /**
     * [setProp description]
     * @param string $prop  [description]
     * @param any $value [description]
     */
    public static function setProperty ($prop = null, $value = null) {

      # verify if '$prop' is an array
      # and map each key:property and value
      # pair inside it in order creating
      # new properties.
      if (is_array ($prop)) {
        # map each key-value pairs
        # inside the '$prop' array.
        foreach ($prop as $key => $value) {
          self::setProperty ($key, $value);
        }

        return;
      }

      $propNameRe = '/^([a-zA-Z_])([a-zA-Z0-9_]+)$/';

      if (!(preg_match ($propNameRe, $prop))) {
        return;
      }

      # Stop executing the function
      # if the current has not the
      # static property '$props'
      # declared.
      if (!isset(self::$props)) {
        return;
      }

      self::$props [ $prop ] = $value;
    }

    /**
     * [mergeProp description]
     * @param  array $prop           [description]
     * @param  array  $altPropContent [description]
     * @return null
     */
    public static function mergeProperty ($prop, $altPropContent = []) {
      if (!isset (self::$props)) {
        return;
      }

      # Verify if the '$prop' string parameter is a valid
      # variable name and it's inside the '$props' array
      # contained in the current class context.
      if (!isset (self::$props [$prop])) {
        return null;
      } elseif (!is_array (self::$props [$prop])) {
        self::$props [$prop] = [];
      }

      $altPropContent = !is_array ($altPropContent) ? [] : (
        $altPropContent
      );

      self::$props [ $prop ] = array_merge (
        self::$props [ $prop ],
        $altPropContent
      );
    }

    public static function getProperty ($prop = null) {
      if (!isset (self::$props)) {
        return;
      }

      $prefixes = array_merge ([''], ['#', '@', '%']);

      for ($i = 0; $i < count ($prefixes); $i++) {
        $propName = $prefixes [$i] . (string) ($prop);

        if (isset(self::$props [ $propName ] )) {
          return self::$props [$propName];
        }
      }

      # Verify if the '$prop' string parameter is a valid
      # variable name and it's inside the '$props' array
      # contained in the current class context.
      if (!(isset(self::$props[$prop]))) {
        if (method_exists(static::class, '__getPropery')) {
          return self::__getPropery($prop);
        } else {
          return null;
        }
      }
    }

    public function setProp () {
      return forward_static_call_array (
        [static::class, 'setProperty'], func_get_args()
      );
    }

    public function getProp () {
      return forward_static_call_array (
        [static::class, 'getProperty'], func_get_args()
      );
    }

    public function mergeProp () {
      return forward_static_call_array (
        [static::class, 'mergeProperty'], func_get_args()
      );
    }

    public function asign ($propList = []) {
      if (!(is_array ($propList) && $propList)) {
        return null;
      }

      foreach ($propList as $prop => $value) {
        self::setProperty ($prop, $value);
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

      if (is_callable ($methodProperty)) {
        return call_user_func_array ($methodProperty, $arguments);
      }

      ErrorHelper::NoMethod ();
    }
  }}
}
