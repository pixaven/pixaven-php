<?php

namespace Pixaven;

const VERSION = '1.0.0';

class Pixaven {

    /**
    * Pixaven constructor
    *
    * @param {String} key
    */

    public function __construct($key = '') {
        $this->options = array(
            'key' => $key,
            'request' => array()
        );

        if ($key == '') {
            $this->options['errorMessage'] = 'Pixaven constructor requires a valid API Key';
        }
    }


    /**
    * Uploads an image for processing
    *
    * @param {String} file
    * @returns {Pixaven}
    */

    public function upload($file = '') {
        if ($file == '') {
            $this->options['errorMessage'] = 'Pixaven upload(string) method requires a valid file path passed as an argument';
        }

        if (isset($this->options['withFetch']) && $this->options['withFetch'] == true) {
            $this->options['errorMessage'] = 'Pixaven only accepts one file input method per call: upload(string) or fetch(string)';
        }

        $this->options['withUpload'] = true;
        $this->options['file'] = $file;

        return $this;
    }


    /**
    * Provides a URL of the image for processing
    *
    * @param {String} url
    * @returns {Pixaven}
    */

    public function fetch($url = '') {
        if ($url == '') {
            $this->options['errorMessage'] = 'Pixaven fetch(string) method requires a valid file URL passed as an argument';
        }

        if (isset($this->options['withUpload']) && $this->options['withUpload'] == true) {
            $this->options['errorMessage'] = 'Pixaven only accepts one file input method per call: upload(string) or fetch(string)';
        }

        $this->options['withFetch'] = true;
        $this->options['url'] = $url;

        return $this;
    }


    /**
    * Sets a timeout for HTTP requests
    *
    * @param {Integer} timeout
    * @returns {Pixaven}
    */

    public function setTimeout($timeout = 30) {
        $this->options['timeout'] = $timeout;
        return $this;
    }


    /**
    * Sets a proxy for HTTP requests
    *
    * @param {String} proxy
    * @returns {Pixaven}
    */

    public function setProxy($proxy = '') {
        $components = parse_url($options['proxy']);

        if (!$components || !isset($components['host'])) {
            $this->options['errorMessage'] = 'Pixaven setProxy() method expects a valid HTTP proxy string as a parameter';
        }

        if ($proxy != '') {
            $this->options['proxy'] = $components;
        }

        return $this;
    }


    /**
    * Flips an image
    *
    * @param {Array} data
    * @returns {Pixaven}
    */

    public function flip($data = array()) {
        if (!empty($data)) {
            $this->options['request']['flip'] = $data;
        }

        return $this;
    }


    /**
    * Resizes an image
    *
    * @param {Array} data
    * @returns {Pixaven}
    */

    public function resize($data = array()) {
        if (!empty($data)) {
            $this->options['request']['resize'] = $data;
        }

        return $this;
    }


    /**
    * Scales an image
    *
    * @param {Array} data
    * @returns {Pixaven}
    */

    public function scale($data = array()) {
        if (!empty($data)) {
            $this->options['request']['scale'] = $data;
        }

        return $this;
    }


    /**
    * Applies a watermark
    *
    * @param {Array} data
    * @returns {Pixaven}
    */

    public function watermark($data = array()) {
        if (!empty($data)) {
            $this->options['request']['watermark'] = $data;
        }

        return $this;
    }


    /**
    * Applies an elliptical mask
    *
    * @param {Array} data
    * @returns {Pixaven}
    */

    public function mask($data = array()) {
        if (!empty($data)) {
            $this->options['request']['mask'] = $data;
        }

        return $this;
    }


    /**
    * Applies a filter
    *
    * @param {Array} data
    * @returns {Pixaven}
    */

    public function filter($data = array()) {
        if (!empty($data)) {
            $this->options['request']['filter'] = $data;
        }

        return $this;
    }


    /**
    * Adjusts visual parameters
    *
    * @param {Array} data
    * @returns {Pixaven}
    */

    public function adjust($data = array()) {
        if (!empty($data)) {
            $this->options['request']['adjust'] = $data;
        }

        return $this;
    }


    /**
    * Automatically enhances an image
    *
    * @param {Array} data
    * @returns {Pixaven}
    */

    public function auto($data = array()) {
        if (!empty($data)) {
            $this->options['request']['auto'] = $data;
        }

        return $this;
    }


    /**
    * Applies a border to an image
    *
    * @param {Array} data
    * @returns {Pixaven}
    */

    public function border($data = array()) {
        if (!empty($data)) {
            $this->options['request']['border'] = $data;
        }

        return $this;
    }


    /**
    * Pads an image
    *
    * @param {Array} data
    * @returns {Pixaven}
    */

    public function padding($data = array()) {
        if (!empty($data)) {
            $this->options['request']['padding'] = $data;
        }

        return $this;
    }


    /**
    * Stores processed image externally
    *
    * @param {Array} data
    * @returns {Pixaven}
    */

    public function store($data = array()) {
        if (!empty($data)) {
            $this->options['request']['store'] = $data;
        }

        return $this;
    }


    /**
    * Sets output format and encoding
    *
    * @param {Array} data
    * @returns {Pixaven}
    */

    public function output($data = array()) {
        if (!empty($data)) {
            $this->options['request']['output'] = $data;
        }

        return $this;
    }


