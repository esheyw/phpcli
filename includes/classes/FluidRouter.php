<?php
interface FluidRouterCacheEngine {
    public function get     ($key);
    public function set     ($key, $value, $expire=null);
    public function exists  ($key);
    public function hash    ($str, $salt);
    public function setSalt ($salt);
}

loadsc(); #load and alias the sanity check class, if it hasn't been done already

class FluidRouter {
    protected $cache;
    protected $keyID;
    protected $vCode;
    protected $hashFunc = null;
    protected $userAgent;

    protected $calls = [
                  'account/AccountStatus'         => [],
                  'account/APIKeyInfo'            => [],
                  'account/Characters'            => [],
                  'api/CallList'                  => [],
                  'char/AccountBalance'           => ['characterID'],
                  'char/AssetList'                => ['characterID'],
                  'char/CalendarEventAttendees'   => ['characterID', 'eventIDs'],
                  'char/CharacterSheet'           => ['characterID'],
                  'char/ContactList'              => ['characterID'],
                  'char/ContactNotifications'     => ['characterID'],
                  'char/Contracts'                => ['characterID', 'contractID'],
              ];

    const APIBASE = 'https://api.eveonline.com/';
    const VERSION = '0.1';
    const UA      = 'FluidRouter v'.self::VERSION.' created by Thallius O\'Quinn thalliusoquinn@gmail.com';
    const TF      = 'Y-m-d H:i:s';

    
    function __construct($keyID = null, $vCode = null, FluidRouterCacheEngine $cache = null)
    {
        $this->keyID          = $keyID;
        $this->vCode          = $vCode;
        $this->cache          = $cache;
        $this->userAgent      = self::UA;
    }

    #basic function for querying an endpoint. returns SimpleXMLElement.
    public function query($endpoint, array $args = []) {
        $args['keyID'] = $this->keyID;
        $args['vCode'] = $this->vCode;
        $cacheid = 'FRC'.$this->cache->hash(implode('',$args));
        echo $cacheid.n;
        if ($this->cache->exists($cacheid)) {
            echo 'Returning Cached API Call'.n;
            return json_decode($this->cache->get($cacheid));
        }
        else {
            $options = [
                'http' => [
                    'header'	 => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'	 => 'POST',
                    'user_agent' => $this->userAgent,
                    'content'	 => http_build_query($args)
                ]
        	];
            $context = stream_context_create($options);
            $xmlresponse = file_get_contents(self::APIBASE.$endpoint.'.xml.aspx', false, $context);

            $sxml = new SimpleXMLElement($xmlresponse);

            echo 'Returning Fresh API call, caching'.n;
            $this->cache->set($cacheid, json_encode($sxml), $this->_gmdiff($sxml->cachedUntil));
            return $sxml;
        }
    }

    #for setting a custom User-Agent
    public function setUserAgent($ua) {
        SC::cStr($ua, 'User Agent');    #sanity check for string
        return $this->userAgent = $ua;
    }

    public function setAPIKey($keyID, $vCode = null) {
        if (isset($vCode)) {

        }
    }

    #difference in seconds between a given time and the current GMT time
    private function _gmdiff($time) {
        return strtotime($time) - strtotime(gmdate(self::TF));
    }
}
?>