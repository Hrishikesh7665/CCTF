<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/template/head.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/common/variables.php');
?>

<body class="spin-lock">

    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <!-- Menu -->
            <?php
            require_once($_SERVER['DOCUMENT_ROOT'] . '/template/side-menu.php');
            ?>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">

                <!-- Navbar -->
                <?php
                require_once($_SERVER['DOCUMENT_ROOT'] . '/template/navbar.php');
                ?>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                <?php require_once $_SERVER["DOCUMENT_ROOT"] . "/template/loadingSpinner.php"; ?>
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="faq-header d-flex flex-column justify-content-center align-items-center pb-3">
                            <h3 class="text-center text-primary mb-2">Frequently Asked Questions</h3>
                            <p class="text-body text-center mb-0 px-3">for CDAC-K Capture the Flag Security Challenge</p>
                        </div>

                        <div class="row mt-4">
                            <!-- Navigation -->
                            <div class="col-lg-3 col-md-4 col-12 mb-md-0 mb-3">
                                <div class="d-flex justify-content-between flex-column mb-2 mb-md-0">
                                    <ul class="nav nav-align-left nav-pills flex-column flex-nowrap">
                                        <li class="nav-item">
                                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#general">
                                                <i class="mdi mdi-flag-checkered me-2"></i>
                                                <span class="align-middle">General</span>
                                            </button>
                                        </li>
                                        <li class="nav-item">
                                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#scoring">
                                                <i class="mdi mdi-scoreboard-outline me-2"></i>
                                                <span class="align-middle">Scoring</span>
                                            </button>
                                        </li>
                                        <li class="nav-item">
                                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#help">
                                                <i class="mdi mdi-help-circle-outline me-2"></i>
                                                <span class="align-middle">Getting Help</span>
                                            </button>
                                        </li>
                                        <li class="nav-item">
                                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#certificate">
                                                <i class="mdi mdi-certificate-outline me-2"></i>
                                                <span class="align-middle">Certificate</span>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <!-- /Navigation -->

                            <!-- FAQ's -->
                            <div class="col-lg-9 col-md-8 col-12">
                                <div class="tab-content p-0">
                                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                                        <div class="d-flex mb-3 gap-3">
                                            <div class="avatar avatar-md">
                                                <div class="avatar-initial bg-label-primary rounded">
                                                    <i class="mdi mdi-flag-checkered mdi-24px"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h5 class="mb-1">
                                                    <span class="align-middle">General</span>
                                                </h5>
                                                <span>General Frequently Asked Questions (FAQ's)</span>
                                            </div>
                                        </div>
                                        <div id="accordionGeneral" class="accordion">
                                            <div class="accordion-item active">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" aria-expanded="true" data-bs-target="#accordionGeneral-1" aria-controls="accordionGeneral-1">
                                                        What is a Capture the Flag (CTF) competition?
                                                    </button>
                                                </h2>

                                                <div id="accordionGeneral-1" class="accordion-collapse collapse show">
                                                    <div class="accordion-body">
                                                        A CTF is a cybersecurity competition where participants compete by solving challenges that involve finding and exploiting vulnerabilities in simulated systems. The goal is to be the first team (or individual) to "capture the flag," which is usually a hidden piece of text or data.
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordionGeneral-2" aria-controls="accordionGeneral-2">
                                                        What are the different types of challenges?
                                                    </button>
                                                </h2>
                                                <div id="accordionGeneral-2" class="accordion-collapse collapse">
                                                    <div class="accordion-body">
                                                        CTF challenges come in many forms, but some common categories include: * Web: Finding vulnerabilities in web applications. * Crypto: Decoding messages or breaking ciphers. * Forensics: Analysing data to uncover hidden information. * Steganography: Finding hidden messages within images or files. * Binary Exploitation: Exploiting vulnerabilities in software code. * Miscellaneous: Challenges that don't fit neatly into any other category.
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordionGeneral-3" aria-controls="accordionGeneral-3">
                                                        How do I find the challenges?
                                                    </button>
                                                </h2>
                                                <div id="accordionGeneral-3" class="accordion-collapse collapse">
                                                    <div class="accordion-body">
                                                        Challenges will be available in the dashboard section which is also the home page after login. One can access the dashboard by clicking on the 4 squares on the top right corner of the website
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="scoring" role="tabpanel">
                                        <div class="d-flex mb-3 gap-3">
                                            <div class="avatar avatar-md">
                                                <span class="avatar-initial bg-label-primary rounded">
                                                    <i class="mdi mdi-scoreboard-outline mdi-24px"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h5 class="mb-1">
                                                    <span class="align-middle">Scoring</span>
                                                </h5>
                                                <span>Scoring Frequently Asked Questions (FAQ's)</span>
                                            </div>
                                        </div>
                                        <div id="accordionScoring" class="accordion">
                                            <div class="accordion-item active">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" aria-expanded="true" data-bs-target="#accordionScoring-1" aria-controls="accordionScoring-1">
                                                        How are points awarded?
                                                    </button>
                                                </h2>

                                                <div id="accordionScoring-1" class="accordion-collapse collapse show">
                                                    <div class="accordion-body">
                                                        Points are typically awarded based on the difficulty of the challenge. Harder challenges will be worth more points. The Points for a particular challenge will be displayed in the question card.
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordionScoring-2" aria-controls="accordionScoring-2">
                                                        Is there a time limit?
                                                    </button>
                                                </h2>
                                                <div id="accordionScoring-2" class="accordion-collapse collapse">
                                                    <div class="accordion-body">Yes, the competition will have a set time limit. The remaining time will be displayed in the left panel. Make sure to manage your time effectively to solve as many challenges as possible.
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordionScoring-4" aria-controls="accordionScoring-4">
                                                        What is the Scoreboard for?
                                                    </button>
                                                </h2>
                                                <div id="accordionScoring-4" class="accordion-collapse collapse">
                                                    <div class="accordion-body">
                                                        The Scoreboard is a leader board which shows the current ranking of participants based on their total score. The Scoreboard can be accessed by clicking on the 4 squares on the top right corner of the website.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="help" role="tabpanel">
                                        <div class="d-flex mb-3 gap-3">
                                            <div class="avatar avatar-md">
                                                <span class="avatar-initial bg-label-primary rounded">
                                                    <i class="mdi mdi-help-circle-outline mdi-24px"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h5 class="mb-1"><span class="align-middle">Getting Help</span></h5>
                                                <span>Getting Help Frequently Asked Questions (FAQ's)</span>
                                            </div>
                                        </div>
                                        <div id="accordionHelp" class="accordion">
                                            <div class="accordion-item active">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" aria-expanded="true" data-bs-target="#accordionHelp-1" aria-controls="accordionHelp-1">
                                                        Can I work with others?
                                                    </button>
                                                </h2>

                                                <div id="accordionHelp-1" class="accordion-collapse collapse show">
                                                    <div class="accordion-body">
                                                        No. This is a completely an individual challenge and discussion about the steps to solve the challenge is strictly prohibited.
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordionHelp-2" aria-controls="accordionHelp-2">
                                                        I'm stuck on a challenge. What should I do?
                                                    </button>
                                                </h2>
                                                <div id="accordionHelp-2" class="accordion-collapse collapse">
                                                    <div class="accordion-body">
                                                        Don't be discouraged! CTF challenges are designed to be difficult. Here are some tips:
                                                        <ul>
                                                            <li>Take a short break.</li>
                                                            <li>Search online forums or communities for similar challenges and solutions (be careful not to spoil the challenge for yourself!).</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" aria-controls="accordionHelp-3" data-bs-target="#accordionHelp-3">
                                                        Is there any technical support available?
                                                    </button>
                                                </h2>
                                                <div id="accordionHelp-3" class="accordion-collapse collapse">
                                                    <div class="accordion-body">
                                                        For any technical issues with the competition platform, you can contact us at “<a href="mailto:iss-kol@cdac.in">iss-kol@cdac.in</a>”
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="certificate" role="tabpanel">
                                        <div class="d-flex mb-3 gap-3">
                                            <div class="avatar avatar-md">
                                                <span class="avatar-initial bg-label-primary rounded">
                                                    <i class="mdi mdi-certificate-outline mdi-24px"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h5 class="mb-1">
                                                    <span class="align-middle">Certificate</span>
                                                </h5>
                                                <span>Certificate Frequently Asked Questions (FAQ's)</span>
                                            </div>
                                        </div>
                                        <div id="accordionCertificate" class="accordion">
                                            <div class="accordion-item active">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" aria-expanded="true" data-bs-target="#accordionCertificate-1" aria-controls="accordionCertificate-1">
                                                        When will I get the certificate?
                                                    </button>
                                                </h2>

                                                <div id="accordionCertificate-1" class="accordion-collapse collapse show">
                                                    <div class="accordion-body">
                                                        Your Participation certificate will auto generate after the competition has ended. The participation certificate will only generate if you have completed the demo challenge.
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordionCertificate-2" aria-controls="accordionCertificate-2">
                                                        I cannot see my certificate. What to do?
                                                    </button>
                                                </h2>
                                                <div id="accordionCertificate-2" class="accordion-collapse collapse">
                                                    <div class="accordion-body">
                                                        There can be two situations in which your certificate may not have been generated.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /FAQ's -->
                        </div>

                        <!-- Contact -->
                        <!-- <div class="row mt-5"> -->
                        <!-- <div class="col-12 text-center mb-4"> -->
                        <!-- <div class="badge bg-label-primary rounded-pill">Question?</div> -->
                        <!-- <h5 class="my-3">You still have a question?</h5> -->
                        <!-- <p>If you can't find question in our FAQ, you can contact us. We'll answer you shortly!</p> -->
                        <!-- </div> -->
                        <!-- </div> -->
                        <!-- <div class="row justify-content-center gap-sm-0 gap-3"> -->
                        <!-- <div class="col-sm-6"> -->
                        <!-- <div class="py-4 rounded bg-faq-section d-flex align-items-center flex-column"> -->
                        <!-- <div class="avatar avatar-md mt-2"> -->
                        <!-- <span class="avatar-initial bg-label-primary rounded"> -->
                        <!-- <i class="mdi mdi-phone mdi-24px"></i> -->
                        <!-- </span> -->
                        <!-- </div> -->
                        <!-- <h4 class="mt-3"><a class="text-heading" href="tel:+(810)25482568">+ (810) 2548 2568</a></h4> -->
                        <!-- <p class="mb-2">We are always happy to help</p> -->
                        <!-- </div> -->
                        <!-- </div> -->
                        <!-- <div class="col-sm-6"> -->
                        <!-- <div class="py-4 rounded bg-faq-section d-flex align-items-center flex-column"> -->
                        <!-- <div class="avatar avatar-md mt-2"> -->
                        <!-- <span class="avatar-initial bg-label-primary rounded"> -->
                        <!-- <i class="mdi mdi-email-outline mdi-24px"></i> -->
                        <!-- </span> -->
                        <!-- </div> -->
                        <!-- <h4 class="mt-3"><a class="text-heading" href="mailto:help@help.com">help@help.com</a></h4> -->
                        <!-- <p class="mb-2">Best way to get a quick answer</p> -->
                        <!-- </div> -->
                        <!-- </div> -->
                        <!-- </div> -->
                        <!-- /Contact -->

                    </div>

                    <!-- toast & modal-->
                    <?php
                    require_once($_SERVER['DOCUMENT_ROOT'] . '/template/toast.php');
                    ?>

                </div>
                				<!-- / Content -->

				<!-- Footer -->
				<?php
                require_once($_SERVER['DOCUMENT_ROOT'] . '/template/footer.php');
                ?>
				<!-- / Footer -->

                <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>

    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>

    </div>
    <!-- / Layout wrapper -->


    <!-- scripts -->

    <?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/template/scripts-section.php');
    ?>
    <!-- / scripts -->

</body>

</html>