<?php

class ShortUrl {
    
	protected static $chars = "123456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ";
    protected static $table = "urls";
    protected static $checkUrlExists = true;
 
    protected $pdo;
    protected $timestamp;
    protected $hashid;
 
    public function __construct(PDO $pdo) 
    {
        $this->pdo = $pdo;
        $this->timestamp = date("Y-m-d H:i:s");
        $this->hashid = new Hashids\Hashids('ola-k-ase', 5, self::$chars);
    }

    public function urlToShortCode($url)
    {
        if (empty($url)) {
            throw new Exception("No URL was supplied.");
        }
 
        if ($this->validateUrlFormat($url) == false) {
            throw new Exception("URL does not have a valid format.");
        }
 
        if (self::$checkUrlExists) {
            if (!$this->verifyUrlExists($url)) {
                throw new Exception("URL does not appear to exist.");
            }
        }
 
        $shortCode = $this->urlExistsInDb($url);

        if ($shortCode == false) {
            $shortCode = $this->createShortCode($url);
        }
 
        return $shortCode;
    }

    protected function validateUrlFormat($url) 
    {
        return filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
    }

    protected function verifyUrlExists($url) 
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
 
        return ( !empty($response) && $response != 404 );
    }

    protected function urlExistsInDb($url)
    {
        $query = "SELECT hash_id FROM " . self::$table . " WHERE url = :long_url LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $params = array(
            "long_url" => $url
        );
        $stmt->execute($params);
 
        $result = $stmt->fetch();
        return (empty($result)) ? false : $result["hash_id"];
    }

    protected function createShortCode($url) 
    {
        $id = $this->insertUrlInDb($url);
        $shortCode = $this->convertIntToShortCode($id);
        $this->insertShortCodeInDb($id, $shortCode);
        return $shortCode;
    }

    protected function insertUrlInDb($url)
    {

        $query = "INSERT INTO " . self::$table . " (url, created_at) VALUES (:long_url, :timestamp)";

        $stmnt = $this->pdo->prepare($query);

        $params = array(
            "long_url" => $url,
            "timestamp" => $this->timestamp
        );

        $stmnt->execute($params);
 
        return $this->pdo->lastInsertId();
    }

    protected function convertIntToShortCode($id)
    {
        $id = intval($id);

        if ($id < 1) {
            throw new Exception("El ID no es un entero válido");
        }

        /*$length = strlen(self::$chars);

        if ($length < 10) {
            throw new Exception("Tamaño de los caracteres es muy pequeño");
        }

        $code = "";

        while ($id > $length - 1) {
            $key = (string) fmod($id, $length);
            $code = self::$chars[$key] . $code;
            $id = floor($id / $length);
        }

        $index = (string) $id;
        $code = self::$chars[$index] . $code;

        return $code;*/

        return $this->hashid->encode($id);
    }

    protected function insertShortCodeInDb($id, $code)
    {
        if ($id == null || $code == null) {
            throw new Exception("Parametros de entrada inválidos");
        }

        $query = "UPDATE " . self::$table . " SET hash_id = :hash_id WHERE id = :id";

        $stmnt = $this->pdo->prepare($query);

        $params = array("hash_id" => $code, "id" => $id);

        $stmnt->execute($params);

        if ($stmnt->rowCount() < 1) {
            throw new Exception("La columna no fue actualizada con el hash");
        }

        return true;
    }

    public function shortCodeToUrl($code, $increment = true) 
    {
        if (empty($code)) {
            throw new Exception("No short code was supplied.");
        }
 
        if ($this->validateShortCode($code) == false) {
            throw new Exception(
                "Short code does not have a valid format.");
        }
 
        $urlRow = $this->getUrlFromDb($code);

        if (empty($urlRow)) {
            throw new Exception(
                "Short code does not appear to exist.");
        }
 
        if ($increment == true) {
            $this->incrementCounter($urlRow["id"]);
        }
 
        return $urlRow["url"];
    }
 
    protected function validateShortCode($code) 
    {
        return preg_match("|[" . self::$chars . "]+|", $code);
    }
 
    protected function getUrlFromDb($code) 
    {
        $query = "SELECT id, url FROM " . self::$table .
            " WHERE hash_id = :hash_id LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $params=array(
            "hash_id" => $code
        );
        $stmt->execute($params);
 
        $result = $stmt->fetch();
        return (empty($result)) ? false : $result;
    }
 
    protected function incrementCounter($id) 
    {
        $query = "UPDATE " . self::$table .
            " SET counter = counter + 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $params = array(
            "id" => $id
        );
        $stmt->execute($params);
    }
}