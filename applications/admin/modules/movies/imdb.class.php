<?php
/////////////////////////////////////////////////////////////////////////////////////////////////////////
// Free PHP IMDb Scraper API for the new IMDb Template.
// Version: 2.5
// Author: Abhinay Rathore
// Website: http://www.AbhinayRathore.com
// Blog: http://web3o.blogspot.com
// Demo: http://lab.abhinayrathore.com/imdb/
// More Info: http://web3o.blogspot.com/2010/10/php-imdb-scraper-for-new-imdb-template.html
// Last Updated: Sept 22, 2011
/////////////////////////////////////////////////////////////////////////////////////////////////////////
 
class Imdb
{  
    function getMovieInfo($title)
    {
        $imdbId = $this->getIMDbIdFromGoogle(trim($title));
        if($imdbId === NULL){
            $arr = array();
            $arr['error'] = "No Title found in Search Results!";
            return $arr;
        }
        return $this->getMovieInfoById($imdbId);
    }
     
    function getMovieInfoById($imdbId)
    {
        $arr = array();
        $imdbUrl = "http://www.imdb.com/title/" . trim($imdbId) . "/";
        $html = $this->geturl($imdbUrl);
        if(stripos($html, "<meta name=\"application-name\" content=\"IMDb\" />") !== false){
            $arr = $this->scrapMovieInfo($html);
            $arr['imdb_url'] = $imdbUrl;
        } else {
            $arr['error'] = "No Title found on IMDb!";
        }
        return $arr;
    }
     
    function getIMDbIdFromGoogle($title){
        $url = "http://www.google.com/search?q=imdb+" . rawurlencode($title);
        $html = $this->geturl($url);
        $ids = $this->match_all('/<a href="http:\/\/www.imdb.com\/title\/(tt\d+).*?".*?>.*?<\/a>/ms', $html, 1);
        if (!isset($ids[0])) //if Google fails
            return $this->getIMDbIdFromBing($title); //search using Bing
        else
            return $ids[0]; //return first IMDb result
    }
     
    function getIMDbIdFromBing($title){
        $url = "http://www.bing.com/search?q=imdb+" . rawurlencode($title);
        $html = $this->geturl($url);
        $ids = $this->match_all('/<a href="http:\/\/www.imdb.com\/title\/(tt\d+).*?".*?>.*?<\/a>/ms', $html, 1);
        if (!isset($ids[0]))
            return NULL;
        else
            return $ids[0]; //return first IMDb result
    }
     
