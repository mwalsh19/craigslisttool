<?php

//Add "2>&1" after command for show errors on the output
echo 'command: ls -la';
exec('ls -la 2>&1', $output);
var_dump($output);

echo 'command: ls';
exec('ls 2>&1', $output_2);
var_dump($output_2);

echo 'command: phantomjs --version';
exec('phantomjs --version 2>&1', $output_3);
var_dump($output_3);

echo 'command: pwd';
exec('pwd 2>&1', $output_4);
var_dump($output_4);

//echo 'command: pwd';
//exec('phantomjs /Users/dev/Documents/projects/craiglist-tool/protected/helpers/phantom/index.js --indx="0" --xml="/Users/dev/Documents/projects/craiglist-tool/xml/post_job_1.xml" --showCookies=false --cookies-file=cookie-jar.txt 2>&1', $output_5);
//var_dump($output_5);


