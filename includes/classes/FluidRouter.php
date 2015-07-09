<?php
interface FluidRouterCacheEngine
{
    public function get($key);
    public function set($key, $value, $expire=null);
    public function exists($key);
    public function hash ($str, $salt);
}


class FluidRouter
{
    protected $cache;
    protected $keyID;
    protected $vCode;
    protected $cachehash;
    protected $hashfunc = null;

    protected $calls =
                    [
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
    const TF      = 'Y-m-d H:i:s';

    
    function __construct($keyID = null, $vCode = null, FluidRouterCacheEngine $cache = null)
    {
        $this->keyID          = $keyID;
        $this->vCode          = $vCode;
        $this->cache          = $cache;
    }
    #basic function for querying an endpoint. returns SimpleXMLElement.
    public function query($endpoint, array $args = [])
    {
        $args['keyID'] = $this->keyID;
        $args['vCode'] = $this->vCode;
        $cacheid = 'FRC'.$this->cache->hash(implode('',$args));
        echo $cacheid.n;
        if ($this->cache->exists($cacheid))
        {
            echo 'Returning Cached API Call'.n;
            return json_decode($this->cache->get($cacheid));
        }
        else
        {
            $options =
                [
                    'http' =>
                    [
                        'header'	=> "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'	=> 'POST',
                        'content'	=> http_build_query($args)
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

    private function _gmdiff($time)
    {
        return strtotime($time) - strtotime(gmdate(self::TF));
    }
    #local hash function if none provided

}
?>