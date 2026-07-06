<?php
class HomeController {
    
    public function index() {
        include 'views/home/index.php';
    }
    
    public function about() {
        include 'views/home/about.php';
    }
    
    public function contact() {
        redirect('/help-center');
    }
    
    public function helpCenter() {
        redirect('/help-center');
    }

    public function accounts() {
        include 'views/home/accounts.php';
    }

    public function services() {
        include 'views/home/services.php';
    }

    public function cards() {
        include 'views/home/cards.php';
    }

    public function investments() {
        include 'views/home/investments.php';
    }

    public function loans() {
        include 'views/home/loans.php';
    }

    public function charity() {
        include 'views/home/charity.php';
    }

    public function security() {
        include 'views/home/security.php';
    }

    public function faqs() {
        include 'views/home/faqs.php';
    }

    public function terms() {
        include 'views/home/terms.php';
    }

    public function investorPortal() {
        include 'views/home/investor-portal.php';
    }

    public function partnership() {
        include 'views/home/partnership.php';
    }
}
