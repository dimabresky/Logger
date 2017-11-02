<?php
namespace travelsoft\rest;

/**
 * Logger
 *
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class Logger {
    
    /**
     * @var string
     */
    protected $_path2Log = '';
    
    /**
     * @param string $path2Log
     */
    public function __construct(string $path2Log = null) {
        
        if (strlen($path2Log)) {
            $this->_path2Log = $path2Log;
        } else {
            $this->_path2Log = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'). '/logger_log.txt';
        }
        
    }
    
    /**
     * Запись в лог
     * @param string $text
     */
    public function write(string $text) {
        
        if (strlen($text)>0) {
            ignore_user_abort(true);
            if ($fp = @fopen($this->_path2Log, "ab")) {
                if (flock($fp, \LOCK_EX)) {
                    @fwrite($fp, "Host: ".$_SERVER["HTTP_HOST"]."\nDate: ".date("d.m.Y H:i:s")."\n".$text."\n");

                    $arBacktrace = array_slice(debug_backtrace(true, 7), 1, 7);
                    $strFunctionStack = "";
                    $strFilesStack = "";
                    $firstFrame = (count($arBacktrace) == 1? 0: 1);
                    $iterationsCount = min(count($arBacktrace), 7);
                    for ($i = $firstFrame; $i < $iterationsCount; $i++) {
                        if (strlen($strFunctionStack)>0) {
                            $strFunctionStack .= " < ";
                        }
                        if (isset($arBacktrace[$i]["class"])) {
                            $strFunctionStack .= $arBacktrace[$i]["class"]."::";
                        }
                        $strFunctionStack .= $arBacktrace[$i]["function"];

                        if(isset($arBacktrace[$i]["file"])) {
                            $strFilesStack .= "\t".$arBacktrace[$i]["file"].":".$arBacktrace[$i]["line"]."\n";
                        }
                        if($bShowArgs && isset($arBacktrace[$i]["args"])) {
                            $strFilesStack .= "\t\t";
                            if (isset($arBacktrace[$i]["class"])) {
                                $strFilesStack .= $arBacktrace[$i]["class"]."::";
                            }
                            $strFilesStack .= $arBacktrace[$i]["function"];
                            $strFilesStack .= "(\n";
                            foreach($arBacktrace[$i]["args"] as $value) {
                                $strFilesStack .= "\t\t\t".$value."\n";
                            }
                            $strFilesStack .= "\t\t)\n";
                        }
                    }

                    if (strlen($strFunctionStack)>0) {
                        @fwrite($fp, "    ".$strFunctionStack."\n".$strFilesStack);
                    }

                    @fwrite($fp, "----------\n");
                    @fflush($fp);
                    @flock($fp, \LOCK_UN);
                    @fclose($fp);
                }
            }
            ignore_user_abort(false);
        }
    }
    
    /**
     * Читаем содержимое файла
     * @return string
     */
    public function read () {
        $text = '';
        if (file_exists($this->_path2Log)) {
            $text = (string)fread(fopen($this->_path2Log), filesize($this->_path2Log));
        }
        return $text;
    }
}

