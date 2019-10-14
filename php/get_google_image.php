<?php 

function slugify($text) { 

  $text = htmlspecialchars_decode($text);

  // replace non letter or digits by -
  $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

  // trim
  $text = trim($text, '-');

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // lowercase
  $text = strtolower($text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  if (empty($text)) {
      return 'n-a';
  }

  return $text;

}

function getGoogleImage( $keyword ) {

  $term = str_replace("-", "+", $keyword);

  $url = "https://www.google.com/search?q=" . $term . "&client=firefox-b&source=lnms&tbm=isch";

  $user_agent = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.73 Safari/537.36";

  $http_header = array( 
    "Accept: text/xml,application/xml,application/xhtml+xml,",
    "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5",
    "Cache-Control: max-age=0",
    "Connection: keep-alive",
    "Keep-Alive: 300",
    "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7",
    "Accept-Language: en-us,en;q=0.5",
    "Pragma: "
  );

  $referer = 'localhost';

  $options = array(
    CURLOPT_RETURNTRANSFER => true,     // return web page
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HEADER         => false,    // don't return headers
    CURLOPT_FOLLOWLOCATION => true,     // follow redirects
    CURLOPT_ENCODING       => "",       // handle all encodings
    CURLOPT_USERAGENT      => $user_agent, // who am i
    CURLOPT_AUTOREFERER    => true,     // set referer on redirect
    CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
    CURLOPT_TIMEOUT        => 120,      // timeout on response
    CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
    CURLOPT_HTTPHEADER     => $http_header,
    CURLOPT_COOKIEFILE     => "cookie.txt",
    CURLOPT_COOKIEJAR      => "cookie.txt",
    CURLOPT_REFERER        => $referer
  );

  $ch       = curl_init( $url );

  curl_setopt_array( $ch, $options );

  $content  = curl_exec( $ch );
  $content  = str_replace(array("\n","\r","\r\n","\n\r","\t","    ","   ","  "),'',$content);
  $content  = str_replace('> <','><',$content);

  curl_close( $ch );

  preg_match_all('/{"id":.*?}/', $content, $matches);

  $result = array();
  for ($i=0; $i < count($matches[0]); $i++) { 
    $obj = json_decode($matches[0][$i]);
    $result []= array(
      "id" => $obj->{'id'},
      "ou" => $obj->{'ou'}
    );
  }

  return $result;
}

$title = "car wallpaper";

$ts = slugify($title);
var_dump(getGoogleImage($ts));

?>