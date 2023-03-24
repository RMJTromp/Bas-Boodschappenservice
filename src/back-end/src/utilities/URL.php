<?php

    namespace Boodschappenservice\utilities;

    use Boodschappenservice\exceptions\MalformedURLException;

    /**
     * @property-read ?string $domain
     * @property-read ?string $hash
     * @property-read ?string $host
     * @property-read ?string $hostname
     * @property-read ?string $href
     * @property-read ?string $origin
     * @property-read ?string $password
     * @property-read ?string $pathname
     * @property-read ?int $port
     * @property-read string $protocol
     * @property-read ?string $search
     * @property-read array<string, string> $searchParams
     * @property-read ?string $tld
     * @property-read ?string $username
     */
    class URL implements \JsonSerializable {

        protected ?string $domain;
        protected ?string $hash;
        protected ?string $host;
        protected ?string $hostname;
        protected ?string $href;
        protected ?string $origin;
        protected ?string $password;
        protected ?string $pathname;
        protected ?int $port;
        protected string $protocol;
        protected ?string $search;
        /** @var array<string, string> $searchParams */
        protected array $searchParams;
        protected ?string $tld;
        protected ?string $username;

        private static ?URL $requestURL = null;
        public static function getRequestURL() : URL {
            if(self::$requestURL === null) self::$requestURL = new URL((($_SERVER['HTTPS'] ?? "") === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
            return self::$requestURL;
        }

        /**
         * @param string $url The URL to be parsed (only supported HTTP and HTTPS protocol)
         * @throws MalformedURLException
         */
        public function __construct(string $url) {
            $parsedUrl = self::parseUrl($url);

            $this->domain = null;
            $this->hash = $parsedUrl['hash'] ?? null;
            $this->host = $parsedUrl['host'] ?? null;
            $this->hostname = $parsedUrl['hostname'] ?? null;
            $this->href = $parsedUrl['href'] ?? null;
            $this->origin = $parsedUrl['origin'] ?? null;
            $this->password = $parsedUrl['password'] ?? null;
            $this->pathname = $parsedUrl['pathname'] ?? null;
            $this->port = $parsedUrl['port'] ?? null;
            $this->protocol = $parsedUrl['protocol'] ?? null;
            $this->search = $parsedUrl['search'] ?? null;
            $this->searchParams = $parsedUrl['searchParams'] ?? [];
            $this->username = $parsedUrl['username'] ?? null;

            if($this->hostname !== null) {
                $regex = RegExp::compile('/(?P<domain>[a-z0-9][a-z0-9-]{1,63}.[a-z.]{2,6})$/i');
                $matches = $regex->exec($this->hostname);
                if(!empty($matches['domain'][0])) {
                    $this->domain = $matches['domain'][0];

                    unset($regex, $matches);
                    $regex = RegExp::compile('/(?<=\.).+?$/');
                    $matches = $regex->exec($this->domain);
                    if(!empty($matches[0][0])) {
                        $this->tld = $matches[0][0];
                    }
                }
            }
        }

        /**
         * @param string|URL $url
         * @param array<string, string> $queryParams
         * @param bool $clearPreviousParams [optional] Whether the previous (if any) query parameters should be cleared
         * @return URL
         */
        public static function build(string|URL $url, array $queryParams, bool $clearPreviousParams = false) : URL {
            if(is_string($url)) $url = new URL($url);
            $query = http_build_query($clearPreviousParams ? $queryParams : array_merge($url->searchParams, $queryParams));
            return new URL(explode("?", $url->href, 2)[0] . "?" . $query . (!empty($url->hash) ? $url->hash : ""));
        }

        public function jsonSerialize() : array {
            $res = [];
            if(!empty($this->domain)) $res['domain'] = $this->domain;
            if(!empty($this->host)) $res['host'] = $this->host;
            if(!empty($this->hash)) $res['hash'] = $this->hash;
            if(!empty($this->hostname)) $res['hostname'] = $this->hostname;
            if(!empty($this->href)) $res['href'] = $this->href;
            if(!empty($this->origin)) $res['origin'] = $this->origin;
            if(!empty($this->password)) $res['password'] = $this->password;
            if(!empty($this->pathname)) $res['pathname'] = $this->pathname;
            if(!empty($this->port)) $res['port'] = $this->port;
            if(!empty($this->protocol)) $res['protocol'] = $this->protocol;
            if(!empty($this->search)) $res['search'] = $this->search;
            if(!empty($this->searchParams)) $res['searchParams'] = $this->searchParams;
            if(!empty($this->username)) $res['username'] = $this->username;
            if(!empty($this->tld)) $res['tld'] = $this->tld;
            return $res;
        }

        public function __toString(): string {
            return $this->href;
        }

        public function __get(string $name) {
            if(isset($this->$name)) return $this->$name;
            return null;
        }

        public function __set(string $name, $value) {
            return null;
        }

        /**
         * @throws MalformedURLException
         */
        private static function parseUrl(string $url) : array {
            if(filter_var($url, FILTER_VALIDATE_URL)) {
                $parsedUrl = parse_url($url);

                $response = [];
                if(!empty($parsedUrl['fragment'])) $response['hash'] = "#{$parsedUrl['fragment']}";
                if(!empty($parsedUrl['port'])) $response['port'] = (int) $parsedUrl['port'];
                if(!empty($parsedUrl['scheme'])) $response['protocol'] = $parsedUrl['scheme'] . ":";
                if(!empty($parsedUrl['host'])) {
                    $response['hostname'] = strtolower($parsedUrl['host']);
                    $response['host'] = strtolower($parsedUrl['host']) . (!empty($response['port']) ? ":".$response['port'] : "");

                    if(!empty($response['protocol'])) {
                        // get the protocol and leading slashes
                        $regex = new RegExp("/(".RegExp::quote($response['protocol'])."\/*)/");
                        $res = $regex->exec($url);
                        if(count($res) > 0) $response['origin'] = "{$res[0][0]}{$response['host']}";
                    }
                }
                $response['href'] = $url;
                if(!empty($parsedUrl['user'])) $response['username'] = $parsedUrl['user'];
                if(!empty($parsedUrl['pass'])) $response['password'] = $parsedUrl['pass'];
                if(!empty($parsedUrl['path'])) $response['pathname'] = $parsedUrl['path'];
                if(!empty($parsedUrl['query'])) {
                    $response['search'] = "?".$parsedUrl['query'];

                    $response['searchParams'] = [];
                    if(isset($parsedUrl['query'])) {
                        foreach (explode("&", $parsedUrl['query']) as $q) {
                            if(empty(trim($q))) continue;
                            $parts = explode("=", $q, 2);
                            if(count($parts) > 0) {
                                $response['searchParams'][$parts[0]] = $parts[1] ?? "";
                            }
                        }
                    }
                }
                return $response;
            } else throw new MalformedURLException("Invalid URL provided");
        }
    }