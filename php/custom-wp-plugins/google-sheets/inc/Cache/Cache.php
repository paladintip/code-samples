<?php


namespace Inc\Cache;


class Cache {
    private $root;
    private $compile;
    private $ttl;

    public function __construct($options = []) {
        $this->options = array_merge(
            array(
                'root' => sys_get_temp_dir(),
                'ttl'  => false,
            ),
            $options
        );
        $this->root = $this->options['root'];
        $this->ttl = $this->options['ttl'];
    }

    public function set($key, $val, $ttl = null) {
        $ttl = $ttl === null ? $this->ttl : $ttl;
        $file = md5($key);
        $val = var_export(array(
            'expiry' => $ttl ? time() + $ttl : false,
            'data' => $val,
        ), true);

        // Write to temp file first to ensure atomicity
        $tmp = $this->root . '/' . $file . '.' . uniqid('', true) . '.tmp';
        file_put_contents($tmp, '<?php $val = ' . $val . ';', LOCK_EX);

        $dest = $this->root . '/' . $file;
        rename($tmp, $dest);
        if(function_exists ( "opcache_invalidate"))
        {
            if(file_exists($dest))
            {
                opcache_invalidate($dest);
            }
            else {
                error_log("No file at $dest", 3, dirname(__FILE__).'/cachelog.txt');
            }

        }
        else
        {
            error_log("opcache_invalidate is missing", 3, dirname(__FILE__).'/cachelog.txt');
        }

    }

    public function get($key) {
        @include $this->root . '/' . md5($key);

        // Not found
        if (!isset($val)) return false;

        // Found and not expired
        if (!$val['expiry'] || $val['expiry'] > time()) return $val['data'];

        // Expired, clean up
        $this->remove($key);
        return false;
    }

    public function remove($key) {
        $dest = $this->root . '/' . md5($key);
        if (@unlink($dest)) {
            // Invalidate cache if successfully written
            if(function_exists ( "opcache_invalidate"))
            {
                if(file_exists($dest))
                {
                    opcache_invalidate($dest);
                }
                else {
                    error_log("No file at $dest", 3, dirname(__FILE__).'/cachelog.txt');
                }

            }
            else
                {
                    error_log("opcache_invalidate is missing", 3, dirname(__FILE__).'/cachelog.txt');
                }

        }
    }
}