<?php

class GW_Movie extends GW_Composite_Data_Object
{
    public $table = 'movies';
    
    public $composite_map = Array
    (
        'image1' => Array('gw_image', Array('dimensions_resize'=>'200x200', 'dimensions_min'=> '100x100')),
    );
    
    public $ignore_fields = ["mdbid"=>1];
    
    public function doImportFromFilelist($filelist)
    {
        $imdb = new Imdb();
        foreach ($filelist as $filename) {
            $title = pathinfo($filename, PATHINFO_FILENAME);
            $movieInfo = $imdb->getMovieInfo($title);
            
            if (isset($movieInfo['error'])) {
                echo "Error importing '{$title}': " . $movieInfo['error'] . "\n";
                continue;
            }
            
            // Assuming the movie data is stored in an associative array
            $movieData = [
                'title' => $movieInfo['title'],
                'original_title' => $movieInfo['original_title'],
                'year' => $movieInfo['year'],
                'rating' => $movieInfo['rating'],
                'genres' => implode(', ', $movieInfo['genres']),
                'directors' => implode(', ', $movieInfo['directors']),
                'writers' => implode(', ', $movieInfo['writers']),
                'stars' => implode(', ', $movieInfo['stars']),
                'cast' => implode(', ', $movieInfo['cast']),
                'mpaa_rating' => $movieInfo['mpaa_rating'],
                'also_known_as' => implode(', ', $movieInfo['also_known_as']),
                'usa_title' => $movieInfo['usa_title'],
                'release_date' => $movieInfo['release_date'],
                'release_dates' => implode(', ', $movieInfo['release_dates']),
                'plot' => $movieInfo['plot'],
                'poster' => $movieInfo['poster'],
                'poster_large' => $movieInfo['poster_large'],
                'poster_small' => $movieInfo['poster_small'],
                'poster_full' => $movieInfo['poster_full'],
                'runtime' => $movieInfo['runtime'],
                'top_250' => $movieInfo['top_250'],
                'oscars' => $movieInfo['oscars'],
                'awards' => $movieInfo['awards'],
                'nominations' => $movieInfo['nominations'],
                'storyline' => $movieInfo['storyline'],
                'tagline' => $movieInfo['tagline'],
                'votes' => $movieInfo['votes'],
                'language' => implode(', ', $movieInfo['language']),
                'country' => implode(', ', $movieInfo['country']),
            ];
            
            // Save the movie data to the database
            $this->createNewObject($movieData, true);
        }
    }
}
