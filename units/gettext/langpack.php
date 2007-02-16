<?php

    /*
     * Little script for converting a Rosetta tarball into
     * a language package.
     *
     * Usage:
     * download a rosetta export from 
     *      https://launchpad.net/elgg/trunk/+pots/elgg/+export
     * and run this script at the download location
     *
     * Notice: this script will only work on *NIX systems
     */

    // Feel free to add your own
    $complete = array('nl',
                        'de',
                        'is',
                        'it',
                        'es',
                        'sv',
                        'pt_BR',
                        'fr',
                        'lt',
                        'ko',
                        'eu',
                        'hu',
                        'en_GB');

    $path = dirname(__FILE__).'/rosetta-elgg/';
    $install_path = dirname(__FILE__).'/languages/';

    // Extract the translations
    $command = "tar xvzf rosetta-elgg.tar.gz";
    shell_exec($command);

    // Create archive
    $command = "tar cvfj ".dirname(__FILE__)."/elgg-langpack_".date("mdY").".tar.bz2";
//    shell_exec($command);

    if (!file_exists($install_path))
    {
        mkdir($install_path);
    }
    
    $basedir = opendir($path);

    while (false !== ($file = readdir($basedir)))
    {

        if (substr($file, -3, 3) == ".po")
        {        
            $lang = substr($file, 0, -3);
            
            $lang_path = $install_path.$lang.'/';
            $lang_install_path = $lang_path.'LC_MESSAGES/';

            if (!file_exists($lang_path))
            {
                mkdir($lang_path);
                mkdir($lang_install_path);
            }
            
            print "Processing: ".$lang."\n";
            $command = "msgfmt -o {$lang_install_path}elgg.mo {$path}{$file}";
            shell_exec($command);

            // Copy the .po file as well
            copy($path.$file, $lang_install_path.$file);

            if (in_array($lang, $complete))
            {
                // Create zip file
                $command = "zip -r ".dirname(__FILE__).'/elgg-langpack_'.date("mdY").'.zip'." languages/{$lang}/*";
                shell_exec($command);

                // Create UNIX archive
                if (!file_exists(dirname(__FILE__).'/elgg-langpack_'.date("mdY").'.tar'))
                {
                    $command = "tar cvf ".dirname(__FILE__).'/elgg-langpack_'.date("mdY").'.tar'." languages/{$lang}/*";
                }
                else
                {
                    $command = "tar --append --file=".dirname(__FILE__).'/elgg-langpack_'.date("mdY").'.tar'." languages/{$lang}/*";
                }
                shell_exec($command);
            }
        }        
    }
    
    if (file_exists(dirname(__FILE__)."/elgg-langpack_".date("mdY").".tar.bz2"))
    {
        unlink(dirname(__FILE__)."/elgg-langpack_".date("mdY").".tar.bz2");
    }

    $command = "bzip2 ".dirname(__FILE__)."/elgg-langpack_".date("mdY").".tar";
    shell_exec($command);
?>
