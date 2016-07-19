<?php namespace FileCommitAnimator;

/**
 * Extracts and provides various data from/about a github repository.
 * All data is returned as a JSON string. 
 * 
 * @version 1.0
 * @link https://github.com/KareemG/FileCommitAnimator
 * @author Kareem Golaub <kareemag@live.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Kareem Golaub
 */
class GithubRepoExtractor {
    private $apiUrl = "https://api.github.com";

    // stream context options
    private $opts;

    /**
     * @param string $ownerName Username of repository's owner.
     * @param string $repoName Name of repository.
     */
    function __construct($ownerName, $repoName, $credentials) {
        $this->ownerName = $ownerName;
        $this->repoName = $repoName;
        $this->opts = array(
                          'http'=> array(
                              'method' => "GET",
                              'header' => "Accept: application/vnd.github.v3+json\r\n" . 
                                          "User-Agent: GithubRepoExtractor/1.0\r\n" .
                                          "Authorization: Basic " . $credentials . "\r\n"
                          )
                      );
    }
    
    /**
     * Returns 'sha' references of commits, where first entry is the earliest commit.
     *
     * @return string[]
     */
    function getCommits() {
        $commitsUrl = $this->apiUrl . "/repos/" . $this->ownerName . "/" . $this->repoName . "/commits";
        $context = stream_context_create($this->opts);
        $jsonStr = @file_get_contents($commitsUrl . "?per_page=1", false, $context);
        $http_response_code = explode(' ', $http_response_header[0])[1];

        if ($http_response_code != 200) {
            throw new \Exception('Could not get commit, ' . $http_response_header[0]);
        }

        $json = json_decode($jsonStr);

        if ($json == null) {
            throw new \Exception('Could not retrieve commits.');
        }

        $links = null;
        for ($i = 0; $i < count($http_response_header); $i++) {
            //echo $http_response_header[$i] . PHP_EOL
            //     . strpos('Link', $http_response_header[$i]) . PHP_EOL;
            if (strpos($http_response_header[$i], 'api.github') != false) {
                $links = explode(',', $http_response_header[$i]);
            }
        }

        if ($links == null) {
            return array($json[0]->sha);
        }

        preg_match('/[&|?]page=(.*?)>/', $links[1], $matches);
        $numOfCommits = $matches[1];
        
        $commits = array();
        while ($numOfCommits > 0) {
            if ($numOfCommits < 100) {
                $jsonStr = @file_get_contents($commitsUrl . "?per_page=" . $numOfCommits, false, $context);
            } else {
                $jsonStr = @file_get_contents($commitsUrl . "?per_page=100", false, $context);
            }

            $http_response_code = explode(' ', $http_response_header[0])[1];

            if ($http_response_code != 200) {
                throw new \Exception('Could not complete request, ' . $http_response_header[0]);
            }

            $json = json_decode($jsonStr);
            $commits = array_merge($commits, $json);

            $numOfCommits -= 100;
        }
        
        $commitShas = array_reverse(array_map(function($o) { return $o->sha; }, $commits));
        return $commitShas;
    }
    
    /**
     * Gets contents at file at specified commit (defaults to current commit if 
     * unspecified).
     *
     * @return string
     */
    function getFileAtCommit($fileName, $commit=null) {
        $fileUrl = $this->apiUrl . '/repos/' . $this->ownerName . '/' . $this->repoName . 
                   '/contents/' . $fileName;

        if ($commit != null) {
            $fileUrl .= "?ref=" . $commit;
        }

        $context = stream_context_create($this->opts);
        $jsonStr = @file_get_contents($fileUrl, false, $context);
        $http_response_code = explode(' ', $http_response_header[0])[1];

        if ($http_response_code != 200) {
            throw new \Exception('Could not get file, ' . $http_response_header[0]);
        }

        $json = json_decode($jsonStr);

        if ($json == null) {
            throw new \Exception('Could not extract data.');
        }

        // Invalid file name is either a vaid path to a folder or an invalid path.
        // Valid folder path returns an array of objects, invalid path returns null on content.
        if (is_array($json) || $json->content == null) {
            throw new \Exception('File unavailable in this commit.');
        }

        $fileContent = base64_decode($json->content);

        return $fileContent;
    }
}
