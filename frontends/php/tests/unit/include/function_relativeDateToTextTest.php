<?php
/*
** Zabbix
** Copyright (C) 2001-2018 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/


class function_relativeDateToTextTest extends PHPUnit_Framework_TestCase {
	protected $tz;

	public function setUp() {
		$this->tz = date_default_timezone_get();
		date_default_timezone_set('Europe/Riga');
	}

	public function tearDown() {
		date_default_timezone_set($this->tz);
	}

	public static function provider() {
		return [
			['params' => ['now-1d/d', 'now-1d/d'],		'expected' => 'Yesterday'],
			['params' => ['now-2d/d', 'now-2d/d'],		'expected' => 'Day before yesterday'],
			['params' => ['now-1w/d', 'now-1w/d'],		'expected' => 'This day last week'],
			['params' => ['now-1w/w', 'now-1w/w'],		'expected' => 'Previous week'],
			['params' => ['now-1M/M', 'now-1M/M'],		'expected' => 'Previous month'],
			['params' => ['now-1y/y', 'now-1y/y'],		'expected' => 'Previous year'],
			['params' => ['now/d', 'now/d'],			'expected' => 'Today'],
			['params' => ['now/d', 'now'],				'expected' => 'Today so far'],
			['params' => ['now/w', 'now/w'],			'expected' => 'This week'],
			['params' => ['now/w', 'now'],				'expected' => 'This week so far'],
			['params' => ['now/M', 'now/M'],			'expected' => 'This month'],
			['params' => ['now/M', 'now'],				'expected' => 'This month so far'],
			['params' => ['now/y', 'now/y'],			'expected' => 'This year'],
			['params' => ['now/y', 'now'],				'expected' => 'This year so far'],
			['params' => ['now-1', 'now'],				'expected' => 'Last 1 second'],
			['params' => ['now-5', 'now'],				'expected' => 'Last 5 seconds'],
			['params' => ['now-55s', 'now'],			'expected' => 'Last 55 seconds'],
			['params' => ['now-60s', 'now'],			'expected' => 'Last 1 minute'],
			['params' => ['now-600s', 'now'],			'expected' => 'Last 10 minutes'],
			['params' => ['now-3600s', 'now'],			'expected' => 'Last 1 hour'],
			['params' => ['now-3601s', 'now'],			'expected' => 'Last 3601 seconds'],
			['params' => ['now-86400s', 'now'],			'expected' => 'Last 1 day'],
			['params' => ['now-59m', 'now'],			'expected' => 'Last 59 minutes'],
			['params' => ['now-60m', 'now'],			'expected' => 'Last 1 hour'],
			['params' => ['now-77m', 'now'],			'expected' => 'Last 77 minutes'],
			['params' => ['now-600m', 'now'],			'expected' => 'Last 10 hours'],
			['params' => ['now-3600m', 'now'],			'expected' => 'Last 60 hours'],
			['params' => ['now-1440m', 'now'],			'expected' => 'Last 1 day'],
			['params' => ['now-23h', 'now'],			'expected' => 'Last 23 hours'],
			['params' => ['now-24h', 'now'],			'expected' => 'Last 1 day'],
			['params' => ['now-77h', 'now'],			'expected' => 'Last 77 hours'],
			['params' => ['now-1d', 'now'],				'expected' => 'Last 1 day'],
			['params' => ['now-3d', 'now'],				'expected' => 'Last 3 days'],
			['params' => ['now-1M', 'now'],				'expected' => 'Last 1 month'],
			['params' => ['now-5M', 'now'],				'expected' => 'Last 5 months'],
			['params' => ['now-1y', 'now'],				'expected' => 'Last 1 year'],
			['params' => ['now-3y', 'now'],				'expected' => 'Last 3 years'],
			['params' => ['now+5m', 'now'],				'expected' => ['from_modifiers' => ['+5 minutes'], 'to_modifiers' => []]],
			['params' => ['now', 'now'],				'expected' => ['from_modifiers' => [], 'to_modifiers' => []]],
			['params' => ['now/m', 'now/m'],			'expected' => ['from' => 'Y-m-d H:i:00', 'to' => 'Y-m-d H:i:59']],
			['params' => ['now/h', 'now/h'],			'expected' => ['from' => 'Y-m-d H:00:00', 'to' => 'Y-m-d H:59:59']],
			['params' => ['now', 'now/d'],				'expected' => ['from_modifiers' => [], 'to_modifiers' => ['today +23 hours +59 minutes +59 seconds']]],
			['params' => ['now/d', 'now/w'],			'expected' => ['from_modifiers' => ['today'], 'to_modifiers' => ['Sunday this week 23:59:59']]],
			['params' => ['now/w', 'now/M'],			'expected' => ['from_modifiers' => ['Monday this week 00:00:00'], 'to_modifiers' => ['last day of this month 23:59:59']]],
			['params' => ['now/M', 'now/y'],			'expected' => ['from_modifiers' => ['first day of this month 00:00:00'], 'to_modifiers' => ['last day of December this year 23:59:59']]],
			['params' => ['now/y', 'now/d'],			'expected' => ['from_modifiers' => ['first day of January this year 00:00:00'], 'to_modifiers' => ['tomorrow', '-1 second']]],
			['params' => ['now/d-3d', 'now/M-1M'],		'expected' => ['from_modifiers' => ['today', '-3 days'], 'to_modifiers' => ['last day of this month 23:59:59', '-1 month']]],
			['params' => ['now-3d/d', 'now-2M/M'],		'expected' => ['from_modifiers' => ['-3 days', 'today'], 'to_modifiers' => ['last day of -2 months', 'tomorrow', '-1 second']]],
			['params' => ['now-3h/d', 'now'],			'expected' => ['from_modifiers' => ['-3 hours', 'today'], 'to_modifiers' => []]],
			['params' => ['now-3w/M', 'now+1M/M'],		'expected' => ['from_modifiers' => ['-3 weeks', 'first day of this month 00:00:00'], 'to_modifiers' => ['+1 month', 'last day of this month 23:59:59']]]
		];
	}

	/**
	 * @dataProvider provider
	 */
	public function test($params, $expected) {
		$result = call_user_func_array('relativeDateToText', $params);

		if (is_array($expected)) {
			for ($attempts = 0; $attempts < 2; $attempts++) {
				$from_ts = new DateTime();
				if (array_key_exists('from_modifiers', $expected)) {
					foreach ($expected['from_modifiers'] as $from_modifier) {
						$from_ts->modify($from_modifier);
					}
					$from_format = 'Y-m-d H:i:s';
				}
				else {
					$from_format = $expected['from'];
				}

				$to_ts = new DateTime();
				if (array_key_exists('to_modifiers', $expected)) {
					foreach ($expected['to_modifiers'] as $to_modifier) {
						$to_ts->modify($to_modifier);
					}
					$to_format = 'Y-m-d H:i:s';
				}
				else {
					$to_format = $expected['to'];
				}

				$expected_result = $from_ts->format($from_format).' - '.$to_ts->format($to_format);

				if ($expected_result === $result) {
					break;
				}
			}

			$this->assertSame($expected_result, $result);
		}
		else {
			$this->assertSame($expected, $result);
		}
	}
}
