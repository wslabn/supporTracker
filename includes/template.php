<?php
class Template {
    public $title = 'SupportTracker';
    public $content = '';
    public $scripts = [];
    public $styles = [];
    
    public function setTitle($title) {
        $this->title = $title;
    }
    
    public function setContent($content) {
        $this->content = $content;
    }
    
    public function addScript($script) {
        $this->scripts[] = $script;
    }
    
    public function addStyle($style) {
        $this->styles[] = $style;
    }
    
    public function render() {
        include 'includes/layouts/main.php';
    }
}

function renderPage($title, $contentFile, $data = []) {
    extract($data);
    
    ob_start();
    include "views/$contentFile";
    $content = ob_get_clean();
    
    $template = new Template();
    $template->setTitle($title);
    $template->setContent($content);
    $template->render();
}
?>