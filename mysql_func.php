<?php
function add_post($link, $user_id, $content) {
	$tstamp = date('Y-m-d G:i:s');
	$sqlcmd = "INSERT INTO posts(user_id, content, time_stamp)
				VALUES($user_id, '".mysql_real_escape_string($content). "', $tstamp";
	$result = mysqli_query($link, $sqlcmd);
}

function add_user($link, $first, $last, $uname, $pass, $email, $status) {
	$sqlcmd = "INSERT INTO users (firstname, lastname, email, username, password) 
				VALUES ('$first', '$last', '$email', '$uname', '$pass')";

	$result = mysqli_query($link, $sqlcmd);

	if(mysqli_errno($link) == $MYSQL_DUPLICATE_KEY) {
		$_SESSION['regerr'] = "An account with this email address already exists";
		header("Location: registration.php");
	} else {
		header("Location: index.php");
	}
}

// $user_id is an array of users to pull posts from
function show_posts($link, $user_id, $limit=0) {
	$posts = array();

	$users = implode(',', $user_id);
	$sqlext = " AND user_id in ($users)";

	if ($limit > 0) {
		$sqlext = "LIMIT $limit";
	} else {
		$sqlext = "";
	}

	$sqlcmd = "SELECT u.username, p.content, p.time_stamp
				FROM posts as p, users as u
				WHERE u.user_id = p.user_id and p.user_id IN ($users)
				ORDER BY time_stamp DESC $sqlext";

	$result = mysqli_query($link, $sqlcmd);

	while($data = mysqli_fetch_object($result)) {
		$posts[] = ['time_stamp' => $data->time_stamp,
					'user_id' => $user_id,
					'username' => $data->username,
					'content' => $data->content];
	}

	return $posts;
}

function show_users($link, $user_id=0) {
	$sqlext = "";

	if($user_id > 0) {
		$followers = array();
		$sqlcmd = "SELECT user_id
					FROM following
					WHERE follower_id='$user_id'";
		
		$result = mysqli_query($link, $sqlcmd);

		while($data = mysqli_fetch_object($result)) {
			array_push($followers, $data->user_id);
		}

		if(count($followers)) {
			$ids = implode(',', $followers);
			$sqlext = " AND user_id IN ($ids)";
		} else {
			return array();
		}
	}

	$users = array();
	$sqlcmd = "SELECT user_id, username, email 
				FROM users 
				WHERE status='active' $sqlext
				ORDER BY username";

	$result = mysqli_query($link, $sqlcmd);

	while($data = mysqli_fetch_object($result)) {
		$users[] = ['user_id' => $data->user_id,
					'username' => $data->username,
				    'email' => $data->email];
	}

	return $users;
}

// Search term may be all or part of a username or email
function search_users($link, $term) {
	$found = array();
	if(!$term == "")
	{
		$users = show_users($link);
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

function following($link, $user_id) {
	$users = array();

	$sqlcmd = "SELECT DISTINCT user_id
				FROM following
				WHERE follower_id = '$user_id'";

	$result = mysqli_query($link, $sqlcmd);

	while($data = mysqli_fetch_object($result)) {
		array_push($users, $data->user_id);
	}

	return $users;
}

function check_follow_count($link, $follower, $followed) {
	$sqlcmd = "SELECT count(*)
				FROM following
				WHERE user_id='$followed' AND follower_id='$follower'";
	$result = mysqli_query($link, $sqlcmd);

	$row = mysqli_fetch_row($result);

	return $row[0];
}

function follow_user($link, $follower, $followed) {
	$count = check_follow_count($follower, $followed);

	if($count == 0) {
		$sqlcmd = "INSERT INTO following (user_id, follower_id)
					VALUES ($followed, $follower)";

		$result = mysqli_query($link, $sqlcmd);
	}
}

function unfollow_user($link, $follower, $followed) {
	$count = check_follow_count($follower, $followed);

	if($count == 0) {
		$sqlcmd = "DELETE FROM following
					WHERE user_id='$followed' AND follower_id='$follower'
					LIMIT 1";

		$result = mysqli_query($link, $sqlcmd);
	}
}
?>