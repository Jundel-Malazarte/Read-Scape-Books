<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Contact Us - ReadScape</title>
        <meta name="description" content="Contact ReadScape - Your Filipino Books E-commerce Platform">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                line-height: 1.6;
                color: #333;
                background-color: #f8f9fa;
                padding: 2rem;
            }

            .contact-container {
                max-width: 1000px;
                margin: 0 auto;
                background-color: #fff;
                padding: 3rem;
                border-radius: 15px;
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            }

            .logo {
                text-align: center;
                margin-bottom: 2rem;
            }

            .logo img {
                width: 120px;
                height: 120px;
                border-radius: 50%;
                box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
            }

            h1 {
                color: #2c3e50;
                font-size: 2.5rem;
                font-weight: 700;
                margin-bottom: 1.5rem;
                text-align: center;
                border-bottom: 3px solid #3498db;
                padding-bottom: 1rem;
            }

            .contact-info {
                background-color: #f8f9fa;
                padding: 2rem;
                border-radius: 10px;
                margin-bottom: 2rem;
            }

            .contact-info h2 {
                color: #34495e;
                font-size: 1.8rem;
                margin-bottom: 1.5rem;
            }

            .contact-info i {
                color: #3498db;
                margin-right: 10px;
                font-size: 1.2rem;
            }

            .contact-form .form-control {
                border: 1px solid #dee2e6;
                border-radius: 8px;
                padding: 0.75rem;
                margin-bottom: 1rem;
            }

            .contact-form .form-control:focus {
                border-color: #3498db;
                box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
            }

            .contact-form label {
                color: #2c3e50;
                font-weight: 500;
                margin-bottom: 0.5rem;
            }

            .btn-primary {
                background-color: #3498db;
                border: none;
                padding: 0.75rem 2rem;
                font-weight: 500;
                transition: all 0.3s ease;
            }

            .btn-primary:hover {
                background-color: #2980b9;
                transform: translateY(-2px);
            }

            .social-links {
                text-align: center;
                margin-top: 2rem;
                padding-top: 2rem;
                border-top: 1px solid #dee2e6;
            }

            .social-links a {
                color: #3498db;
                font-size: 1.5rem;
                margin: 0 1rem;
                transition: all 0.3s ease;
            }

            .social-links a:hover {
                color: #2980b9;
                transform: translateY(-2px);
            }

            @media (max-width: 768px) {
                body {
                    padding: 1rem;
                }

                .contact-container {
                    padding: 1.5rem;
                }

                h1 {
                    font-size: 2rem;
                }
            }
        </style>
    </head>
    <body>
        <div class="contact-container">
            <div class="logo">
                <img src="../../images/Readscape.png" alt="ReadScape Logo">
            </div>

            <h1>Contact Us</h1>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="contact-info">
                        <h2>Get in Touch</h2>
                        <p><i class="fas fa-envelope"></i> Email: jundelmalazarte348@gmail.com</p>
                        <p><i class="fas fa-phone"></i> Phone: +63 912 345 6789</p>
                        <p><i class="fas fa-map-marker-alt"></i> Address: Urdaneta City, Pangasinan, Philippines</p>
                        <p><i class="fas fa-clock"></i> Business Hours: 9:00 AM - 6:00 PM (Monday - Saturday)</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <form class="contact-form">
                        <div class="mb-3">
                            <label for="name" class="form-label">Your Name</label>
                            <input type="text" class="form-control" id="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Send Message
                        </button>
                    </form>
                </div>
            </div>

            <div class="social-links">
                <a href="#" target="_blank"><i class="fab fa-facebook"></i></a>
                <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                <a href="#" target="_blank"><i class="fab fa-linkedin"></i></a>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>