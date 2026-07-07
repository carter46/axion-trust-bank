<?php
class HomeController {
    
    public function index() {
        include __DIR__ . '/../views/home/index.php';
    }
    
    public function about() {
        include __DIR__ . '/../views/home/about.php';
    }
    
    public function contact() {
        redirect('/help-center');
    }
    
    public function helpCenter() {
        redirect('/help-center');
    }

    public function accounts() {
        include __DIR__ . '/../views/home/accounts.php';
    }

    public function services() {
        include __DIR__ . '/../views/home/services.php';
    }

    public function cards() {
        include __DIR__ . '/../views/home/cards.php';
    }

    public function investments() {
        include __DIR__ . '/../views/home/investments.php';
    }

    public function loans() {
        include __DIR__ . '/../views/home/loans.php';
    }

    public function charity() {
        include __DIR__ . '/../views/home/charity.php';
    }

    public function security() {
        include __DIR__ . '/../views/home/security.php';
    }

    public function faqs() {
        include __DIR__ . '/../views/home/faqs.php';
    }

    public function terms() {
        include __DIR__ . '/../views/home/terms.php';
    }

    public function investorPortal() {
        include __DIR__ . '/../views/home/investor-portal.php';
    }

    public function partnership() {
        include __DIR__ . '/../views/home/partnership.php';
    }
}
