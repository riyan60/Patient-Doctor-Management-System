<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/nav.php'; ?>

<head>
    <style>
        :root {
            --bs-bg: #e3f3f6;
            --bs-black: #000 !important;
            --bs-white: #fff !important;
            --bs-teal: #00796b;
            --bs-lteal: #72c5bb;
            --bs-br: 0.3em !important;
            --bs-font: 'Roboto', sans-serif !important;
            --bs-hfont: "Montserrat", sans-serif;
            --bs-gradient: linear-gradient(to right, red, blue) !important;
        }

        section {
            padding: 60px 40px;
            border-radius: var(--bs-br);
            background: white;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .contact-info p {
            margin: 5px 0;
            font-size: 1.1rem;
        }

        .contact-form {
            max-width: 600px;
            margin: auto;
        }

        .contact-form input,
        .contact-form textarea {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 15px;
            border: 1px solid var(--bs-teal);
            border-radius: var(--bs-br);
            font-size: 1rem;
        }

        .map-container {
            margin-top: 40px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2{
            text-align: center;
            margin-bottom: 1.5rem;
        }
    </style>
</head>

<div class="container my-5">
    <!-- Contact Info -->
    <section class="text-center contact-info">
        <h2>Contact Information</h2>
        <p><strong>Email:</strong> <?php echo $company_email; ?></p>
        <p><strong>Phone:</strong> <?php echo $company_phone; ?></p>
        <p><strong>Address:</strong> <?php echo $company_address; ?></p>
    </section>

    <!-- Contact Form -->
    <section>
        <h2>Send Us a Message</h2>
        <form class="contact-form" action="contact_process.php" method="POST">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <input type="text" name="subject" placeholder="Subject" required>
            <textarea name="message" rows="6" placeholder="Your Message" required></textarea>
            <div class="text-center">
                <button type="submit" class="btn custom-btn">Send Message</button>
            </div>
        </form>
    </section>

    <!-- Map -->
    <section>
        <h2>Find Us Here</h2>
        <div class="map-container">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d962.1820601250095!2d73.96846046606841!3d15.282608982909787!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bbfb40e6f8f7a69%3A0x754bfa01fa23da76!2sDeviant%20Studio!5e0!3m2!1sen!2sin!4v1758386804585!5m2!1sen!2sin"
                width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </section>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
