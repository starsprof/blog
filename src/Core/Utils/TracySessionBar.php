<?php


namespace App\Core\Utils;


use Tracy\IBarPanel;

class TracySessionBar implements IBarPanel
{
    /**
     * @var string
     */
    public $icon;
    public function __construct()
    {
        $this->icon = "<svg  xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 448 512\"><path fill=\"orange\" d=\"M400 224h-24v-72C376 68.2 307.8 0 224 0S72 68.2 72 152v72H48c-26.5 0-48 21.5-48 48v192c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V272c0-26.5-21.5-48-48-48zm-104 0H152v-72c0-39.7 32.3-72 72-72s72 32.3 72 72v72z\" class=\"\"></path></svg>";
    }

    /**
     * Renders HTML code for custom tab.
     * @return string
     */
    function getTab()
    {
        return '
        <span title="Session storage">
            '.$this->icon.'
        </span>';
    }

    /**
     * Renders HTML code for custom panel.
     * @return string
     */
    function getPanel()
    {
        $data = $_SESSION;
        unset($data['db_log']);
        unset($data['_tracy']);
        return  '
        <div style="max-width: 600px; max-height: 600px">
            <h1>
                <div style="width: 20px; height: 20px; display: inline-block;">'.$this->icon.'</div> 
                 Session storage
            </h1>
            <div class="tracy-inner">
               '.\Tracy\Dumper::toHtml($data).'
            </div>
        </div>';
    }
}