    /**
    * Sets a Webhook as a response delivery method
    *
    * @param {Array} data
    * @returns {Pixaven}
    */

    public function webhook($data = array()) {
        if (!empty($data)) {
            $this->options['request']['webhook'] = $data;
        }

        return $this;
    }


    /**
    * Controls the Pixaven CDN behaviour
    *
    * @param {Array} data
    * @returns {Pixaven}
    */

    public function cdn($data = array()) {
        if (!empty($data)) {
            $this->options['request']['cdn'] = $data;
        }

        return $this;
    }


    /**
    * Sends a standard request to the API
    * and returns a JSON response
    *
    * @param {Function} cb
    * @returns {Response}
    */

    public function toJSON($callback = array()) {
        if (gettype($callback) != 'object') {
            $this->options['errorMessage'] = 'Pixaven toJSON(fn) method requires a callback function';
        }

        if (isset($this->options['toFile']) && $this->options['toFile'] == true) {
            $this->options['errorMessage'] = 'Pixaven only accepts one response method per call: toJSON(fn), toFile(string, fn) or toBuffer(fn)';
        }

        if (isset($this->options['toBuffer']) && $this->options['toBuffer'] == true) {
            $this->options['errorMessage'] = 'Pixaven only accepts one response method per call: toJSON(fn), toFile(string, fn) or toBuffer(fn)';
        }

        if (!isset($this->options['withUpload']) && !isset($this->options['withFetch'])) {
            $this->options['errorMessage'] = 'No file input has been specified with either upload(string) or fetch(string) method';
        }

        $this->options['toJSON'] = true;

        $client = new Client($this->options);

        $client->sendRequest(function ($error, $meta) use ($callback) {
            return $callback($error, $meta);
        });
    }


    /**
    * Instructs the API to use a Binary Response
    * and streams the response to disk
    *
    * @param {String} path
    * @param {Function} cb
    * @returns {Pixaven}
    */

    public function toFile($path = '', $callback = array()) {
        if ($path == '') {
            $this->options['errorMessage'] = 'Pixaven toFile(string, fn) method requires a valid output file path as a first parameter';
        }

        if (gettype($callback) != 'object') {
            $this->options['errorMessage'] = 'Pixaven toFile(string, fn) method requires a callback function';
        }

        if (isset($this->options['toJSON']) && $this->options['toJSON'] == true) {
            $this->options['errorMessage'] = 'Pixaven only accepts one response method per call: toJSON(fn), toFile(string, fn) or toBuffer(fn)';
        }

        if (isset($this->options['toBuffer']) && $this->options['toBuffer'] == true) {
            $this->options['errorMessage'] = 'Pixaven only accepts one response method per call: toJSON(fn), toFile(string, fn) or toBuffer(fn)';
        }

        if (isset($this->options['request']['webhook'])) {
            $this->options['errorMessage'] = 'Binary responses with toFile(string, fn) method are not supported when using Webhooks';
        }

        if (isset($this->options['request']['store'])) {
            $this->options['errorMessage'] = 'Binary responses with toFile(string, fn) method are not supported when using External Storage';
        }

        if (!isset($this->options['withUpload']) && !isset($this->options['withFetch'])) {
            $this->options['errorMessage'] = 'No file input has been specified with either upload(string) or fetch(string) method';
        }

        if (!is_writable(dirname($path))) {
            $this->options['errorMessage'] = 'Unable to open output file path `' . $path . '` for writing';
        }

        $this->options['toFile'] = true;
        $this->options['outputPath'] = $path;

        $client = new Client($this->options);

        $client->sendRequest(function ($error, $meta) use ($callback) {
            return $callback($error, $meta);
        });
    }


    /**
    * Instructs the API to use a Binary Response
    * and returns a buffer to the user
    *
    * @param {Function} cb
    * @returns {Pixaven}
    */

    public function toBuffer($callback = array()) {
        if (gettype($callback) != 'object') {
            $this->options['errorMessage'] = 'Pixaven toBuffer(fn) method requires a callback function';
        }

        if (isset($this->options['toJSON']) && $this->options['toJSON'] == true) {
            $this->options['errorMessage'] = 'Pixaven only accepts one response method per call: toJSON(fn), toFile(string, fn) or toBuffer(fn)';
        }

        if (isset($this->options['toFile']) && $this->options['toFile'] == true) {
            $this->options['errorMessage'] = 'Pixaven only accepts one response method per call: toJSON(fn), toFile(string, fn) or toBuffer(fn)';
        }

        if (isset($this->options['request']['webhook'])) {
            $this->options['errorMessage'] = 'Binary responses with toBuffer(fn) method are not supported when using Webhooks';
        }

        if (isset($this->options['request']['store'])) {
            $this->options['errorMessage'] = 'Binary responses with toBuffer(fn) method are not supported when using External Storage';
        }

        if (!isset($this->options['withUpload']) && !isset($this->options['withFetch'])) {
            $this->options['errorMessage'] = 'No file input has been specified with either upload(string) or fetch(string) method';
        }

        $this->options['toBuffer'] = true;

        $client = new Client($this->options);

        $client->sendRequest(function ($error, $meta, $buffer) use ($callback) {
            return $callback($error, $meta, $buffer);
        });
    }
}