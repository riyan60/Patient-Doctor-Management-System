<?php
include __DIR__ . '/../includes/session.php';

// Check if we have confirmation data
if (!isset($_SESSION['appointment_confirmation'])) {
    header('Location: dashboard.php?section=book-appointment');
    exit;
}

$appointment = $_SESSION['appointment_confirmation'];

// Clear the session data
unset($_SESSION['appointment_confirmation']);
?>

<style>
.confirmation-section {
    padding: 80px 0;
    background: var(--bs-bg);
    min-height: 100vh;
}

.confirmation-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 0 20px;
}

.confirmation-card {
    background: white;
    border-radius: var(--bs-br);
    padding: 40px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    border: 2px solid var(--bs-teal);
}

.confirmation-icon {
    width: 80px;
    height: 80px;
    background: var(--bs-teal);
    border-radius: 50%;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
}

.confirmation-title {
    font-family: var(--bs-hfont);
    font-weight: 700;
    margin-bottom: 20px;
}

.appointment-details {
    background: #f8f9fa;
    border-radius: var(--bs-br);
    padding: 25px;
    margin: 30px 0;
    text-align: left;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #e0e0e0;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: var(--bs-black);
}

.detail-value {
    color: #666;
    font-weight: 500;
}

.appointment-id {
    background: var(--bs-teal);
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 20px;
}

.action-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 30px;
    flex-wrap: wrap;
}

.btn {
    padding: 12px 25px;
    border: none;
    border-radius: var(--bs-br);
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block;
    text-align: center;
    min-width: 150px;
}

.btn-primary {
    background: var(--bs-teal);
    color: white;
}

.btn-primary:hover {
    background: var(--bs-lteal);
    transform: translateY(-2px);
}

.btn-outline-primary {
    background: transparent;
    color: var(--bs-teal);
    border: 2px solid var(--bs-teal);
}

.btn-outline-primary:hover {
    background: var(--bs-teal);
    color: white;
    transform: translateY(-2px);
}

.next-steps {
    background: #e8f5e8;
    border-radius: var(--bs-br);
    padding: 25px;
    margin-top: 30px;
}

.next-steps h4 {
    color: #28a745;
    margin-bottom: 15px;
}

.next-steps ul {
    list-style: none;
    padding: 0;
    text-align: left;
}

.next-steps li {
    padding: 8px 0;
    color: #666;
}

.next-steps i {
    color: #28a745;
    margin-right: 10px;
    width: 20px;
}

@media (max-width: 768px) {
    .confirmation-section {
        padding: 40px 0;
    }

    .confirmation-container {
        padding: 0 15px;
    }

    .confirmation-card {
        padding: 25px;
    }

    .action-buttons {
        flex-direction: column;
        align-items: center;
    }

    .btn {
        width: 100%;
        max-width: 250px;
    }
}
</style>

<section class="confirmation-section">
    <div class="confirmation-container">
        <div class="confirmation-card">
            <div class="confirmation-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>

            <h1 class="confirmation-title">Appointment Confirmed!</h1>

            <div class="appointment-id">
                Appointment ID: <?= htmlspecialchars($appointment['id']) ?>
            </div>

            <p style="color: #666; margin-bottom: 30px;">
                Your appointment has been successfully booked. A confirmation email has been sent to your email address.
            </p>

            <div class="appointment-details">
                <h3 style="margin-bottom: 20px; color: var(--bs-black); text-align: center;">
                    <i class="bi bi-calendar-event me-2"></i>Appointment Details
                </h3>

                <div class="detail-row">
                    <span class="detail-label">Patient Name:</span>
                    <span class="detail-value"><?= htmlspecialchars($appointment['name']) ?></span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Doctor:</span>
                    <span class="detail-value"><?= htmlspecialchars($appointment['doctor']) ?></span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Date:</span>
                    <span class="detail-value">
                        <?= date('l, F j, Y', strtotime($appointment['date'])) ?>
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Time:</span>
                    <span class="detail-value">
                        <?= date('g:i A', strtotime($appointment['time'])) ?>
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Type:</span>
                    <span class="detail-value">
                        <?= ucwords(str_replace('-', ' ', $appointment['type'])) ?>
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value" style="font-size: 0.9rem;">
                        <?= htmlspecialchars($appointment['email']) ?>
                    </span>
                </div>
            </div>

            <div class="next-steps">
                <h4><i class="bi bi-info-circle me-2"></i>What happens next?</h4>
                <ul>
                    <li><i class="bi bi-envelope-check"></i>You will receive a confirmation email with all details</li>
                    <li><i class="bi bi-bell"></i>You'll get a reminder 24 hours before your appointment</li>
                    <li><i class="bi bi-calendar"></i>Your appointment is now saved in our system</li>
                    <li><i class="bi bi-telephone"></i>Our staff may call you to confirm the appointment</li>
                </ul>
            </div>

            <div class="action-buttons">
                <a href="../index.php" class="btn btn-primary">
                    <i class="bi bi-house me-2"></i>Back to Home
                </a>
                <a href="dashboard.php?section=book-appointment" class="btn btn-outline-primary">
                    <i class="bi bi-calendar-plus me-2"></i>Book Another
                </a>
            </div>
        </div>
    </div>
</section>