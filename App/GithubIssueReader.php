<?php
namespace App;

use GuzzleHttp\Client;

class GithubIssueReader 
{
	protected $settings = [];
	protected $client = null;

	public function __construct($settings){
		$this->settings = $settings;
		$this->client = new \GuzzleHttp\Client(['verify' => false]);	
	}

	public function getMilestoneIssues($milestone, $page = 1)
	{
		return $this->query("/issues?milestone={$milestone}&page={$page}&state=all");
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

	public function getAllMilestoneIssues($milestone) {
		$page = 1;
		$still_more = true;
		$data = [];
		while($still_more) {
			$new = $this->getMilestoneIssues($milestone, $page);
			if (sizeof($new) < 30) {
				$still_more = false;
			}
			$data = array_merge($data, $new);
			$page++;
		}
		return $data;
	}

	public function getMilestones()
	{
		return $this->getAllResults("/milestones?state=all");
	}

	protected function query($url)
	{
		$res = $this->client->request('GET', $this->getRepoUrl().$url, [
			"headers" => [
				"Accept" => "application/vnd.github.v3+json",
				"User-Agent" => "GH stats output",
				"Authorization" => "token ".$this->settings['auth_token']
			]
		]);
		return json_decode($res->getBody(), true);
	}

	protected function getRepoUrl()
	{
		return "https://api.github.com/repos/{$this->settings['owner']}/{$this->settings['repo']}";
	}
}