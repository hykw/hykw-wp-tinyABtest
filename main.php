<?php
/*
  Plugin Name: WordPress Tiny AB test
  Plugin URI: https://github.com/hykw/hykw-wp-tinyABtest
  Description: 簡易的なABテストを支援するプラグイン
  Author: hitoshi-hayakawa
  Version: 1.0.0
 */

class HYKWTinyABTest
{
  const COOKIE_AB = 'ABC';
  const MAX_AB_NUM = 2;

  private $ab;
  private $cookie_expire;
  private $isDisable;


  /**
   * __construct cookieの値をセット、なければサイコロを振る
   * 
   */
  function __construct()
  {
    $this->cookie_expire = time() + 60*30; // 30分
    $this->enable();


    if (isset($_COOKIE[self::COOKIE_AB])) {
      $cookie = $_COOKIE[self::COOKIE_AB];

      # cookie 改竄されてないか？
      if (is_numeric($cookie)) {
        if ( (0 <= $cookie) && ($cookie < self::MAX_AB_NUM)) {
          $this->ab = intval($cookie);
          return;
        }
      }
    }

    # cookieの値を利用できないので、値を再生成
    $this->ab = $this->_castDice(self::MAX_AB_NUM);
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

    return $this->ab;
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
      setcookie(self::COOKIE_AB, $this->ab, $this->cookie_expire);
  }


}

