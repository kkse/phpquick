<?php
namespace kkse\quick\pack;

/**
 * 将curl相关的包装
 * Created by PhpStorm.
 * User: kkse
 * Date: 2018/3/13
 * Time: 9:22
 */
class Curl
{
    //禁止设置的选项
    const NOT_SET = [
        CURLOPT_HEADERFUNCTION,
        CURLOPT_HEADER,
        CURLINFO_HEADER_OUT,
        CURLOPT_RETURNTRANSFER,
    ];
    protected $cl = null;
    protected $options = [];

    /**
     * @var bool
     */
    protected $result_info = false;

    /**
     * @var null|string
     */
    protected $result_hrader = null;


    public function __construct($options = [])
    {
        $this->options = $options;
    }

    public function __destruct()
    {
        $this->close();
    }

    public function close()
    {
        if ($this->cl) {
            curl_close($this->cl);
            $this->cl = null;
        }
    }

    public function __clone()
    {
        if ($this->cl) {
            $this->cl = curl_copy_handle($this->cl);
        }
    }

    /**
     * @param bool $setval
     * @return self
     */
    public function setResultInfo($setval)
    {
        $this->result_info = (bool)$setval;
        return $this;
    }

    /**
     * @return bool
     */
    public function getResultInfo()
    {
        return $this->result_info;
    }

    /**
     * @return bool
     */
    public function getResultHrader()
    {
        return isset($this->result_hrader);
    }

    /**
     * @param bool $setval
     * @return self
     */
    public function setResultHrader($setval)
    {
        $setval = (bool)$setval;
        if (isset($this->result_hrader) != $setval) {
            $this->result_hrader = $setval?'':null;
            if ($this->cl) {
                curl_setopt($this->cl, CURLOPT_HEADERFUNCTION,
                    $this->getSetHraderClosure($setval));
            }
        }

        return $this;
    }


    /**
     * @param int $option
     * @param mixed $value
     * @return self
     */
    public function setOpt($option , $value)
    {
        if (!in_array($option, self::NOT_SET)) {
            $this->options[$option] = $value;
            if ($this->cl) {
                curl_setopt($this->cl, $option, $value);
            }
        }

        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptArray(array $options)
    {
        $options = array_diff_key($options, array_flip(self::NOT_SET));
        $this->options = $options + $this->options;
        if ($this->cl) {
            curl_setopt_array($this->cl, $options);
        }

        return $this;
    }

    protected function getSetHraderClosure($setval = true)
    {
        if ($setval) {
            return function (){
                $str = func_get_arg(1);
                $this->result_hrader .= $str;
                return strlen($str);
            };
        } else {
            return function(){
                return strlen(func_get_arg(1));
            };
        }
    }

    protected function initCurl()
    {
        $this->cl and $this->close();

        $this->cl = curl_init();

        curl_setopt_array($this->cl, $this->options);

        if (isset($this->result_hrader)) {
            curl_setopt($this->cl, CURLOPT_HEADERFUNCTION, $this->getSetHraderClosure());
        }

        //有一些设置是固定的，且不允许修改
        curl_setopt($this->cl, CURLOPT_HEADER, false);
        curl_setopt($this->cl, CURLINFO_HEADER_OUT, true);//请求header是否在curl_getinfo中获取
        curl_setopt($this->cl, CURLOPT_RETURNTRANSFER, true);
    }

    public function exec(array $options = [])
    {
        $options and $this->setOptArray($options);
        $this->cl or $this->initCurl();
        $response = curl_exec($this->cl);

        $result = [
            'errno'=>curl_errno($this->cl),
            'error'=>curl_error($this->cl),
            'response'=>$response,
        ];

        $this->result_info and $result['info'] = curl_getinfo($this->cl);

        if (isset($this->result_hrader)) {
            $result['hrader'] = $this->result_hrader;
            $this->result_hrader = '';
        }

        return $result;
    }
}