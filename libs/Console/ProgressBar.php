<?php
// Copyright (c) 2007 Stefan Walk
//
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to
// deal in the Software without restriction, including without limitation the
// rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
// sell copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
//
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
// FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
// IN THE SOFTWARE.

/**
 * Class to display a progressbar in the console.
 *
 * @package Console_ProgressBar
 * @category Console
 * @version 0.5.2
 * @author Stefan Walk <et@php.net>
 * @author Ondřej Nešpor <andrew@andrewsville.cz>
 * @license MIT License
 */
class Console_ProgressBar
{

	/**
	 * Skeleton for use with sprintf.
	 *
	 * @var string
	 */
	private $skeleton;

	/**
	 * The bar gets filled with this.
	 *
	 * @var string
	 */
	private $bar;

	/**
	 * The width of the bar.
	 *
	 * @var integer
	 */
	private $blen;

	/**
	 * The total width of the display.
	 *
	 * @var integer
	 */
	private $tlen;

	/**
	 * The position of the counter when the job is "done".
	 *
	 * @var integer
	 */
	private $target_num;

	/**
	 * Options, like the precision used to display the numbers.
	 *
	 * @var array
	 */
	private $options = array();

	/**
	 * Length to erase.
	 *
	 * @var integer
	 */
	private $rlen = 0;

	/**
	 * When the progress started.
	 *
	 * @var float
	 */
	private $start_time = null;

	/**
	 * Rate datapoints.
	 *
	 * @var array
	 */
	private $rate_datapoints = array();

	/**
	 * Time when the bar was last drawn
	 *
	 * @var float
	 */
	private $last_update_time = 0.0;

	/**
	 * Constructor, sets format and size.
	 *
	 * See the reset() method for documentation.
	 *
	 * @param string The format string
	 * @param string The string filling the progress bar
	 * @param string The string filling empty space in the bar
	 * @param integer The width of the display
	 * @param float The target number for the bar
	 * @param array Options for the progress bar
	 * @see reset()
	 */
	public function __construct($formatstring, $bar, $prefill, $width, $target_num, $options = array())
	{
		$this->reset($formatstring, $bar, $prefill, $width, $target_num, $options);
	}

