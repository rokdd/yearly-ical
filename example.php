
<!DOCTYPE html>
<?php


/*
*
* Copyright (c) 2018 Robert Kühn.
*
* The file based on the script of https://www.gaijin.at/scrphpcalj.php
*
* This file is part of yearly-ical.
* 
* yearly-ical is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
* 
* yearly-ical is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License along with yearly-ical. If not, see http://www.gnu.org/licenses/.
*
*/

//Updates and author: https://www.rokdd.de

//TODO: Replace the table with css-flex

//TODO: Use as parser: https://github.com/u01jmg3/ics-parser

//load the include file  for parsing ical
require_once 'class.iCalReader.php';

$monat  = date('n');
$jahr   = date('Y');
$heute  = date('d');
$monate = array('Januar', 'Februar', 'M&auml;rz', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember');

$cals = array('calender_ident_1' => array('d' => array(), 'url' => 'https://calendar.google.com/..group.calendar.google.com/basic.ics'), 'calender_ident_2' => array('d' => array(), 'url' => 'https://calendar.google.com/..group.calendar.google.com/basic.ics'));

foreach ($cals as $k => $arr_k) {
    $cals[$k]['o'] = new ical($arr_k['url']);

}

echo '<html>
<head>

<style type="text/css">
        body,table { font-size: 1.5em;font-family:"Trebuchet MS"; }
        tr,td,p {font-size:0.7em;}
        th  {font-family:Arial; text-transform:uppercase;font-size:1.2em}
        td.wday {text-align:center;color:black;text-transform:uppercase}
        td.cal_calender_ident_1 {background-color:#FF3366;color:white;font-size:0.7em;font-weight:bold}
        td.cal_calender_ident_2 {background-color:black;color:white;font-size:0.7em;font-weight:bold}</style>

' . "\n" . '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />' . "\n" . '</head>' . "\n" . '<body>
<table border=0 width=100%>';
echo '<th colspan=4 align=center style="font-size:18pt;">YOUR TITLE ' . $jahr . '</th>';

for ($reihe = 1; $reihe <= 3; $reihe++) {
    echo '<tr>';
    for ($spalte = 1; $spalte <= 4; $spalte++) {
        $this_month = ($reihe - 1) * 4 + $spalte;
        $erster     = date('w', mktime(0, 0, 0, $this_month, 1, $jahr));
        $insgesamt  = date('t', mktime(0, 0, 0, $this_month, 1, $jahr));
        if ($erster == 0) {
            $erster = 7;
        }

        echo '<td width="25%" valign=top>';
        echo '<table border=0 align=center style="width:90%">';
        echo '<th colspan=7 align=center style="">' . $monate[$this_month - 1] . '</th>';
        echo '<tr><td class="wday"><b>Mo</b></td><td class="wday"><b>Di</b></td>';
        echo '<td class="wday"><b>Mi</b></td><td class="wday"><b>Do</b></td>';
        echo '<td class="wday"><b>Fr</b></td><td class="wday"><b>Sa</b></td>';
        echo '<td class="wday" style="color:gray"><b>So</b></td></tr>';
        echo '<tr><br>';
        $i = 1;
        while ($i < $erster) {
            echo '<td> </td>';
            $i++;
        }
        $i = 1;
        $w = array();
        while ($i <= $insgesamt) {
            $rest = ($i + $erster - 1) % 7;
            if (($i == $heute) && ($this_month == $monat)) {
                echo '<td style=" font-family:Verdana; background:#ff0000;" align=center>';
            } else {
                echo '<td style="" font-family:Verdana" align=center>';
            }
            if (($i == $heute) && ($this_month == $monat)) {
                echo '<span style="color:#ffffff;">' . $i . '</span>';
            } else if ($rest == 6) {
                echo '<span style="color:gray">' . $i . '</span>';
            } else if ($rest == 0) {
                echo '<span style="color:gray">' . $i . '</span>';
            } else {
                echo $i;
            }
            echo "</td>\n";
            $w[] = mktime(0, 0, 0, $this_month, $i, $jahr); // $jahr . '-' . str_pad($this_month, 2, "0", STR_PAD_LEFT) . '-' . str_pad($i, 2, "0", STR_PAD_LEFT);
            if ($rest == 0) {
                echo "</tr>\n";
//here we go for each of us
                foreach ($cals as $k => $arr_k) {
                    $arr_e = $cals[$k]['o']->eventsByDateBetween($w[count($w) - 1] - (60 * 60 * 24 * 7), $w[count($w) - 1]);
                    //each calender has a row..
                    echo '<tr><td class="cal_' . $k . '" colspan="7">';
                    foreach ($arr_e as $o) {
                        foreach ($o as $o2) {
                            //output each entry with the title/summary after each other
                            echo $o2->title() . " ";
                        }
                    }
                    echo '</td></tr>';
                }
                $w = array();
                echo '<tr><td  colspan="7" height="5"></td></tr>';
                echo "<tr>\n";
            }

            $i++;
        }
        echo '</tr>';
        echo '</table>';
        echo '</td>';
    }
    echo '</tr>';
}

echo '</table></body></html>';
?>