<?php

/**
 *  Afficha de message et alertes
 */
class Message
{
    public static $messages = array();

    public static function init($twig)
    {
        $function = new Twig_SimpleFunction('Messages', function () {
            global $twig;
            static $displayed = 0;     

            if ($displayed == 0 && count(Message::$messages) > 0)
            {
                session_start();
                $displayed = 1;
                $_SESSION['display_messages'] = [];
                session_write_close();
                
                return ($twig->render('messages/index.twig', ['messages' => Message::$messages]));
            }
        });

        $twig->addFunction($function);

        if ($m = $_SESSION['display_messages'])
            Message::$messages = $m;
   	}

    public static function add($type = 'info', $title, $message = '')
    {
        session_start();
        if (!empty($title))
        {
            Message::$messages[] = ['type' => $type, 'title' => $title, 'message' => $message];
            $_SESSION['display_messages'] = Message::$messages;
        }
        session_write_close();
    }
}
?>