<?php
/*****************************************************************
    Supports diagnostic tracing
                               11/18/2006 6:19AM
*****************************************************************/
// require_once('tops_lib/sys/TConfiguration.php');
class TTracer
{
    private static $tracer;

    private $messages = array();
    private $echoMessage = false;
    private $suspended = array();
    private $traceId = array();
    private $traceCount = -1;
    private $outputMode = "html";

    public function  __construct()
    {
        $this->outputMode = TConsole::GetOutputMode();
    }

    public function active()
    {
        return  ($this->traceCount > -1 &&
                (!$this->suspended[$this->traceCount]));
    }

    public function addMessage($message)
    {

        if ($this->traceCount > -1) {
            if (!$this->suspended[$this->traceCount]) {
                $traceId = $this->traceId[$this->traceCount];
                if (!empty($traceId))
                    $message = "[$traceId] ".$message;
                array_push($this->messages,$message);
                if ($this->echoMessage)
                    echo
                        $this->outputMode == "html" ?
                            '<span style="background-color:white">Trace: '.$message.'</span><br/>' :
                            "$message\n";
            }
        }
    }  //  addMessage

    public function setEchoOn()
    {
        $this->echoMessage = true;
    }  //  echoOn

    public function setEchoOff()
    {
       $this->echoMessage = false;
    }  //  echoOff

    public function suspendTrace()
    {
        $this->suspended[$this->traceCount] = true;
    }  //  suspend

    public function resumeTrace()
    {
        $this->suspended[$this->traceCount] = false;
    }  //  resume

    public function resetTraces()
    {
        $this->messages = array();
        $this->suspended = array();
    }  //  reset

    public function startTrace($traceId = '')
    {
        array_push($this->traceId,$traceId);
        array_push($this->suspended,false);
        ++$this->traceCount;
    }  //  start

    public function stopTrace($traceId='')
    {
        if ($this->traceCount < 0)
            return; // already off
        if ($this->traceId[$this->traceCount] == $traceId) {
            array_pop($this->traceId);
            array_pop($this->suspended);
            --$this->traceCount;
        }
    }  //  end

    public function getTraceMessages()
    {
        return $this->messages;
    }  //  getMessages


//static methods

    public static function On($traceId='')
    {
        $configuration = TConfiguration::GetSettings();
        if (!isset(TTracer::$tracer)) {
            TTracer::$tracer = new TTracer();
        }

        TTracer::$tracer->startTrace($traceId);
        if (!empty($traceId)) {
            $switch = $configuration->getValue('traces',$traceId,0);
            if (empty($switch))
                TTracer::$tracer->suspendTrace();
        }

        $switch = $configuration->getValue('traces','echo',0);
        if (!empty($switch))
            TTracer::EchoOn();

    }  //  TTracer::On


    public static function Off($traceId='')
    {

        if (isset(TTracer::$tracer))
            TTracer::$tracer->stopTrace($traceId);
    }  //  traceOff

    public static function Suspend()
    {
        if (isset(TTracer::$tracer))
            TTracer::$tracer->suspendTrace();
    }  //  TTracer::Suspend()

    public static function Resume()
    {
        if (isset(TTracer::$tracer))
            TTracer::$tracer->resumeTrace();
    }  //  resumeTrace

    public static function Assert($value,$trueMessage,$falseMessage='',$file='',$line=0) {
        if (empty($value)) {
            if (empty($falseMessage))
                $falseMessage = 'NOT: '.$trueMessage;
            TTracer::Trace($falseMessage,$file,$line);
        }
        else
            TTracer::Trace($trueMessage,$file,$line);
    }

    public static function Trace($message,$file='',$line=0)
    {
        if (isset(TTracer::$tracer)) {
            if (!empty($file)) {
                if (!empty($line))
                    $message .= "  on line $line";
                $message .= " in file '$file'.";
            }
            TTracer::$tracer->addMessage($message);
        }

    }  //  trace

    public static function WriteLines()
    {
        $messages = TTracer::GetMessages();
        $count = count($messages);
        if ($count == 0)
            return '';
        $result = $count.' trace messages\n';
        foreach ($messages as $message)
            $result .= "$message\n";
        return $result;
    }  //  dumpTrace


    public static function Render()
    {
        $messages = TTracer::GetMessages();
        $br = TConsole::GetOutputMode() == "html" ? '<br>' : '';
        $count = count($messages);
        if ($count == 0)
            return '';
        $result = $count." trace messages$br\n";
        foreach ($messages as $message)
            $result .= "$message$br\n";
        return $result;
    }  //  dumpTrace

    public static function PrintMessages() {
        $messageText = TTracer::Render();
        if (!empty($messageText))
            print TConsole::GetOutputMode() == "html" ?
                print '<div id="trace">'.$messageText.'</div>' :
                "$messageText\n";
    }

    public static function GetMessages()
    {
        if (isset(TTracer::$tracer))
            return TTracer::$tracer->getTraceMessages();
        return array();
    }  //  getTraceMessages

    public static function EchoOn()
    {
        if (isset(TTracer::$tracer))
            TTracer::$tracer->setEchoOn();
    }  //  TTracer::EchoOn

    public static function EchoOff()
    {
        if (isset(TTracer::$tracer))
            TTracer::$tracer->setEchoOff();
    }  //  TTracer::EchoOff



    public static function ShowArray($arr) {
        if (isset(TTracer::$tracer) && TTracer::$tracer->active()) {
            if (TConsole::GetOutputMode() != "html") {
                print "\n";
                print_r($arr);
                print "\n";
            }
            else {
                print '<div style="padding:3px;background-color:white;color:black;text-align:left">';
                print '<pre>';
                print_r($arr);
                print '</pre></div>';
            }
        }
    }


} // end TTracer

// some convenience functions for backward compatibility
/*
function traceAssert($value,$trueMessage,$falseMessage='',$file='',$line=0) {
    TTracer::Assert($value,$trueMessage,$falseMessage,$file,$line);
}

function trace($message,$file='',$line=0)
{
    TTracer::Trace($message,$file,$line);
}  //  trace
*/


