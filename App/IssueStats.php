<?php
namespace App;
/**
 * Quick view wrapper
 */
class IssueStats 
{
	//assignees,user,labels
	public static function topSumbitters($issues)
	{
		$results = [];
		foreach($issues as $issue){
			$login = $issue['user']['login'];
			if (isset($results[$login])) {
			 	$results[$login]++;
			} else {
				$results[$login] = 1;
			}
		}

		arsort($results);
		return $results;
	}

	public static function topAssigned($issues)
	{
		$results = [];
		foreach($issues as $issue){
			foreach($issue['assignees'] as $assignee){
				$login = $assignee['login'];
				if(isset($results[$login])) {
				 	$results[$login]++;
				} else {
					$results[$login] = 1;
				}
			}
		}

		arsort($results);
		return $results;
	}

	public static function topLabels($issues)
	{
		$results = [];
		foreach($issues as $issue){
			foreach($issue['labels'] as $assignee){
				$login = $assignee['name'];
				if(isset($results[$login])) {
				 	$results[$login]++;
				} else {
					$results[$login] = 1;
				}
			}
		}

		arsort($results);
		return $results;
	}
}