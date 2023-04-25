<?php
namespace App\Http\Controllers;


use App\Models\DocumentationFile;
use Illuminate\Support\Facades\DB;

/**
 * This was my start at putting together AI generated documentation for subnautica.
 * Further research indicated this could cost hundreds of dollars at current rates so it's on hold for now.
 *
 * Class DocumentationController
 * @package App\Http\Controllers
 */
class DocumentationController extends Controller
{
    public function parse()
    {
        $projectDir = '/mnt/c/Users/Admir/Desktop/SubnauticaDnSpyDump/Assembly-CSharp/';

        //list every file in $projectDir
        $files = scandir($projectDir);
        foreach ($files as $file) {
            // Ignore directories
            if (is_dir($file))
                continue;


//            $path = str_replace('/mnt/c/Users/Admir/Desktop/', '', $projectDir . $file);
//            if (Doc
//umentationFile::where('path', $path)->first())
//                continue;

            // Ignore non .cs files
            if (substr($file, -3) !== '.cs')
                continue;

//            $file = new DocumentationFile([
//                'path' => $path,
//                'estimated_tokens' => $this->estimateTokenCount(file_get_contents($projectDir.$file))
//            ]);
//            $file->save();

            echo $projectDir . $file . "<br />";
        }
    }

    private function estimateTokenCount($string) {
        $tokenCount = 0;

        $chars = preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($chars as $char) {
            $ord = mb_ord($char, 'UTF-8');

            if ($ord <= 0x7F)
                $tokenCount += 1;
            else if ($ord <= 0x7FF)
                $tokenCount += 2;
            else if ($ord <= 0xFFFF)
                $tokenCount += 3;
            else
                $tokenCount += 4;
        }

        return $tokenCount;
    }
}