    // Scan movie meta data from IMDb page
    function scrapMovieInfo($html)
    {
        $arr = array();
        $arr['title_id'] = $this->match('/<link rel="canonical" href="http:\/\/www.imdb.com\/title\/(tt\d+)\/" \/>/ms', $html, 1);
        $arr['title'] = trim($this->match('/<title>(IMDb \- )*(.*?) \(.*?<\/title>/ms', $html, 2));
        $arr['original_title'] = trim($this->match('/class="title-extra">(.*?)</ms', $html, 1));
        $arr['year'] = trim($this->match('/<title>.*?\(.*?(\d{4}).*?\).*?<\/title>/ms', $html, 1));
        $arr['rating'] = $this->match('/ratingValue">(\d.\d)</ms', $html, 1);
        $arr['genres'] = array();
        foreach($this->match_all('/<a.*?>(.*?)<\/a>/ms', $this->match('/Genre.?:(.*?)(<\/div>|See more)/ms', $html, 1), 1) as $m)
            array_push($arr['genres'], $m);
        $arr['directors'] = array();
        foreach($this->match_all('/<a.*?>(.*?)<\/a>/ms', $this->match('/Director.?:(.*?)(<\/div>|>.?and )/ms', $html, 1), 1) as $m)
            array_push($arr['directors'], $m);
        $arr['writers'] = array();
        foreach($this->match_all('/<a.*?>(.*?)<\/a>/ms', $this->match('/Writer.?:(.*?)(<\/div>|>.?and )/ms', $html, 1), 1) as $m)
            array_push($arr['writers'], $m);
        $arr['stars'] = array();
        foreach($this->match_all('/<a.*?>(.*?)<\/a>/ms', $this->match('/Stars:(.*?)<\/div>/ms', $html, 1), 1) as $m)
            array_push($arr['stars'], $m);
        $arr['cast'] = array();
        foreach($this->match_all('/<td class="name">(.*?)<\/td>/ms', $html, 1) as $m)
            array_push($arr['cast'], trim(strip_tags($m)));
        $arr['mpaa_rating'] = $this->match('/infobar">.<img.*?alt="(.*?)".*?>/ms', $html, 1);
        //Get extra inforation on  Release Dates and AKA Titles
        if($arr['title_id'] != ""){
            $releaseinfoHtml = $this->geturl("http://www.imdb.com/title/" . $arr['title_id'] . "/releaseinfo");
            $arr['also_known_as'] = $this->getAkaTitles($releaseinfoHtml, $usa_title);
            $arr['usa_title'] = $usa_title;
            $arr['release_date'] = $this->match('/Release Date:<\/h4>.*?([0-9][0-9]? (January|February|March|April|May|June|July|August|September|October|November|December) (19|20)[0-9][0-9]).*?(\(|<span)/ms', $html, 1);
            $arr['release_dates'] = $this->getReleaseDates($releaseinfoHtml);
        }
        $arr['plot'] = trim(strip_tags($this->match('/<p itemprop="description">(.*?)(<\/p>|<a)/ms', $html, 1)));
        $arr['poster'] = $this->match('/img_primary">.*?<img src="(.*?)".*?<\/td>/ms', $html, 1);
        $arr['poster_large'] = "";
        $arr['poster_small'] = "";
        $arr['poster_full'] = "";
        if ($arr['poster'] != '' && strrpos($arr['poster'], "nopicture") === false && strrpos($arr['poster'], "ad.doubleclick") === false) { //Get large and small posters
            $arr['poster_large'] = preg_replace('/_V1\..*?.jpg/ms', "_V1._SY500.jpg", $arr['poster']);
            $arr['poster_small'] = preg_replace('/_V1\..*?.jpg/ms', "_V1._SY150.jpg", $arr['poster']);
            $arr['poster_full'] = preg_replace('/_V1\..*?.jpg/ms', "_V1._SY0.jpg", $arr['poster']);
        } else {
            $arr['poster'] = "";
        }
        $arr['runtime'] = trim($this->match('/Runtime:<\/h4>.*?(\d+) min.*?<\/div>/ms', $html, 1));
        if($arr['runtime'] == '') $arr['runtime'] = trim($this->match('/infobar.*?(\d+) min.*?<\/div>/ms', $html, 1));
        $arr['top_250'] = trim($this->match('/Top 250 #(\d+)</ms', $html, 1));
        $arr['oscars'] = trim($this->match('/Won (\d+) Oscars./ms', $html, 1));
        $arr['awards'] = trim($this->match('/(\d+) wins/ms',$html, 1));
        $arr['nominations'] = trim($this->match('/(\d+) nominations/ms',$html, 1));
        $arr['storyline'] = trim(strip_tags($this->match('/Storyline<\/h2>(.*?)(<em|<\/p>|<span)/ms', $html, 1)));
        $arr['tagline'] = trim(strip_tags($this->match('/Tagline.?:<\/h4>(.*?)(<span|<\/div)/ms', $html, 1)));
        $arr['votes'] = $this->match('/ratingCount">(\d+,?\d*)<\/span>/ms', $html, 1);
        $arr['language'] = array();
        foreach($this->match_all('/<a.*?>(.*?)<\/a>/ms', $this->match('/Language.?:(.*?)(<\/div>|>.?and )/ms', $html, 1), 1) as $m)
            array_push($arr['language'], trim($m));
        $arr['country'] = array();
        foreach($this->match_all('/<a.*?>(.*?)<\/a>/ms', $this->match('/Country:(.*?)(<\/div>|>.?and )/ms', $html, 1), 1) as $c)
            array_push($arr['country'], $c);
 
        if($arr['title_id'] != "") $arr['media_images'] = $this->getMediaImages($arr['title_id']);
 
        return $arr;
    }
     
