<?php
/**
 * Created by PhpStorm.
 * User: Domin
 * Date: 2018/2/21
 * Time: 16:06
 */

namespace Models\Utilities;

//arg1 -v --grades=good -T 5 -l val1,val2,val3 --names=Dominic,Mimi,Luke
//arg2  -m 1,2,3 -Z bailey -f -a Erica,is,36,inches,tall
//arg3 --geek=eckroth,duncan,hans,sam --cool=plante --slick=sam,RYAN,Huddy,drew,john

class ParseArgv
{
    private $argsUnparsed;
    private $argsParsed;
    private $flags;
    private $singles;
    private $doubles;

    public function __construct($args)
    {
        $this->argsUnparsed = $args;
        $this->parseFlags();
        $this->parseSingles();
        $this->parseDoubles();
        $this->output();
    }

    /* parse flags function*/
    private function parseFlags()
    {
        //go through command line looking for flags
        for($i = 0; $i < count($this->argsUnparsed); $i++)
        {
            if (preg_match("/^-.{1}$/", $this->argsUnparsed[$i], $match))
            {
                if (preg_match("/^-/", $this->argsUnparsed[$i + 1], $match) )
                {
                    //get rid of dish and delete parsed segment
                    $this->flags[str_replace("-", "", $this->argsUnparsed[$i])] = null;
                    $this->argsUnparsed[$i] = null;
                }
            }
        }

        //if flag exists at least one in command line, move it to parsed
        if($this->flags != null)
        {
            $this->argsParsed['FLAGS'] = $this->flags;
        }
    }

    /* parse flags function*/
    private function parseSingles()
    {
        //go through command line looking for single dish
        for ($i = 0; $i < count($this->argsUnparsed); $i++)
        {
            if (preg_match("/^-.{1}$/", $this->argsUnparsed[$i], $match))
            {
                //check item after single dish is a list or not
                if (preg_match("/,/", $this->argsUnparsed[$i + 1], $match))
                {
                    //break list into single element with index
                    $list = explode(",", $this->argsUnparsed[$i + 1]);

                    foreach ($list as $index => $data)
                    {
                        //get rid of dish and delete parsed segment
                        $this->singles[str_replace("-", "", $this->argsUnparsed[$i])][$index] = $data;
                    }
                    $this->argsUnparsed[$i+1] = null;
                }
                else {
                    //get rid of dish and delete parsed segment
                    $this->singles[str_replace("-", "", $this->argsUnparsed[$i])] = $this->argsUnparsed[$i+1];
                    $this->argsUnparsed[$i+1] = null;
                }
                $this->argsUnparsed[$i] = null;
            }
        }

        //if single dish exists at least one in command line, move it to parsed
        if($this->singles != null)
        {
            $this->argsParsed['SINGLES'] = $this->singles;
        }
    }

    /* parse flags function*/
    private function parseDoubles()
    {
        //go through command line looking for double dishes
        for ($i = 0; $i < count($this->argsUnparsed); $i++)
        {
            if(preg_match("/^--/", $this->argsUnparsed[$i], $match))
            {
                //break the string when encounter "=" and take the part before break
                $subCat = strtok((str_replace("--","",$this->argsUnparsed[$i])),"=");
                $data = substr($this->argsUnparsed[$i], strrpos($this->argsUnparsed[$i], '=') + 1);

                //check item after double dishes is a list or not
                if (preg_match("/,/", $data, $match))
                {
                    //break list into single element with index
                    $temp = explode(",",$data);

                    foreach ($temp as $key => $val)
                    {
                        $this->doubles[$subCat][$key] = $val;
                    }
                    $this->argsUnparsed[$i] = null;
                }
                else
                {
                    $this->doubles[$subCat] = $data;
                    $this->argsUnparsed[$i] = null;
                }
            }
        }

        //if double dishes exists at least one in command line, move it to parsed
        if($this->doubles != null)
        {
            $this->argsParsed['DOUBLES'] = $this->doubles;
        }
    }

    /* print out parsed argument*/
    private function output()
    {
        //go through parsed argument
        foreach ($this->argsParsed as $category => $subCat)
        {
            //print category Flag, single, double
            print("$category\n");
            //go through each category
            foreach ($subCat as $sub => $data)
            {
                print("'$sub' ");
                //check data is one segment or more
                if (count($data) > 1)
                {
                    print("=> ");
                    foreach ($data as $arg => $value)
                    {
                        //print every segment data and following a comma
                        print("[$arg] '$value'");
                        if (next($data) != null)
                        {
                            print(", ");
                        }
                    }
                    //the size of data array/ the amount of argument
                    $num = sizeof($data);
                    print(" ($num arguments) ");
                }
                else {
                    print("=> '$data'");
                    print(" (1 argument) ");
                }
                print("\n");
            }
            print("\n");
        }
    }
}