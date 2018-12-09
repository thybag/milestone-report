<?php
namespace App;

use App\View;
use App\IssueStats;
use App\GithubIssueReader;
use thybag\PhpSimpleCache\StaticCache as Cache;

/**
 * Quick view wrapper
 */
class Report 
{
	protected $config = [];
	protected $repos;
	protected $milestones;
		
	/**
	 * Setup auth
	 * 
	 * @param [type] $config [description]
	 */
	public function __construct($config)
	{
		$this->config = $config;
	}

	/**
	 * Render the report
	 * 
	 * @return html string
	 */
	public function run()
	{
		Cache::fresh(['allow_cache_bypass' => true]);

		if (!$this->config) {
			return $this->error("Config file missing", 'unknown');
		}

		// Get data
		$this->repos = $this->getRepos();
		if (sizeof($this->repos) == 0 || $this->repos === false) {
			return $this->error("Unable to load repos - please check your github token is correct.", 'unknown');
		}

		// Url options
		if (isset($_GET['repo'])) {
			$repo = $_GET['repo'];
		} else {
			// Get latest (or use default)
			$repo = isset($this->config['repo']) ? $this->config['repo'] : $this->repos[0]['name'];
		}

		$this->milestones = $this->getMilestones($repo);
		if (sizeof($this->milestones) == 0){
			return $this->error("This repo has no milestones.", $repo);
		}

		// Url options
		if (isset($_GET['milestone'])) {
			$milestone = $_GET['milestone'];
		} else {
			// Get latest
			$milestone = $this->milestones[0]['number'];
		}
		
		// Get the issues
		$issues = $this->getIssues($repo, $milestone);

		if (!$issues || sizeof($issues) == 0){
			return $this->error("Milestone empty or not found", $repo);
		}

		// seperate em out
		$groups = ['Complete' => [], 'Pending'=> []];
		foreach ($issues as $issue)
		{
			if($issue['state'] == "open"){
				$groups['Pending'][] = $issue;
			}else if($issue['state'] == "closed"){
				$groups['Complete'][] = $issue;
			}
		}

		// Render it all
		return new View('views/wrapper', [
			'content' => 'main',
			'repos' => $this->repos ,
			'repo_name' => $repo,
			'milestone_id' => $milestone,
			'milestones'=> $this->milestones,
			'data' => [
				'top_submit' => IssueStats::topSumbitters($issues),
				'top_assigned' => IssueStats::topAssigned($issues),
				'top_labeled' => IssueStats::topLabels($issues),
				'allIssues' => $issues,
				'groups' => $groups,
				'milestone' => $issues[0]['milestone'],
				'debug' => (isset($this->config['debug']) && $this->config['debug']===true)
			]
		]);
	}

	/**
	 * Show error if unable to load
	 * 
	 * @param  [type] $message [description]
	 * @param  [type] $repo    [description]
	 * @return [type]          [description]
	 */
	public function error($message, $repo)
	{
		// Render it all
		return new View('views/wrapper', [
			'content' => 'error',
			'repo_name' => $repo,
			'milestone_id' => null,
			'repos' => is_array($this->repos) ? $this->repos : [],
			'milestones'=> is_array($this->milestones) ? $this->milestones : [],
			'data' => ['message' => $message] 
		]);
	}

	/**
	 * Get milestones
	 * 
	 * @param  [type] $auth [description]
	 * @return [type]       [description]
	 */
	protected function getMilestones($repo)
	{
		return Cache::get("repo-{$repo}.milestones", function() use($repo) {
			$grabber = new GithubIssueReader($this->config);
			$milestones = $grabber->getMilestones($repo);

			usort($milestones,function($a, $b){
				if($a['state'] == 'open' && $b['state'] == 'closed'){
					return -1;;
				}
				if($b['state'] == 'open' && $a['state'] == 'closed'){
					return 1;
				}
				// Fallback to dates
				return ($a['created_at'] < $b['created_at']) ? 1 : 0;
			});

			return $milestones;
		}, 500);
	}

	/**
	 * Get repos
	 * 
	 * @param  [type] $auth [description]
	 * @return [type]       [description]
	 */
	protected function getRepos()
	{
		return Cache::get("repos", function() {
			$grabber = new GithubIssueReader($this->config);	
			return $grabber->getRepos();
		}, 500);
	}
	
	/**
	 * Get issues for milestone
	 * 
	 * @param  [type] $milestone [description]
	 * @param  [type] $auth      [description]
	 * @return [type]            [description]
	 */
	protected function getIssues($repo, $milestone)
	{
		return Cache::get("repo-{$repo}.issues.milestione-$milestone", function() use ($repo, $milestone) {
			$grabber = new GithubIssueReader($this->config);
			return $grabber->getMilestoneIssues($repo, $milestone);
		}, 100);
	}
}