    // Scan all Release Dates
    function getReleaseDates($html){
        $releaseDates = array();
        foreach($this->match_all('/<tr>(.*?)<\/tr>/ms', $this->match('/Date<\/th><\/tr>(.*?)<\/table>/ms', $html, 1), 1) as $r)
        {
            $country = trim(strip_tags($this->match('/<td><b>(.*?)<\/b><\/td>/ms', $r, 1)));
            $date = trim(strip_tags($this->match('/<td align="right">(.*?)<\/td>/ms', $r, 1)));
            array_push($releaseDates, $country . " = " . $date);
        }
        return $releaseDates;
    }
 
    // Scan all AKA Titles
    function getAkaTitles($html, &$usa_title){
        $akaTitles = array();
        foreach($this->match_all('/<tr>(.*?)<\/tr>/msi', $this->match('/Also Known As(.*?)<\/table>/ms', $html, 1), 1) as $m)
        {
            $akaTitleMatch = $this->match_all('/<td>(.*?)<\/td>/ms', $m, 1);
            $akaTitle = trim($akaTitleMatch[0]);
            $akaCountry = trim($akaTitleMatch[1]);
            array_push($akaTitles, $akaTitle . " = " . $akaCountry);
            if ($akaCountry != '' && strrpos(strtolower($akaCountry), "usa") !== false) $usa_title = $akaTitle;
        }
        return $akaTitles;
    }
 
    // Collect all Media Images
    function getMediaImages($titleId){
        $url  = "http://www.imdb.com/title/" . $titleId . "/mediaindex";
        $html = $this->geturl($url);
        $media = array();
        $media = array_merge($media, $this->scanMediaImages($html));
        foreach($this->match_all('/<a href="\?page=(.*?)">/ms', $this->match('/<span style="padding: 0 1em;">(.*?)<\/span>/ms', $html, 1), 1) as $p)
        {
            $html = $this->geturl($url . "?page=" . $p);
            $media = array_merge($media, $this->scanMediaImages($html));
        }
        return $media;
    }
 
    // Scan all media images
    function scanMediaImages($html){
        $pics = array();
        foreach($this->match_all('/src="(.*?)"/ms', $this->match('/<div class="thumb_list" style="font-size: 0px;">(.*?)<\/div>/ms', $html, 1), 1) as $i)
        {
            array_push($pics, preg_replace('/_V1\..*?.jpg/ms', "_V1._SY0.jpg", $i));
        }
        return $pics;
    }
 
    // ************************[ Extra Functions ]******************************
    function geturl($url)
    {
    	/*
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $ip=rand(0,255).'.'.rand(0,255).'.'.rand(0,255).'.'.rand(0,255);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("REMOTE_ADDR: $ip", "HTTP_X_FORWARDED_FOR: $ip"));
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/".rand(3,5).".".rand(0,3)." (Windows NT ".rand(3,5).".".rand(0,2)."; rv:2.0.1) Gecko/20100101 Firefox/".rand(3,5).".0.1");
        $html = curl_exec($ch);
        curl_close($ch);
        */
        
        $http_agent = new GW_Http_Agent;
        $html = $http_agent->getContents($url);
        
        return $html;
    }
 
    function match_all($regex, $str, $i = 0)
    {
        if(preg_match_all($regex, $str, $matches) === false)
            return false;
        else
            return $matches[$i];
    }
 
    function match($regex, $str, $i = 0)
    {
        if(preg_match($regex, $str, $match) == 1)
            return $match[$i];
        else
            return false;
    }
}
?>