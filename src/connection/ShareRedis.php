<?php
namespace syslayer\basic;

use Iterator;
use Redis;
use think\App;
use think\Log;

/**
 * 共享的redis操作对象
 * Created by PhpStorm.
 * User: kkse
 * Date: 2017/6/29
 * Time: 11:54
 * @method bool psetex(string $key, int $ttl, string $value)
 * @method array|bool scan(Iterator &$iterator, string $pattern = null, int $count = 0 )
 * @method mixed client(string $command, string $arg = '')
 * @method mixed slowlog(string $command)
 * @method int getOption(string $name)
 * @method string ping()
 * @method string|bool get(string $key)
 * @method bool set(string $key, string $value, int $timeout = 0)
 * @method bool setex(string $key, int $ttl, string $value )
 * @method bool setnx(string $key, string  $value)
 * @method int del( string ...$keys)
 * @method int delete(string ...$keys)
 * @method Redis multi(int $mode = Redis::MULTI)
 * @method void watch(string|array $key)
 * @method void unwatch()
 * @method void subscribe(array $channels, string|array $callback )
 * @method void psubscribe(array $patterns,string|array $callback )
 * @method int publish(string $channel,string $message )
 * @method array|int pubsub(string $keyword, string|array $argument)
 * @method bool exists(string $key)
 * @method int incr(string $key)
 * @method float incrByFloat(string $key, float $increment)
 * @method int incrBy(string $key, int $value)
 * @method int decr(string $key)
 * @method int decrBy(string $key, int $value)
 * @method array getMultiple(array $keys)
 * @method int lPush(string $key, string $value, string ...$values)
 * @method int rPush(string $key, string $value, string ...$values)
 * @method int lPushx(string $key, string  $value)
 * @method int rPushx(string $key, string  $value)
 * @method string lPop(string $key)
 * @method string rPop(string $key)
 * @method array blPop(array $keys, int $timeout)
 * @method array brPop(array $keys, int $timeout)
 * @method int lLen(string $key)
 * @method int lSize(string $key)
 * @method String lIndex(string $key, int $index)
 * @method String lGet(string $key, int $index)
 * @method bool lSet(string $key, int $index, string $value)
 * @method array lRange(string $key,int $start,int $end )
 * @method array lGetRange(string $key,int $start,int $end )
 * @method array lTrim(string $key,int $start,int $stop )
 * @method array listTrim(string $key,int $start,int $stop )
 * @method int lRem(string $key,string $value, int $count )
 * @method int lRemove(string $key,string $value, int $count )
 * @method int lInsert(string $key,int $position,string $pivot,string $value )
 * @method int sAdd(string $key, string $value, string ...$values)
 * @method boolean sAddArray(string $key, array $values)
 * @method int sRem(string $key,string $member1,string ...$member2)
 * @method int sRemove(string $key,string $member1,string ...$member2)
 * @method bool sMove(string $srcKey,string $dstKey,string $member )
 * @method bool sIsMember(string $key,string $value )
 * @method bool sContains(string $key,string $value )
 * @method int sCard(string $key )
 * @method string sPop(string $key )
 * @method string|array sRandMember(string $key, int $count = null)
 * @method array sInter(string $key, string ...$keys)
 * @method int sInterStore(string $dstKey,string $key, string ...$keys)
 * @method array sUnion(string $key, string ...$keys)
 * @method int sUnionStore(string $dstKey,string $key, string ...$keys)
 * @method array sDiff(string $key, string ...$keys)
 * @method int sDiffStore(string $dstKey,string $key, string ...$keys)
 * @method array sMembers(string $key )
 * @method array sGetMembers(string $key )
 * @method string getSet(string $key,string $val)
 * @method string randomKey()
 * @method bool move(string $key , int     $dbindex)
 * @method bool rename(string $srcKey,string $dstKey)
 * @method bool renameKey(string $srcKey,string $dstKey)
 * @method bool renameNx(string $srcKey,string $dstKey)
 * @method bool expire(string $key ,int     $ttl)
 * @method bool pExpire(string $key ,int     $ttl)
 * @method bool setTimeout(string $key  ,int     $ttl)
 * @method bool expireAt(string $key, int $timestamp )
 * @method int dbSize()
 * @method bool bgrewriteaof()
 * @method string object(string $string  ,string     $key)
 * @method bool save( )
 * @method bool bgsave( )
 * @method int lastSave()
 * @method int wait(int $numSlaves,int $timeout )
 * @method int type(string $key )
 * @method int append(string $key,string $value )
 * @method string getRange(string $key,int $start,int $end )
 * @method string substr(string $key,int $start,int $end )
 * @method string setRange(string $key,int $offset,string $value )
 * @method int strlen(string $key)
 * @method int bitpos(string $key,int $bit,int $start = 0,int $end = null)
 * @method int getBit(string $key,int $offset )
 * @method int setBit(string $key,int $offset,bool|int $value )
 * @method int bitCount(string $key)
 * @method bool flushDB()
 * @method bool flushAll()
 * @method array sort(string $key,array $option = null )
 * @method mixed info(string $option = null )
 * @method bool resetStat( )
 * @method int ttl(string $key)
 * @method int pttl(string $key)
 * @method bool persist(string $key)
 * @method bool mset( array $array )
 * @method array mget( array $array )
 * @method int msetnx( array $array )
 * @method string rpoplpush(string $srcKey, string $dstKey)
 * @method string brpoplpush(string $srcKey, string $dstKey, int $timeout)
 * @method int zAdd(string $key,float $score1, string $value1, ...$arr)
 * @method array zRange(string $key,int $start,int $end,bool $withscores = null )
 * @method int zRem(string $key,string $member1,string ...$member2)
 * @method int zDelete(string $key,string $member1,string ...$member2)
 * @method array zRevRange(string $key,int $start,int $end,bool $withscores = null )
 * @method array zRangeByScore(string $key,int $start,int $end, array $options = array() )
 * @method array zRevRangeByScore(string $key,int $start,int $end, array $options = array() )
 * @method array zRangeByLex(string $key,int $min,int $max,int $offset = null,int $limit = null )
 * @method array zRevRangeByLex(string $key,int $min,int $max,int $offset = null,int $limit = null )
 * @method int zCount(string $key,int $start,int $end )
 * @method int zRemRangeByScore(string $key,float $start,float $end )
 * @method int zDeleteRangeByScore(string $key,float $start,float $end )
 * @method int zRemRangeByRank(string $key,int $start,int $end )
 * @method int zDeleteRangeByRank(string $key,int $start,int $end )
 * @method int zCard(string $key )
 * @method int zSize(string $key )
 * @method float zScore(string $key,string $member )
 * @method int zRank(string $key,string $member )
 * @method int zRevRank(string $key,string $member )
 * @method float zIncrBy(string $key,float $value,string $member )
 * @method int zUnion(string $Output,array $ZSetKeys, array $Weights = null,string $aggregateFunction = 'SUM')
 * @method int zInter(string $Output,array $ZSetKeys, array $Weights = null,string $aggregateFunction = 'SUM')
 * @method int hSet(string $key,string $hashKey,string $value )
 * @method bool hSetNx(string $key,string $hashKey,string $value )
 * @method string hGet(string $key,string $hashKey)
 * @method int hLen(string $key )
 * @method int hDel(string $key,string $hashKey1,string ... $hashKey2)
 * @method array hKeys(string $key )
 * @method array hVals(string $key )
 * @method array hGetAll(string $key )
 * @method bool hExists(string $key,string $hashKey )
 * @method int hIncrBy(string $key,string $hashKey,int $value )
 * @method float hIncrByFloat(string $key,float $field,string $increment )
 * @method bool hMset(string $key,array $hashKeys )
 * @method array hMGet(string $key,array $hashKeys )
 * @method array config(string $operation,string $key,string $value )
 * @method mixed evaluate(string $script,array $args = array(),int $numKeys = 0 )
 * @method mixed evalSha(string $scriptSha,array $args = array(),int $numKeys = 0 )
 * @method mixed evaluateSha(string $scriptSha,array $args = array(),int $numKeys = 0 )
 * @method mixed script(string $command,string $script )
 * @method string getLastError()
 * @method bool clearLastError()
 * @method string _prefix(string $value )
 * @method mixed _unserialize(string $value )
 * @method mixed _serialize(mixed $value )
 * @method string dump(string $key )
 * @method bool restore(string $key,int $ttl,string $value )
 * @method array time()
 * @method bool pfAdd(string $key, array $elements )
 * @method int pfCount(string|array $key )
 * @method bool pfMerge(string $destkey, array $sourcekeys )
 * @method mixed rawCommand(string $command,mixed $arguments )
 * @method int getMode()
 */
