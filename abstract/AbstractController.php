<?php
namespace App\core\abstract;
use App\core\Session;
use App\core\App;

abstract class AbstractController
{
    protected $commonLayout = 'base';
    protected Session $session;

    protected function __construct(Session $session){
        $this->session = $session;
    }

    public abstract function index();
    public function render(string $view){
        ob_start();
        require_once '../templates/' . $view . '.html.php';

        $containForLayout = ob_get_clean();
        require_once '../templates/layout/'.$this->commonLayout.'.layout.html.php';
    }
}