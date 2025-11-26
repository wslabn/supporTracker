<?php
class ModernTemplate {
    public $title = 'SupportTracker';
    public $pageTitle = '';
    public $content = '';
    public $headerActions = '';
    public $scripts = [];
    public $styles = [];
    
    public function setTitle($title) {
        $this->title = $title;
    }
    
    public function setPageTitle($pageTitle) {
        $this->pageTitle = $pageTitle;
    }
    
    public function setContent($content) {
        $this->content = $content;
    }
    
    public function setHeaderActions($actions) {
        $this->headerActions = $actions;
    }
    
    public function addScript($script) {
        $this->scripts[] = $script;
    }
    
    public function addStyle($style) {
        $this->styles[] = $style;
    }
    
    public function isActive($page) {
        $currentPage = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
        return $currentPage === $page ? 'active' : '';
    }
    
    public function render() {
        include 'includes/layouts/modern.php';
    }
}

function renderModernPage($title, $pageTitle, $contentFile, $data = [], $headerActions = '') {
    extract($data);
    
    ob_start();
    include "views/$contentFile";
    $content = ob_get_clean();
    
    $template = new ModernTemplate();
    $template->setTitle($title);
    $template->setPageTitle($pageTitle);
    $template->setContent($content);
    $template->setHeaderActions($headerActions);
    $template->render();
}
?>