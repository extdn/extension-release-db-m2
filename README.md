# ExtDN Extension Releases Database (Magento 2)
Intended to become the data source for the [dashboard extension](https://github.com/extdn/extension-dashboard-m2).

The aim is to keep barrier to contribute as low as possible. For now the output is a simple csv file.


Usage:

Place the latest [KeepAChangeLog](https://keepachangelog.com/en/1.0.0/) compatible changelog into the folder structure
input/Vendor/ExtensionName
provide a 

run php -f main.php
this updates output/all-releases.csv

commit


Next Steps:
Allow other input methods
Maybe keep a manual.csv that merges to output/all-releases.csv

Feed into FriendsOfPHP-Security-Advisories