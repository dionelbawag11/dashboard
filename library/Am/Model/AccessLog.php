<?php

/**
 * Class represents records from table access_log
 * {autogenerated}
 * @property int $log_id
 * @property int $user_id
 * @property datetime $time
 * @property string $url
 * @property string $remote_addr
 * @property string $referrer
 * @see Am_Table
 */
class AccessLog extends Am_Record
{
}

class AccessLogTable extends Am_Table
{
    protected $_key = 'log_id';
    protected $_table = '?_access_log';
    protected $_recordClass = 'AccessLog';

    protected $_logged = false;

    public function insert(array $values, $returnInserted = false)
    {
        if (empty($values['time']))
            $values['time'] = $this->getDi()->sqlDateTime;
        return parent::insert($values, $returnInserted);
    }

    /** @access private */
    function _resetOnce($flag)
    {
        $this->_logged = (bool)$flag;
    }

    function clearOld($date)
    {
        $this->_db->query("DELETE FROM ?_access_log
            WHERE `time` < ? ", $date . ' 00:00:00');
    }

    function logOnce($user_id=null, $ip=null, $url=null, $referer=null)
    {
        if ($this->_logged) return;
        $this->log($user_id, $ip, $url, $referer);
        $this->_logged = true;
    }

    function log($user_id=null, $ip=null, $url=null, $referer=null)
    {
        $this->_db->query("INSERT INTO ?_access_log
        (time, user_id, remote_addr, url, referrer)
        VALUES
        (?, ?d, ?, ?, ?)"
            ,
            $this->getDi()->sqlDateTime,
            get_first($user_id, $this->getDi()->auth->getUserId ()),
            get_first($ip, $_SERVER['REMOTE_ADDR']),
            get_first($url, $_SERVER['REQUEST_URI']),
            get_first($referer, @$_SERVER['HTTP_REFERER']));
    }

    function isIpCountExceeded($user_id, $ip)
    {
        if (!$this->getDi()->config->get('max_ip_count'))
            return;
        $octets = 4 - $this->getDi()->config->get('max_ip_octets');
        if ($octets < 1)
            $octets = 4;

        $blocks = 8 - $this->getDi()->config->get('max_ip6_blocks');
        if ($blocks < 1)
            $blocks = 8;

        if (($octets == 4) && ($blocks == 8))
        {
            $ipCount = $this->_db->selectCell("SELECT COUNT(DISTINCT remote_addr)
                FROM ?_access_log
                WHERE user_id=?d
                AND time BETWEEN (? - INTERVAL ?d MINUTE) AND ? AND remote_addr <> ?",
                $user_id,
                $this->getDi()->sqlDateTime,
                $this->getDi()->config->get('max_ip_period', 10),
                $this->getDi()->sqlDateTime,
                $ip);
        } else {
            if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
                $ip_oct = implode(":", array_slice(explode(":", $ip), 0, $blocks));
            }else{
                $ip_oct = implode(".", array_slice(explode(".", $ip), 0, $octets));
            }
            $ipCount = $this->_db->selectCell("
                SELECT COUNT(DISTINCT IF(POSITION(':' IN remote_addr)=0, SUBSTRING_INDEX(remote_addr,'.',?d), SUBSTRING_INDEX(remote_addr,':',?d)))
                FROM ?_access_log
                WHERE user_id=?d
                AND time BETWEEN (? - INTERVAL ?d MINUTE) AND ? AND remote_addr NOT LIKE ?",
                $octets,
                $blocks,
                $user_id,
                $this->getDi()->sqlDateTime,
                $this->getDi()->config->get('max_ip_period', 10),
                $this->getDi()->sqlDateTime,
                $ip_oct . '%');
        }
        return $ipCount >= $this->getDi()->config->get('max_ip_count');
    }
}