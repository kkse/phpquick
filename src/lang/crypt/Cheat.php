<?php
/**
 * Created by PhpStorm.
 * User: kkse
 * Date: 2018/6/20
 * Time: 15:22
 */

namespace kkse\quick\lang\crypt;

/**
 * 假加密
 * Class Cheat
 * @package kkse\quick\lang\crypt
 */
class Cheat
{
    const AUTO = '';
    protected $key = null;
    protected $iv = null;
    protected $cipher = null;

    /**
     * Cheat constructor.
     * @param string $key
     * @param string $iv
     */
    public function __construct($key = self::AUTO, $iv = self::AUTO)
    {
        $this->key = $key;
        $this->iv = $iv;
        $this->cipher = new Cipher('AES-256-CBC', $key, $iv, OPENSSL_RAW_DATA);
    }

    /**
     * 加密
     * @param string $data 原文字符串
     * @return string
     */
    public function encrypt($data)
    {
        $this->key === self::AUTO and $this->cipher->setKey(random_bytes(32));
        $this->iv === self::AUTO and $this->cipher->genIv(true);

        $dedata = $this->cipher->encrypt($data);
        if (!$dedata) return $dedata;

        $text = $this->getMode();
        $this->key === self::AUTO and $text .= $this->cipher->getKey();
        $this->iv === self::AUTO and $text .= $this->cipher->getIv();
        $text .= $dedata;

        return base64_encode($text);
    }

    /**
     * 解密
     * @param string $dedata 密文
     * @return string
     */
    public function decrypt($dedata)
    {
        $text = base64_decode($dedata);
        $mode = substr($text, 0, 1);
        if ($mode != $this->getMode()) {
            return false;
        }
        $text = substr($text, 1);

        if ($this->key === self::AUTO) {
            $this->cipher->setKey(substr($text, 0, 32));
            $text = substr($text, 32);
        }

        if ($this->iv === self::AUTO) {
            $iv_length = $this->cipher->getIvLength();
            $this->cipher->setIv(substr($text, 0, $iv_length));
            $text = substr($text, $iv_length);
        }

        return $this->cipher->decrypt($text);
    }

    protected function getMode()
    {
        $mode = 0;
        $this->key === self::AUTO and $mode |= 1;
        $this->iv === self::AUTO and $mode |= 2;
        return chr($mode);
    }


    public static function quickEncrypt($data)
    {
        return (new self())->encrypt($data);
    }

    public static function quickDecrypt($data)
    {
        return (new self())->decrypt($data);
    }

}