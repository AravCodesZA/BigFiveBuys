<?php

if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'BigFiveBuys');
}

$page_title = "About Us - " . SITE_NAME;
require_once 'Includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2>About BigFiveBuys</h2>
                </div>
                <div class="card-body">
                    <h3 class="mb-3">Our Story</h3>
                    <p>BigFiveBuys was founded in 2025 with a mission to create a truly South African e-commerce platform that empowers local buyers and sellers. Inspired by the Big Five animals that symbolize our rich heritage, we aim to be the premier destination for secure and trustworthy online transactions.</p>
                    
                    <h3 class="mt-4 mb-3">Our Mission</h3>
                    <p>To provide a secure, locally-owned platform that keeps e-commerce revenue within South Africa while supporting small businesses and individual entrepreneurs across the country.</p>
                    
                    <h3 class="mt-4 mb-3">Why Choose Us?</h3>
                    <ul>
                        <li>100% South African owned and operated</li>
                        <li>Secure transactions with buyer/seller protection</li>
                        <li>Support for local businesses and entrepreneurs</li>
                        <li>User-friendly platform designed for South Africans</li>
                        <li>Dedicated customer support</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'Includes/footer.php'; ?>