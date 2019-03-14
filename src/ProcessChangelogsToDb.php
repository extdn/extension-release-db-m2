<?php

namespace Extdn\ExtensionReleaseDb;

use League\Csv\Writer;
use Changelog\Parser;

use Noodlehaus\Config;

class ProcessChangelogsToDb
{
    public function execute()
    {
        //TODO add handlers to deal with different sources beyond keepachangelog.com
        $allChangelogs = $this->getAllExtensionChangelogs();
        $releases = [];
        foreach ($allChangelogs as $extensionName => $allChangelog) {
            $parser = new Parser(file_get_contents($allChangelog));

            foreach ($parser->getReleases() as $release) {
                $oneRelease = [];
                $oneRelease['extension'] = $extensionName;
                $oneRelease['version'] = str_replace(['[', ']'], '', $release['name']);
                if (strtolower($oneRelease['version']) == 'unreleased') {
                    continue;
                }

                $oneRelease['date'] = $release['date'];
                $oneRelease['security'] = 0;

                $content = [];
                foreach ($release['changes'] as $category => $details) {
                    if ($category == 'security') {
                        $oneRelease['security'] = 1;
                        $this->handleSecurityRelease(dirname($allChangelog), $release, $details);
                    }
                    $content[] = strtoupper($category) . ':';
                    foreach ($details as $line) {
                        $content[] = $line;
                    }
                }
                $oneRelease['content'] = implode('\n', $content);

                $releases[] = $oneRelease;
            }
        }

        $csv = Writer::createFromPath(__DIR__ . "/../output/all-releases.csv", "w");
        $csv->insertOne(['extension', 'version', 'date', 'security', 'content']);
        $csv->insertAll($releases);
    }

    private function handleSecurityRelease($path, $release, $details)
    {
        if (file_exists($path . '/config.ini')) {
            $conf = Config::load($path . '/config.ini');
            $fullPackageName = $conf->get('composer-package-name');
            list($vendor, $package) = explode('/', $fullPackageName);

            echo "TODO: Should create FriendsOfPHP Security Advisory for " . $vendor . '/' . $package . $release['name'] . PHP_EOL;
        }
    }

    private function getAllExtensionChangelogs()
    {
        $result = [];
        $di = new \RecursiveDirectoryIterator(__DIR__ . '/../input', \RecursiveDirectoryIterator::SKIP_DOTS);
        $it = new \RecursiveIteratorIterator($di);

        foreach ($it as $file) {
            if (strtoupper(pathinfo($file, PATHINFO_FILENAME)) == "CHANGELOG") {
                $name = basename(dirname(dirname($file))) . '_' . basename(dirname($file));
                $result[$name] = $file;
            }
        }
        return $result;
    }
}
