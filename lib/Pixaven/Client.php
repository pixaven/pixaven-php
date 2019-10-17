<?php

namespace Pixaven;

class Client {

    /**
    * Client constructor
    *
    * @param {Array} options
    */

    public function __construct($options) {

        /**
        * Define common cURL settings
        */

        $this->curlSettings = array(
            CURLOPT_USERPWD => $options['key'] . ':',
            CURLOPT_USERAGENT => $this->getUserAgent(),
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FAILONERROR => false,
            CURLOPT_CAINFO => __DIR__ . '/../data/cacert.pem',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => $options['timeout']
        );


        /**
        * Validate and set HTTP proxy
        */

        if (isset($options['proxy'])) {
            $this->curlSettings[CURLOPT_PROXY] = $options['proxy']['host'];
            $this->curlSettings[CURLOPT_PROXYTYPE] = CURLPROXY_HTTP;

            if (isset($components['port'])) {
                $this->curlSettings[CURLOPT_PROXYPORT] = $options['proxy']['port'];
            }

            $proxyAuth = '';

            if (isset($options['proxy']['user'])) {
                $proxyAuth .= $options['proxy']['user'];
            }

            if (isset($options['proxy']['pass'])) {
                $proxyAuth .= ':' . $options['proxy']['pass'];
            }

            if ($proxyAuth != '') {
                $this->curlSettings[CURLOPT_PROXYAUTH] = CURLAUTH_ANY;
                $this->curlSettings[CURLOPT_PROXYUSERPWD] = $proxyAuth;
            }
        }

        $this->options = $options;
    }


    /**
    * Sends HTTPS request to the Pixaven Image API
    *
    * @param {Object} callback
    */

    public function sendRequest($callback) {
        if (isset($this->options['errorMessage'])) {
            return $callback($this->options['errorMessage'], null, null);
        }


        /**
        * Define empty headers array
        * wich will be injected together with cURL parameters
        */

        $curlHeaders = array();


        /**
        * Set the response mode to binary and setup cURL header function
        * when dealing with Binary Responses
        */

        if ($this->options['toFile'] || $this->options['toBuffer']) {
            $this->options['request']['response'] = array(
                'mode' => 'binary'
            );

            array_push($curlHeaders, 'X-Pixavwn-Binary: 1');

            $this->curlSettings[CURLOPT_HEADERFUNCTION] = array($this, 'readHeader');
        }


        /**
        * Use JSON request for fetch() mode
        */

        if ($this->options['withFetch']) {
            $data = json_encode($options['request']);

            array_push($curlHeaders, 'Content-Type: application/json');

            $this->curlSettings[CURLOPT_POSTFIELDS] = $data;
            $this->curlSettings[CURLOPT_URL] = 'https://api.pixaven.com/1.0/fetch';
        }


        /**
        * Use Multipart request for upload() mode
        */

        if ($this->options['withUpload']) {
            if (!file_exists($this->options['file'])) {
                $error = 'Input file `' . $this->options['file'] . '` does not exist';
                return $callback($error, null, null);
            }

            if (class_exists('CURLFile')) {
                $file = new \CURLFile($this->options['file']);
            } else {
                $file = '@' . $this->options['file'];
            }

            $data = array_merge(array(
                'file' => $file,
                'data' => json_encode($this->options['request'])
            ));

            $this->curlSettings[CURLOPT_POSTFIELDS] = $data;
            $this->curlSettings[CURLOPT_URL] = 'https://api.pixaven.com/1.0/upload';
        }


        /**
        * Set a 'write function' for cURL when dealing with toFile() method
        */

        if ($this->options['toFile']) {
            $this->curlSettings[CURLOPT_WRITEFUNCTION] = array($this, 'writeData');;
        }


        /**
        * Initialize a new cURL session
        */

        $req = curl_init();


        /**
        * Check if a cURL session has been initialized correctly
        */

        if ($req == false || $req == null) {
            $this->closeFileHandle();

            $error = 'Unable to initialize a new cURL session. Please check if cURL extension is installed correctly.';
            return $callback($error, null, null);
        }


        /**
        * Append custom headers
        */

        $this->curlSettings[CURLOPT_HTTPHEADER] = $curlHeaders;


        /**
        * Inject cURL parameters
        */

        curl_setopt_array($req, $this->curlSettings);


        /**
        * Execute cURL request
        */

        $result = curl_exec($req);


        /**
        * Check for a cURL session error and throw when one occurs
        */

        if ($result === false || $result === null) {
            $this->closeFileHandle();

            $error = 'cURL session error: ' . curl_error($req);
            return $callback($error, null, null);
        }


        /**
        * Close a cURL session
        */

        curl_close($req);


        /**
        * Close output file handle
        */

        $this->closeFileHandle();


        /**
        * Parse the response body when dealing with toJSON() requests
        * and return the data to the user
        */

        if ($this->options['toJSON']) {
            try {
                $response = json_decode($result, true);
            } catch (Exception $e) {
                $error = 'Unable to parse JSON response from the Pixaven Image API';
                return $callback($error, null, null);
            }

            if ($response['success'] == false) {
                $error = $response['message'];
                return $callback($error, $response, null);
            }

            return $callback(null, $response, null);
        }


        /**
        * Try to parse JSON data from X-Pixaven-Meta header
        */

        try {
            $meta = json_decode($this->metaHeader, true);
        } catch (Exception $e) {
            $error = 'Unable to parse JSON data from X-Pixaven-Meta header';
            return $callback($error, null, null);
        }


        /**
        * Check whether the API call resulted with a failed response
        * and pass the error message to the user
        */

        if ($meta['success'] == false) {
            $error = $meta['message'];
            return $callback($error, $meta, null);
        }


        /**
        * Return metadata to the user when dealing with toFile() requests
        */

        if ($this->options['toFile']) {
            return $callback(null, $meta, null);
        }


        /**
        * Return the buffer to the user when dealing with toBuffer() requests
        */

        if ($this->options['toBuffer']) {
            return $callback(null, $meta, $result);
        }
    }


