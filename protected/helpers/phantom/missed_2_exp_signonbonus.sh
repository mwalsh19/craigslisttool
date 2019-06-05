php /Applications/MAMP/htdocs/craiglist-tool/protected/yiic jobs init --index=0 --xml='missed_2_exp_signonbonus.xml'
php /Applications/MAMP/htdocs/craiglist-tool/protected/yiic jobs init --index=1 --xml='missed_2_exp_signonbonus.xml'
php /Applications/MAMP/htdocs/craiglist-tool/protected/yiic jobs retryPosts --file='missed_2_exp_signonbonus'
