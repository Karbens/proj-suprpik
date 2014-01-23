<?php
	include_once('session.php');

	$contest_id = isset($_REQUEST['contest_id'])? (int)$_REQUEST['contest_id'] : 0;

	$contests = get_contests($contest_id);

	if($contest_id>0){
		$contest = $contests[0];

		$templates = get_templates();

		if(class_exists($contest['template'])){

			$contestObject = new $contest['template']($contest);

		} else {
			exit();

		}
		if(isset($_POST['action'])){
			$success = 'false';
			$pass_id = 0;
			$content = '';


			if($_POST['action']=='update_contest' && isset($_POST['contest'])){

				if($contestObject->update_contest($_POST['contest'])){						
					$success  = 'true';
				}

			}

			if($_POST['action']=='add_event'){

				if($event_id = $contestObject->add_event()){						
					$success  = 'true';
				}

			}

			if(isset($_POST['event_id'])){

				if($_POST['action']=='delete_event'){
					if($contestObject->delete_event($_POST['event_id'])){
						$success  = 'true';
						$pass_id = $_POST['event_id'];
					}
				}

				if($_POST['action']=='add_choice'){

					if($choice_id = $contestObject->add_event_choice($_POST['event_id'])){

						$choices = $contestObject->getChoices($_POST['event_id']);
						$choice_count = count($choices);

						$content = '<tr id="chRow_'.$choice_id.'"><td width="80">Choice '.$choice_count.':</td><td width="350"><input type="text" class="required choiceInput" value="" name="choice['.$choice_id.']" required="required" ></td><td nowrap><a href="#eventname_'.$_POST['event_id'].'" class="button_minus" data-event_id="'.$_POST['event_id'].'" data-choice_id="'.$choice_id.'">&nbsp;</a></td></tr>';
							
						$success  = 'true';
						$pass_id = $_POST['event_id']; //pass event id to append to
					}

				}

				if(isset($_POST['choice_id'])){
					if($_POST['action']=='delete_choice'){
						if($contestObject->delete_event_choice($_POST['event_id'], $_POST['choice_id'])){
							$success  = 'true';
							$pass_id = $_POST['choice_id'];
						}
					}
				}

			}

		}

		header('Content-Type: application/json');

		echo json_encode(array('success' => $success, 'pass_id' => $pass_id, 'content' => $content));


	} 