    /**
    * Generates User-Agent string
    *
    * @returns {String}
    */

    private function getUserAgent() {
        $curl = curl_version();
        return 'Pixaven/' . VERSION . ' PHP/' . PHP_VERSION . ' CURL/' . $curl['version'];
    }


    /**
    * Closes file handle (used in toFile() requests)
    */

    private function closeFileHandle() {
        if (isset($this->options['outputHandle'])) {
            fclose($this->options['outputHandle']);
        }
    }


    /**
    * HTTP Header function called by cURL every time
    * a header from the response is parsed.
    *
    * Used to extract metadata from X-Pixaven-Meta header
    *
    * @param {Resource} curl
    * @param {String} header
    * @returns {Integer}
    */

    private function readHeader($curl, $header) {
        if (strpos($header, ':') == false) {
            return strlen($header);
        }

        list($key, $value) = explode(':', trim($header), 2);

        if (trim($key) == 'X-Pixaven-Meta') {
            $this->metaHeader = trim($value);
        }

        return strlen($header);
    }


    /**
    * Callback function used for toFile() requests called every time
    * cURL receives the data from the upstream.
    *
    * Since writeFile method will only be called when there's actually
    * any data to write it first checks if the file handle has been set.
    * This prevents writing an empty file to disk or truncating
    * existing file when the API responds with non-200 OK responses.
    *
    * @param {Resource} curl
    * @param {String} data
    * @returns {Integer}
    */

    private function writeData($curl, $data) {
        if (!isset($this->options['outputHandle'])) {
            $this->options['outputHandle'] = fopen($this->options['outputPath'], 'w');
        }

        if ($len = fwrite($this->options['outputHandle'], $data)) {
            return $len;
        } else {
            return strlen($data);
        }
    }
}