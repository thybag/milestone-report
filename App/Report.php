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
		// Get data
		$milestones = $this->getMilestones();

		// Url options
		if (isset($_GET['milestone'])) {
			$milestone = $_GET['milestone'];
		} else {
			// Get latest
			$milestone = $milestones[0]['number'];
		}
		
		// Get the issues
		$issues = $this->getIssues($milestone);

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
			'milestones'=> $milestones,
			'data' => [
				'top_submit' => IssueStats::topSumbitters($issues),
				'top_assigned' => IssueStats::topAssigned($issues),
				'top_labeled' => IssueStats::topLabels($issues),
				'allIssues' => $issues,
				'groups' => $groups,
				'milestone' => $issues[0]['milestone']
			]
		]);
	}

	/**
	 * Get milestones
	 * 
	 * @param  [type] $auth [description]
	 * @return [type]       [description]
	 */
	protected function getMilestones()
	{
		return Cache::get("milestones", function() {
			$grabber = new GithubIssueReader($this->config);
			$milestones = $grabber->getMilestones();

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
	 * Get issues for milestone
	 * 
	 * @param  [type] $milestone [description]
	 * @param  [type] $auth      [description]
	 * @return [type]            [description]
	 */
	protected function getIssues($milestone)
	{
		return Cache::get("issues.milestione-$milestone", function() use ($milestone) {
			$grabber = new GithubIssueReader($this->config);
			return $grabber->getAllMilestoneIssues($milestone);
		}, 100);
	}
}