	/**
	 * Re-sets format and size.
	 *
	 * <pre>
	 * The reset method expects 5 to 6 arguments:
	 * - The first argument is the format string used to display the progress
	 *   bar. It may (and should) contain placeholders that the class will
	 *   replace with information like the progress bar itself, the progress in
	 *   percent, and so on. Current placeholders are:
	 *     %bar%         The progress bar
	 *     %current%     The current value
	 *     %max%         The maximum malue (the "target" value)
	 *     %fraction%    The same as %current%/%max%
	 *     %percent%     The status in percent
	 *     %elapsed%     The elapsed time
	 *     %estimate%    An estimate of how long the progress will take
	 *   More placeholders will follow. A format string like:
	 *   "* stuff.tar %fraction% KB [%bar%] %percent%"
	 *   will lead to a bar looking like this:
	 *   "* stuff.tar 391/900 KB [=====>---------]  43.44%"
	 * - The second argument is the string that is going to fill the progress
	 *   bar. In the above example, the string "=>" was used. If the string you
	 *   pass is too short (like "=>" in this example), the leftmost character
	 *   is used to pad it to the needed size. If the string you pass is too long,
	 *   excessive characters are stripped from the left.
	 * - The third argument is the string that fills the "empty" space in the
	 *   progress bar. In the above example, that would be "-". If the string
	 *   you pass is too short (like "-" in this example), the rightmost
	 *   character is used to pad it to the needed size. If the string you pass
	 *   is too short, excessive characters are stripped from the right.
	 * - The fourth argument specifies the width of the display. If the options
	 *   are left untouched, it will tell how many characters the display should
	 *   use in total. If the "absolute_width" option is set to false, it tells
	 *   how many characters the actual bar (that replaces the %bar%
	 *   placeholder) should use.
	 * - The fifth argument is the target number of the progress bar. For
	 *   example, if you wanted to display a progress bar for a download of a
	 *   file that is 115 KB big, you would pass 115 here.
	 * - The sixth argument optional. If passed, it should contain an array of
	 *   options. For example, passing array('absolute_width' => false) would
	 *   set the absolute_width option to false. Current options are:
	 *
	 *     option             | def.  |  meaning
	 *     --------------------------------------------------------------------
	 *     percent_precision  | 2     |  Number of decimal places to show when
	 *                        |       |  displaying the percentage.
	 *     fraction_precision | 0     |  Number of decimal places to show when
	 *                        |       |  displaying the current or target
	 *                        |       |  number.
	 *     percent_pad        | ' '   |  Character to use when padding the
	 *                        |       |  percentage to a fixed size. Senseful
	 *                        |       |  values are ' ' and '0', but any are
	 *                        |       |  possible.
	 *     fraction_pad       | ' '   |  Character to use when padding max and
	 *                        |       |  current number to a fixed size.
	 *                        |       |  Senseful values are ' ' and '0', but
	 *                        |       |  any are possible.
	 *     width_absolute     | true  |  If the width passed as an argument
	 *                        |       |  should mean the total size (true) or
	 *                        |       |  the width of the bar alone.
	 *     ansi_terminal      | false |  If this option is true, a better
	 *                        |       |  (faster) method for erasing the bar is
	 *                        |       |  used. CAUTION - this is known to cause
	 *                        |       |  problems with some terminal emulators,
	 *                        |       |  for example Eterm.
	 *     ansi_clear         | false |  If the bar should be cleared everytime
	 *     num_datapoints     | 5     |  How many datapoints to use to create
	 *                        |       |  the estimated remaining time
	 *     min_draw_interval  | 0.0   |  If the last call to update() was less
	 *                        |       |  than this amount of seconds ago,
	 *                        |       |  don't update.
	 * </pre>
	 *
	 * @param string The format string
	 * @param string The string filling the progress bar
	 * @param string The string filling empty space in the bar
	 * @param integer The width of the display
	 * @param float The target number for the bar
	 * @param array Options for the progress bar
	 * @return boolean
	 */
	public function reset($formatstring, $bar, $prefill, $width, $target_num, $options = array())
	{
		if ($target_num == 0) {
			throw new Exception("Console_ProgressBar: Using a target number equal to 0 is invalid, setting to 1 instead");
			$this->target_num = 1;
		} else {
			$this->target_num = $target_num;
		}
		$default_options = array(
			'percent_precision' => 2,
			'fraction_precision' => 0,
			'percent_pad' => ' ',
			'fraction_pad' => ' ',
			'width_absolute' => true,
			'ansi_terminal' => false,
			'ansi_clear' => false,
			'num_datapoints' => 5,
			'min_draw_interval' => 0.0,
		);
		$intopts = array();
		foreach ($default_options as $key => $value) {
			if (!isset($options[$key])) {
				$intopts[$key] = $value;
			} else {
				settype($options[$key], gettype($value));
				$intopts[$key] = $options[$key];
			}
		}
		$this->options = $options = $intopts;
		// placeholder
		$cur = '%2$\'' . $options['fraction_pad']{0} . strlen((int)$target_num) . '.' . $options['fraction_precision'] . 'f';
		$max = $cur; $max{1} = 3;
		$padding = 4 + $options['percent_precision'];
		$perc = '%4$\'' . $options['percent_pad']{0} . $padding . '.' . $options['percent_precision'] . 'f';

		$transitions = array(
			'%%' => '%%',
			'%fraction%' => $cur.'/'.$max,
			'%current%' => $cur,
			'%max%' => $max,
			'%percent%' => $perc.'%%',
			'%bar%' => '%1$s',
			'%elapsed%' => '%5$s',
			'%estimate%' => '%6$s',
		);

		$this->skeleton = strtr($formatstring, $transitions);

		$slen = strlen(sprintf($this->skeleton, '', 0, 0, 0, '00:00:00','00:00:00'));

		if ($options['width_absolute']) {
			$blen = $width - $slen;
			$tlen = $width;
		} else {
			$tlen = $width + $slen;
			$blen = $width;
		}

		$lbar = str_pad($bar, $blen, $bar{0}, STR_PAD_LEFT);
		$rbar = str_pad($prefill, $blen, substr($prefill, -1, 1));

		$this->bar = substr($lbar, -$blen) . substr($rbar, 0, $blen);
		$this->blen = $blen;
		$this->tlen = $tlen;
		$this->first = true;

		return true;
	}

