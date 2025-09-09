<div class="text-stuff">
	<label>Total Guests: <?= $total_guests ?></label>
	<div class="info">
		<div class="entries-header entry">
			<p>Username</p>
			<p>IP</p>
			<p>Country</p>
			<p>Enrolled</p>
			<p>Registered</p>
		</div>
		<?php
			foreach ($all_data as $guest) {
				$user_meta = get_user_meta($guest->assigned_id);
				$enrolments = json_decode($user_meta['wp9r_risksensei_enrolment_providers_state'][0], true); 
				$enrolled_courses = [];
				foreach($enrolments as $course_id => $details){
					if($details['manual']['enrolment_status']){
						$course_num = explode(' ', get_the_title($course_id))[1];
						$enrolled_courses[] = $course_num;
					}
				}

				?>
				<div class="entry">
					<p><?= $guest->username ?></p>
					<p><?= $guest->ipv4_v6_address ?></p>
					<p><?= $guest->country ?></p>
					<p><?= implode('-', array_unique($enrolled_courses)) ?> </p>
					<p><?= $guest->ts ?></p>
				</div>
			<?php
			} ?>
	</div>
</div>
<style>
	.text-stuff{
		display: flex;
		flex-direction: column;
	}

	.text-stuff .info{
		display: flex;
		flex-direction: column;
	}

	.text-stuff .info .entry{
		text-align: center;
		display: flex;
		flex-direction: row;
		gap: 10px;
	}

	.text-stuff .entry p{
		flex: 1;
	}
	.
</style>