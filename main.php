<?php
/*
  Plugin Name: WordPress Tiny AB test
  Plugin URI: https://github.com/hykw/hykw-wp-tinyABtest
  Description: 簡易的なABテストを支援するプラグイン
  Author: hitoshi-hayakawa
  Version: 2.0.0
 */

class HYKWTinyABTestBase
{
  const DEFAULT_COOKIE_NAME = 'ABC';

  private $cookie_value;
  private $cookie_expire;
  private $cookie_name;
  private $isDisable;
  protected $max_ab_num;


  /**
   * __construct cookieの値をセット、なければサイコロを振る
   * 
   * @param int $max_ab_num サイコロの範囲は0～(この値-1)までの間
   * @param int $expire_min Cookieの寿命(分): デフォルト30分
   * @param int $cookie_name cookieの名前
   * @param int $cookie_value $_COOKIEが無い場合のデフォルト値
   */
  function __construct($max_ab_num = FALSE, $expire_min = FALSE, 
                       $cookie_name = FALSE, $cookie_value = FALSE)
  {
    # デフォルト値のセット
    if ($max_ab_num == FALSE)
      $max_ab_num = 2;
    if ($expire_min == FALSE)
      $expire_min = 30;
    if ($cookie_name == FALSE)
      $cookie_name = self::DEFAULT_COOKIE_NAME;

    $this->cookie_expire = time() + ($expire_min * 60);
    $this->enable();
    $this->max_ab_num = $max_ab_num;
    $this->cookie_name = $cookie_name;


    # validation OK なら、cookieの値を信用して読み込む
    # 改竄されてたら再生成
    $cookie = FALSE;
    if (isset($_COOKIE[$this->cookie_name])) {
      $work = $_COOKIE[$this->cookie_name];

      if ($this->validateCookieValue($work))
        $cookie = $work;
    }


    # 引数をセット or 再生成
    if ($cookie == FALSE) {
      if ($cookie_value == FALSE)
        $cookie = $this->_castDice($this->max_ab_num);
      else
        $cookie = $cookie_value;
    }


    $this->cookie_value = $cookie;
  }


  /**
   * validateCookieValue 
   * 
   * @param mixed $cookie_value 
   * @return string TRUE:問題なし, FALSE:改竄とか想定外の値とか(要再生成）
   */
  function validateCookieValue($cookie_value)
  {
    return FALSE;
  }


  /**
   * disable 全機能のenable/disable設定（ソースはasisで残して、機能だけ止めたいときを想定）
   * 
   */
  function disable()
  {
    $this->isDisable = TRUE;
  }

  function enable()
  {
    $this->isDisable = FALSE;
  }

  function isDisable()
  {
    return $this->isDisable;
  }
  function isEnable()
  {
    return !$this->isDisable;
  }



  /**
   * getDice サイコロの値を返す
   * 
   * @return integer 値
   */
  function getDice()
  {
    # 停止時は常に同じ値を返す
    if ($this->isDisable)
      return 0;

    return $this->cookie_value;
  }


  /**
   * setDice サイコロの値をセットする
   * 
   * @param string $value セットする値
   */
  function setDice($value)
  {
    $this->cookie_value = $value;
  }


  /**
   * _castDice サイコロを振る
   * 
   * @param mixed $max 個数
   * @return integer: サイコロの値(0～max-1)
   */
  function _castDice($max)
  {
    $dice = mt_rand(0, $max-1);

    return $dice;
  }


  /**
   * writeABCookie cookieを出力
   * 
   */
  function writeABCookie()
  {
    if (!$this->isDisable)
      setcookie($this->cookie_name, $this->cookie_value, $this->cookie_expire);
  }


}


# 数字のみ許可
class NumericHYKWTinyABTest extends HYKWTinyABTestBase
{
  function validateCookieValue($cookie_value)
  {
    if (!is_numeric($cookie_value))
      return FALSE;

    if ( ($cookie_value < 0) || ($this->max_ab_num <= $cookie_value))
      return FALSE;

    return TRUE;
  }

}



