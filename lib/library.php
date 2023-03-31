<?php

class Info{
    
    private $ip;
    private $port;
    private $domin;
    private $ssl;
    private $session;
    private $headers;
    
    public function __construct($ip, $port, $domin, $ssl = 'http://', $session) {
        
        $this->ip = $ip;
        $this->ssl = $ssl;
        $this->port = $port;
        $this->domin = $domin;
        $this->session = $session;
        $this->headers = [
            "Accept-Encoding: gzip, deflate",
            "Accept-Language: en-US,en;q=0.5",
            "Connection: keep-alive",
            "Content-Length: 0",
            "Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
            "Cookie: session=" . $this->session,
            "Host: " . $this->ip . ':' . $this->port,
            "Origin: " . $this->ssl . $this->ip . ':' . $this->port,
            "Referer: " . $this->ssl .  $this->ip . ':' . $this->port . '/xui',
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/109.0",
            "X-Requested-With: XMLHttpRequest",
        ];
        
    }
    
    private function Request(string $url, bool $method = false, ?array $headers = null, $data = null): ?array {
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_POST => $method,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true
        ]);
        
        $result = json_decode(curl_exec($curl), true);
        if(curl_errno($curl)){
            $result = ['error' => curl_error($curl)];
        }
        curl_close($curl);
        return $result;
    }

    
    public function serviceStatus($remark){
        
        $url = $this->ssl . $this->ip . ':' . $this->port . '/xui/inbound/list';
        $result = $this->request($url, true, $this->headers)['obj'];
    
        foreach ($result as $item) {
            if ($remark == $item['remark']) {
                return $item;
            }
            foreach ($item['clientStats'] as $client) {
                if ($remark == $client['email']) {
                    return $client;
                }
            }
        }
        return null;
        
    }

    
}