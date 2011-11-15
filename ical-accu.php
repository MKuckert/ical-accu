<?php
if($argc<2) die('usage: '.$argv[0].' <ical-file> [<num-entries>]');

$input=file($argv[1]);
if(!$input) die('Failed to read input file');
$input=array_map('trim', $input);

$events=array();
$event=array();
foreach($input as $index=>$row) {
	$split=explode(':', $row, 2);
	if(count($split)<2) continue;
	list($key, $value)=$split;
	if($key==='END' and $value==='VEVENT') {
		if(isset($event['summary'], $event['start'], $event['end'])) {
			unset($event['summary']);
			$events[]=$event;
		}
		
		$event=array();
	}
	else if(in_array($key, array('DTSTART', 'DTEND'))) {
		$event[strtolower(substr($key, 2))]=strtotime($value);
	}
	else if($key==='SUMMARY') {
		$event['summary']=$value;
	}
}

$weeks=array();
foreach($events as $event) {
	$week=date('Y-W', $event['start']);
	$duration=($event['end']-$event['start']) / (60*60); // Skaliert auf Stunden
	if(isset($weeks[$week])) {
		$weeks[$week]['duration']+=$duration;
		if($event['start']<$weeks[$week]['start']) {
			$weeks[$week]['start']=$event['start'];
		}
	}
	else {
		$weeks[$week]=array(
			'start' => $event['start'],
			'duration' => $duration
		);
	}
}

ksort($weeks);
$weeks=array_reverse($weeks);
$weeks=array_slice($weeks, isset($argv[3]) ? $argv[3] : 0, isset($argv[2]) ? $argv[2] : 10);
$sum=0;
foreach($weeks as $week=>$entry) {
	$end=mktime(0, 0, 0, date('n', $entry['start']), date('j', $entry['start'])+4, date('Y', $entry['start']));
	$sum+=$entry['duration'];
	echo date('d.m', $entry['start']).' '.date('d.m', $end).': '.$entry['duration'].PHP_EOL;
}

$mean=$sum/count($weeks);
echo 'Ø'.$mean.PHP_EOL;
echo '∑'.($sum-40*count($weeks)).PHP_EOL;