class ShareRedis
{
    protected static $handlers = [];
    protected $handler_key;
    protected $host;
    protected $port;
    protected $select;
    protected $persistent;
    protected $inhandler = false;
    protected $auth;
    protected $prefix;

    public function __construct($host, $port, $select = 0, $persistent = false, $auth = '', $prefix = '')
    {
        $this->handler_key = sprintf("%s-%s-%s-%s", $persistent?'P':'T', $host, $port, $auth);
        $this->host = $host;
        $this->port = $port;
        $this->select = $select;
        $this->persistent = $persistent;
        $this->auth = $auth;
        $this->prefix = $prefix;
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * @return Redis
     */
    protected function initHandler()
    {
        extension_loaded('redis') or trigger_error('不支持:redis', E_USER_ERROR);
        if (!isset(self::$handlers[$this->handler_key])) {
            $func = $this->persistent ? 'pconnect' : 'connect';
            $handler  = new Redis();
            $handler->$func($this->host, $this->port);
            $this->auth and $handler->auth($this->auth);
            $handler->select($this->select);
            $handler->setOption(Redis::OPT_PREFIX, $this->prefix);
            self::$handlers[$this->handler_key] = [$handler, $this->select, 0, $this->prefix];

            if (App::$debug) {
                Log::record(sprintf('[ CACHE ] INIT redis %s:%s',  $this->host, $this->port) , 'info');
            }
        }

        if (!$this->inhandler) {
            self::$handlers[$this->handler_key][2]++;
            $this->inhandler = true;
        }

        $handlers_info = self::$handlers[$this->handler_key];

        /** @var Redis $handler */
        $handler = $handlers_info[0];
        if ($this->select != $handlers_info[1]) {
            $handler->select($this->select);
            self::$handlers[$this->handler_key][1] = $this->select;
        }

        if ($this->prefix != $handlers_info[3]) {
            $handler->setOption(Redis::OPT_PREFIX, $this->prefix);
            self::$handlers[$this->handler_key][3] = $this->prefix;
        }

        return $handler;
    }

    /**
     * Set client option.
     *
     * @param   string  $name    parameter name
     * @param   string  $value   parameter value
     * @return  bool    TRUE on success, FALSE on error.
     * @example
     * <pre>
     * $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);        // don't serialize data
     * $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);         // use built-in serialize/unserialize
     * $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_IGBINARY);    // use igBinary serialize/unserialize
     * $redis->setOption(Redis::OPT_PREFIX, 'myAppName:');                      // use custom prefix on all keys
     * </pre>
     */
    public function setOption( $name, $value ) {
        if ($name == Redis::OPT_PREFIX) {
            if (is_string($value)) {
                $this->prefix = $value;
                return true;
            }
            return false;
        }

        return call_user_func_array([$this->initHandler(), __FUNCTION__], func_get_args());
    }

    /**
     * Switches to a given database.
     *
     * @param   int     $dbindex
     * @return  bool    TRUE in case of success, FALSE in case of failure.
     * @link    http://redis.io/commands/select
     * @example
     * <pre>
     * $redis->select(0);       // switch to DB 0
     * $redis->set('x', '42');  // write 42 to x
     * $redis->move('x', 1);    // move to DB 1
     * $redis->select(1);       // switch to DB 1
     * $redis->get('x');        // will return 42
     * </pre>
     */
    public function select( $dbindex ) {
        if ($this->select != $dbindex) {
            $this->select = $dbindex;
        }
        return true;
    }



    /**
     * Disconnects from the Redis instance, except when pconnect is used.
     */
    public function close() {
        if ($this->inhandler) {
            self::$handlers[$this->handler_key][2]--;
            if (self::$handlers[$this->handler_key][2] <= 0) {
                /** @var Redis $handler */
                $handler = self::$handlers[$this->handler_key][0];
                $handler->close();
                unset(self::$handlers[$this->handler_key]);
            }
            $this->inhandler = false;
        }
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->initHandler(), $name], $arguments);
    }

    /**
     * Returns the keys that match a certain pattern.
     *
     * @param   string  $pattern pattern, using '*' as a wildcard.
     * @return  array   of STRING: The keys that match a certain pattern.
     * @link    http://redis.io/commands/keys
     * @example
     * <pre>
     * $allKeys = $redis->keys('*');   // all keys will match this.
     * $keyWithUserPrefix = $redis->keys('user*');
     * </pre>
     */
    public function keys( $pattern ) {
        $keys = call_user_func_array([$this->initHandler(), __FUNCTION__], func_get_args());

        if ($keys && $this->prefix) {
            $length = strlen($this->prefix);
            foreach ($keys as $k =>$val) {
                substr($val, 0, $length) == $this->prefix
                and $keys[$k] = substr($val, $length);
            }
        }
        return $keys;
    }

    public function getKeys( $pattern ) {
        return $this->keys($pattern);
    }
}
