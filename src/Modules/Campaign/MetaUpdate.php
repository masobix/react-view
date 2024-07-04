<?php

namespace FRZR\Modules\Campaign;

use FRZR\Helper\Utils;

class MetaUpdate
{

	private $post_id;

	public function set_id($post_id): self
	{
		$this->post_id = $post_id;
		return $this;
	}

	public function funding($amount): self
	{
		$raised = (int) get_post_meta($this->post_id, 'raised', true);
		$total = $raised + intval($amount);

		update_post_meta($this->post_id, 'raised', $total);

		return $this;
	}

	public function refresh(): self
	{

		$this->raised();
		$this->goal();
		$this->progress();
		$this->deadline();

		return $this;
	}

	public function raised(): self
	{
		$amount = (int) get_post_meta($this->post_id, 'raised', true);

		$display = Utils::currency_format($amount);
		update_post_meta($this->post_id, 'echo_raised', $display);

		return $this;
	}

	public function goal(): self
	{
		$amount = (int) get_post_meta($this->post_id, 'goal', true);
		$display = Utils::currency_format($amount);
		update_post_meta($this->post_id, 'echo_goal', $display);

		return $this;
	}

	public function progress(): self
	{
		$raised = (int) get_post_meta($this->post_id, 'raised', true);
		$goal = (int) get_post_meta($this->post_id, 'goal', true);

		if ($goal === 0) {
			update_post_meta($this->post_id, 'echo_progress', 0);
		} else {
			$calculated = round($raised / $goal * 100, 2);
			update_post_meta($this->post_id, 'echo_progress', $calculated);
		}

		return $this;
	}

	public function deadline(): self
	{
		$deadline = get_post_meta($this->post_id, 'deadline', true);

		if (empty($deadline)) {
			update_post_meta($this->post_id, 'echo_deadline', "∞");
		} else {
			$time = human_time_diff(time(), strtotime($deadline));
			update_post_meta($this->post_id, 'echo_deadline', $time);
		}

		return $this;
	}
}
