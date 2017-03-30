<?php

use GraphAware\Neo4j\Client\Exception\Neo4jException;

function add_post($client, $email, $content) {
	$tstamp = date('Y-m-d G:i:s');
	$query = "
	MATCH (u:User)
	WHERE u.email = '$email'
	CREATE (
		p:Post
		{
			content: \"$content\",
			timestamp: '$tstamp'
		}
	)
	CREATE (u)-[:POSTED]->(p)";

	try {
		$results = $client->run($query);
	} catch(Neo4jException $e) {
		$_SESSION['posterr'] = "Unable to post content with message ". $e->getMessage();
	}
}

function add_user($client, $first, $last, $uname, $pass, $email, $status) {
	$query = "CREATE (
		u:User
		{
			first: '$first',
			last: '$last',
			uname: '$uname',
			pass: '$pass',
			email: '$email',
			status: '$status'
		}
	)";

	try {
		$results = $client->run($query);
		header("Location: index.php");
	} catch(Neo4jException $e) {
		$errcode = $e->getCode();
		echo $errcode;
		if($errcode == 0) {
			$_SESSION['regerr'] = "An account with this email address already exists.";
			header("Location: registration.php");
		} else {
			$_SESSION['regerr'] = "A database error prevented this account from being created.";
			header("Location: registration.php");
		}
	}
}

function login($client, $email, $pass) {

	$query = "MATCH (u:User) 
			  WHERE u.email='$email' AND u.pass='$pass'
			  RETURN u.email as email, u.uname as username";

	try {
		$results = $client->run($query);
		$records = $results->getRecords();

		if(count($records) != 1) {
			$_SESSION['autherr'] = "Your email address or password is incorrect!";
			header("Location: index.php");
		} else {
			$_SESSION['email'] = $records[0]->value('email');
			$_SESSION['username'] = $records[0]->value('username');
			header("Location: home.php");
		}

	} catch(Neo4jException $e) {
		$_SESSION['autherr'] = "Failed to verify your credentials.  Please try again later.";
		header("Location: index.php");
	}
}


// $user_id is an array of users to pull posts from
function show_posts($client, $emails, $limit=0) {
	$posts = array();

	$users = implode('\',\'', $emails);
	$sqlext = "";

	if ($limit > 0) {
		$sqlext = "LIMIT $limit";
	} else {
		$sqlext = "";
	}

	$query = "MATCH (u:User)-[:POSTED]->(p:Post)
			  WHERE u.email IN ['$users']
			  RETURN u.uname as username, u.email as email, p.content as content, p.timestamp as timestamp
			  ORDER BY timestamp DESC $sqlext";

	try {
		$results = $client->run($query);

		foreach($results->getRecords() as $record) {
			$posts[] = ['timestamp' => $record->value('timestamp'),
						'email' => $record->value('email'),
						'username' => $record->value('username'),
						'content' => $record->value('content')];
		}
	} catch(Neo4jException $e) {
		$err = $e->getMessage();
		$_SESSION['dberr'] = "Post retrieval failed with message ".$err;
	}

	return $posts;
}


function show_users($client, $email="") {
	$sqlext = "";

	if($email != "") {
		$followers = array();
		$query = "MATCH (follower:User)-[:FOLLOWS]->(followed:User)
				  WHERE follower.email='$email'
				  RETURN followed.email as email";
		
		try {
			$results = $client->run($query);

			foreach($results->getRecords() as $record) {
				array_push($followers, $record->value('email'));
			}
		} catch(Neo4jException $e) {
			$err = $e->getMessage();
			$_SESSION['dberr'] = "User retrieval failed with message ".$err;
		}


		if(count($followers)) {
			$emails = implode(',', $followers);
			$sqlext = " AND u.email IN ['$emails']";
		} else {
			return array();
		}
	}

	$users = array();

	$query = "MATCH (u:User)
			  WHERE u.status='active' $sqlext
			  RETURN u.uname as username, u.email as email
			  ORDER BY username";


	try {
		$results = $client->run($query);

		foreach($results->getRecords() as $record) {
			$users[] = ['username' => $record->value('username'),
						'email' => $record->value('email')];
		}
	} catch(Neo4jException $e) {
		$err = $e->getMessage();
		$_SESSION['dberr'] = "User retrieval failed with message ".$err;
	}

	return $users;
}

// Search term may be all or part of a username or email
function search_users($client, $term) {
	$found = array();
	if(!$term == "")
	{
		$users = show_users($client);
		$lowerterm = strtolower($term);

		foreach($users as $key => $user) {
			$keys = array_keys($user);
			$username = $user[$keys[0]];
			$email = $user[$keys[1]];
			
			if(strpos(strtolower($username), strtolower($term)) !== false || 
			   strpos(strtolower($email), strtolower($term)) !== false) {
				
				$found[] = $user;
			}
		}
		if(count($found) == 0)
		{
			$_SESSION['searcherr'] = "We were unable to find any users that match that name!";
		}
	} else {
		$_SESSION['searcherr'] = "Please enter a search string!";
	}

	return $found;
}

function following($client, $email) {
	$users = array();

	$query = "MATCH (u:User)-[:FOLLOWS]->(p:User)
			  WHERE u.email='$email'
			  RETURN DISTINCT p.email as email";

	try {
		$results = $client->run($query);

		foreach($results->getRecords() as $record) {
			array_push($users, $record->value('email'));
		}
	} catch(Neo4jException $e) {
		$err = $e->getMessage();
		$_SESSION['dberr'] = "User retrieval failed with message ".$err;
	}

	return $users;
}

function check_follow_count($client, $follower, $followed) {
	$query = "MATCH (follower:User)-[:FOLLOWS]->(followed:User)
			  RETURN COUNT(follower) as followers";

	$sqlcmd = "SELECT count(*)
				FROM following
				WHERE user_id='$followed' AND follower_id='$follower'";
	$result = mysqli_query($client, $sqlcmd);

	$row = mysqli_fetch_row($result);

	return $row[0];
}

function follow_user($client, $follower, $followed) {

	$query = "MATCH (follower:User {email: '$follower'}), (followed:User {email: '$followed'})
			  CREATE (follower)-[f:FOLLOWS]->(followed)";

	try {
		$results = $client->run($query);
	} catch(Neo4jException $e) {
		$err = $e->getMessage();
		$_SESSION['dberr'] = "Failed to follow user with message ".$err;
	}
}

function unfollow_user($client, $follower, $followed) {

	$query = "MATCH (follower:User {email: '$follower'})-[f:FOLLOWS]->(followed:User {email: '$followed'})
			  DELETE f";

	try {
		$results = $client->run($query);
	} catch(Neo4jException $e) {
		$err = $e->getMessage();
		$_SESSION['dberr'] = "Failed to unfollow user with message ".$err;
	}
}
?>