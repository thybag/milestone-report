<?php
namespace App;

use GuzzleHttp\Client;

class GithubIssueReader 
{
	protected $settings = [];
	protected $client = null;

	public function __construct($settings)
	{
		$this->settings = $settings;
		$this->client = new \GuzzleHttp\Client(['verify' => false]);	
	}

	public function getMilestoneIssues($repo, $milestone)
	{
		return $this->getAllResults("/{$repo}/issues?milestone={$milestone}&state=all");
	}

	public function getMilestones($repo)
	{
		return $this->getAllResults("/{$repo}/milestones?state=all");
	}

	public function getRepos()
	{
		return $this->query("https://api.github.com/user/repos", false);
	}

	protected function getAllResults($query)
	{
		$page = 1;
		$still_more = true;
		$data = [];
		while($still_more) {
			$new = $this->query($query . '&page=' . $page);
			if (sizeof($new) < 30) {
				$still_more = false;
			}
			$data = array_merge($data, $new);
			$page++;
		}
		return $data;
	}

	protected function query($url, $prefix = true)
	{
		$url = $prefix ? $this->getRepoUrl().$url : $url;

		$res = $this->client->request('GET', $url, [
			"headers" => [
				"Accept" => "application/vnd.github.v3+json",
				"User-Agent" => "GH stats output",
				"Authorization" => "token ".$this->settings['auth_token']
			]
		]);
		//die(json_decode($res->getBody(), true));
		return json_decode($res->getBody(), true);
	}

	protected function getRepoUrl()
	{
		return "https://api.github.com/repos/{$this->settings['owner']}";
	}
}