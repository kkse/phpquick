<?php
/**
 * Created by PhpStorm.
 * User: kkse
 * Date: 2018/6/19
 * Time: 17:41
 */

namespace kkse\quick\lang\crypt;

/**
 * 对称加解密
 * Class Cipher
 * @package kkse\quick\lang\crypt
 */
class Cipher
{
    protected $iv = '';
    protected $key = '';
    protected $options = 0;
    protected $method = 'AES-256-CBC';
    protected $aad = '';
    protected $tag_length = 16;

    public function __construct($method = 'AES-256-CBC', $key = '', $iv = '', $options = 0, $aad = 0)
    {
        $this->setMethod($method);
        $this->setKey($key);
        $this->setIv($iv);
        $this->setOptions($options);
        $this->setAad($aad);
    }

    /**
     * @return string
     */
    public function getIv(): string
    {
        return $this->iv;
    }

    /**
     * @param string $iv
     */
    public function setIv(string $iv)
    {
        $this->iv = $iv;
    }

    public function genIv($setvi = false)
    {
        $iv = openssl_random_pseudo_bytes($this->getIvLength());
        $setvi and $this->setIv($iv);
        return $iv;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    public function getIvLength()
    {
        return openssl_cipher_iv_length($this->method);
    }

    /**
     * @param string $key
     */
    public function setKey(string $key)
    {
        $this->key = $key;
    }

    /**
     * @return int
     */
    public function getOptions(): int
    {
        return $this->options;
    }

    /**
     * @param int $options
     */
    public function setOptions(int $options)
    {
        $this->options = $options&(OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING);
    }

    /**
     * @return string
     */
    public function getAad(): string
    {
        return $this->aad;
    }

    /**
     * @param string $aad
     */
    public function setAad(string $aad)
    {
        $this->aad = $aad;
    }


    protected $is_aead = null;

    /**
     * @return int
     */
    public function getTagLength(): int
    {
        return $this->tag_length;
    }

    /**
     * @param int $tag_length
     */
    public function setTagLength(int $tag_length)
    {
        $this->tag_length = $tag_length;
        switch ($this->isAead()) {
            case 'GCM'://1-16
                if ($this->tag_length < 1 || $this->tag_length > 16) {
                    $this->tag_length = 16;
                }
                break;
            case 'CCM'://4、6、8、10、12、14、16
                if ($this->tag_length < 4 || $this->tag_length > 16 || $this->tag_length%2==1) {
                    $this->tag_length = 16;
                }
                break;
        }

    }

    /**
     * @return bool|string
     */
    public function isAead()
    {
        if (isset($this->is_aead)) return $this->is_aead;

        $mode = strtoupper(substr($this->method, -3));
        if (in_array($mode, ['GCM', 'CCM'])) {
            return $this->is_aead = $mode;
        } else {
            return $this->is_aead = false;
        }
    }

    public function setMethod($method)
    {
        if (in_array($method, self::getMethods())) {
            $this->method = $method;
            $this->is_aead = null;
            $this->isAead() and $this->setTagLength($this->tag_length);
        }
    }



    /**
     * 加密
     * @param string $data 原文字符串
     * @param null $tag
     * @return string
     */
    public function encrypt($data, &$tag = null)
    {
        if (strlen($this->iv) != $this->getIvLength()) {
            if ($this->iv) return false;
            $this->genIv(true);
        }

        if ($this->isAead()) {
            return openssl_encrypt($data, $this->method, $this->key, $this->options, $this->iv, $tag, $this->aad, $this->tag_length);
        } else {
            return openssl_encrypt($data, $this->method, $this->key, $this->options, $this->iv);
        }
    }

    /**
     * 解密
     * @param string $dedata 密文
     * @param null $tag
     * @return string
     */
    public function decrypt($dedata, $tag = null)
    {
        if ($this->isAead()) {
            if (empty($tag)) return false;
            if (!$this->checkTag($tag)) return false;
            return openssl_decrypt($dedata, $this->method, $this->key, $this->options, $this->iv, $tag, $this->aad);
        } else {
            return openssl_decrypt($dedata, $this->method, $this->key, $this->options, $this->iv);
        }
    }

    protected function checkTag($tag)
    {
        $tag_len = strlen($tag);
        $tag_length = $this->tag_length;//bak
        $this->setTagLength($tag_len);
        $check_length = $this->tag_length;
        $this->tag_length = $tag_length;//还原
        return $check_length == $tag_len;
    }

    /**
     * @return array
     */
    public static function getMethods()
    {
        return openssl_get_cipher_methods();
    }
}