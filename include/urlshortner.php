<?php
//url shortner class
class UrlShortner
{
    protected static $chars = "123456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ"; //selected characters which excludes the vowels and few characters which are visually same
    protected static $table = "url_shortner";
    protected static $checkUrlExists = true;

    protected $pdo;
    protected $timestamp;
	
	//the obvious contructor
    public function __construct(PDO $pdo)
	{
        $this->pdo = $pdo;
        $this->timestamp = $_SERVER["REQUEST_TIME"];
    }

	//first check for valid url and then convert it into short
    public function urlToShortUrl($url) 
	{
        if (empty($url)) 
		{
            throw new Exception("No URL was supplied.");
        }

        if ($this->validateUrlFormat($url) == false) 
		{
            throw new Exception(
                "URL does not have a valid format.");
        }

        if (self::$checkUrlExists) 
		{
            if (!$this->verifyUrlExists($url)) 
			{
                throw new Exception("URL does not appear to exist.");
            }
        }

        $shortUrl = $this->urlExistsInDb($url);
        if ($shortUrl == false) 
		{
            $shortUrl = $this->createShortUrl($url);
        }

        return $shortUrl;
    }

	//validate the url format passed by users
    protected function validateUrlFormat($url) 
	{
        return filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
    }
	
	//check if url exist
    protected function verifyUrlExists($url) 
	{
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch,  CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return (!empty($response) && $response != 404);
    }

	//time to check if url already exist in database or not
    protected function urlExistsInDb($url) 
	{
        $query = "SELECT url_shortner FROM ".self::$table." WHERE long_url = :long_url LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $params = array("long_url" => $url);
        $stmt->execute($params);

        $result = $stmt->fetch();
        return (empty($result)) ? false : $result["short_url"];
    }

	//create short url
    protected function createShortUrl($url) 
	{
        $id = $this->insertUrlInDb($url);
        $shortUrl = $this->convertIntToShortUrl($id);
        $this->insertShortUrlInDb($id, $shortUrl);
        return $shortUrl;
    }

	//insert the url into database
    protected function insertUrlInDb($url) 
	{
        $query = "INSERT INTO ".self::$table." (long_url, created_on)"." VALUES (:long_url, :timestamp)";
        $stmnt = $this->pdo->prepare($query);
        $params = array(
            "long_url" => $url,
            "timestamp" => $this->timestamp
        );
        $stmnt->execute($params);

        return $this->pdo->lastInsertId();
    }

	//using int for shorturl
    protected function convertIntToShortUrl($id) {
        $id = intval($id);
        if ($id < 1) 
		{
            throw new Exception(
                "The ID is not a valid integer");
        }

        $length = strlen(self::$chars);
        // make sure length of available characters is at
        // least a reasonable minimum - there should be at
        // least 10 characters
        if ($length < 10) 
		{
            throw new Exception("Length of chars is too small");
        }

        $code = "";
        while ($id > $length - 1) 
		{
            // determine the value of the next higher character
            // in the short url should be and prepend
            $code = self::$chars[fmod($id, $length)].$code;
            // reset $id to remaining value to be converted
            $id = floor($id / $length);
        }

        // remaining value of $id is less than the length of
        // self::$chars
        $code = self::$chars[$id] . $code;

        return $code;
    }

	//insert created shorturl in database
    protected function insertShortUrlInDb($id, $code) 
	{
        if ($id == null || $code == null) 
		{
            throw new Exception("Input parameter(s) invalid.");
        }
        $query = "UPDATE " . self::$table . " SET short_url = :short_url WHERE id = :id";
        $stmnt = $this->pdo->prepare($query);
        $params = array(
            "short_url" => $code,
            "id" => $id
        );
        $stmnt->execute($params);

        if ($stmnt->rowCount() < 1) 
		{
            throw new Exception("Row was not updated with short url.");
        }
		
        return true;
    }
	
	//decode the short url
	public function shorturlToUrl($code, $increment = true) 
	{
        if (empty($code)) 
		{
            throw new Exception("No short url was supplied.");
        }
 
        if ($this->validateShortUrl($code) == false) 
		{
            throw new Exception("Short url does not have a valid format.");
        }
 
        $urlRow = $this->getUrlFromDb($code);
        if (empty($urlRow)) 
		{
            throw new Exception("Short url does not appear to exist.");
        }
 
        if ($increment == true) 
		{
            $this->incrementCounter($urlRow["id"]);
        }
 
        return $urlRow["long_url"];
    }
	
	//check if short url is valid
    protected function validateShortUrl($code) 
	{
        return preg_match("|[" . self::$chars . "]+|", $code);
    }
	
	//get original url from database
    protected function getUrlFromDb($code) 
	{
        $query = "SELECT id, long_url FROM " . self::$table . " WHERE short_url = :short_url LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $params=array(
            "short_url" => $code
        );
        $stmt->execute($params);
 
        $result = $stmt->fetch();
        return (empty($result)) ? false : $result;
    }
 
	//increase the hit
    protected function incrementCounter($id) {
        $query = "UPDATE " . self::$table . " SET hits = hits + 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $params = array(
            "id" => $id
        );
        $stmt->execute($params);
    }
}
?>