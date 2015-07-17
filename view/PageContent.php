<?php

/**
 * Descrizione dei contenuti della pagina
 *
 * @author Mattia Ibba
 */
class PageContent {
    private $title;
    private $header;
    private $sidebar;
    private $content;
    private $errorMessage;
    private $confirmMessage;
    private $page;
    private $subPage;
    
    public function __construct() {}

    public function setTitle($title) {
        $this->title = $title;
    }
    
    public function getTitle() {
        return $this->title;
    }

    public function setHeader($header) {
        $this->header = $header;
    }

    public function getHeader() {
        return $this->header;
    }
    
    public function setSidebar($sidebar) {
        $this->sidebar = $sidebar;
    }
    
    public function getSidebar() {
        return $this->sidebar;
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function getContent() {
        return $this->content;
    }
    
    public function setErrorMessage($errorMessage) {
        $this->errorMessage = $errorMessage;
    }
    
    public function getErrorMessage() {
        return $this->errorMessage;
    }

    public function setConfirmMessage($confirmMessage) {
        $this->confirmMessage = $confirmMessage;
    }

    public function getConfirmMessage() {
        return $this->confirmMessage;
    }

    public function setPage($page) {
        $this->page = $page;
    }

    public function getPage() {
        return $this->page;
    }
    
    public function setSubPage($subPage) {
        $this->subPage = $subPage;
    }

    public function getSubPage() {
        return $this->subPage;
    }
}