	/**
	 * Updates the bar with new progress information.
	 *
	 * @param integer Current position of the progress counter
	 * @return boolean
	 */
	public function update($current)
	{
		$time = microtime(true);
		$this->addDatapoint($current, $time);
		if ($this->first) {
			if ($this->options['ansi_terminal']) {
				echo "\x1b[s"; // save cursor position
			}
			$this->first = false;
			$this->start_time = microtime(true);
			$this->display($current);
			return;
		}
		if (($time - $this->last_update_time < $this->options['min_draw_interval'])
			&& ($current != $this->target_num)) {
			return;
		}
		$this->erase();
		$this->display($current);
		$this->last_update_time = $time;
	}

	/**
	 * Returns the current progress.
	 *
	 * @return integer
	 */
	public function getProgress()
	{
		if (empty($this->rate_datapoints)) {
			return 0;
		}

		$progress = end($this->rate_datapoints);
		return $progress['value'];
	}

	/**
	 * Prints the bar. Usually, you don't need this method, just use update()
	 * which handles erasing the previously printed bar also. If you use a
	 * custom protected function (for whatever reason) to erase the bar, use this method.
	 *
	 * @param int Current position of the progress counter
	 * @return bool
	 */
	public function display($current)
	{
		$percent = $current / $this->target_num;
		$filled = round($percent * $this->blen);
		$visbar = substr($this->bar, $this->blen - $filled, $this->blen);
		$elapsed = $this->formatSeconds(microtime(true) - $this->start_time);
		$estimate = $this->formatSeconds($this->generateEstimate());
		$this->rlen = printf(
			$this->skeleton,
			$visbar,
			$current,
			$this->target_num,
			$percent * 100,
			$elapsed,
			$estimate
		);

		if ($current === $this->target_num) {
			echo "\n";
		}

		return true;
	}

	/**
	 * Erases a previously printed bar.
	 *
	 * @param boolean If the bar should be cleared in addition to resetting the cursor position
	 * @return bool
	 */
	public function erase($clear = false)
	{
		if ($this->options['ansi_terminal'] and !$clear) {
			if ($this->options['ansi_clear']) {
				echo "\x1b[2K\x1b[u"; // restore cursor position
			} else {
				echo "\x1b[u"; // restore cursor position
			}
		} elseif (!$clear) {
			echo str_repeat(chr(0x08), $this->rlen);
		} else {
			echo str_repeat(chr(0x08), $this->rlen) . str_repeat(chr(0x20), $this->rlen) . str_repeat(chr(0x08), $this->rlen);
		}
	}

	/**
	 * Returns a string containing the formatted number of seconds
	 *
	 * @param float The number of seconds
	 * @return string
	 */
	protected function formatSeconds($seconds)
	{
		$hou = floor($seconds/3600);
		$min = floor(($seconds - $hou * 3600) / 60);
		$sec = $seconds - $hou * 3600 - $min * 60;
		if ($hou == 0) {
			$format = '%2$02d:%3$05.2f';
		} elseif ($hou < 100) {
			$format = '%02d:%02d:%02d';
		} else {
			$format = '%05d:%02d';
		}
		return sprintf($format, $hou, $min, $sec);
	}

	/**
	 * Adds a datapoint.
	 *
	 * @param string $val Current value
	 * @param float $time Current time
	 */
	protected function addDatapoint($val, $time) {
		if (count($this->rate_datapoints) == $this->options['num_datapoints']) {
			array_shift($this->rate_datapoints);
		}
		$this->rate_datapoints[] = array(
			'time' => $time,
			'value' => $val,
		);
	}

	/**
	 * Generates estimated finish time.
	 *
	 * @return float
	 */
	protected function generateEstimate() {
		if (count($this->rate_datapoints) < 2) {
			return 0.0;
		}
		$first = $this->rate_datapoints[0];
		$last = end($this->rate_datapoints);
		return ($this->target_num - $last['value']) / ($last['value'] - $first['value']) * ($last['time'] - $first['time']);
	}
}
