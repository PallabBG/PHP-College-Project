<?php

require_once 'includes/functions.php';

if (is_logged_in()) {
    $role = get_user_role();
    redirect("{$role}/dashboard.php");
}

require_once 'includes/header.php';
?>

<style>
    .hero-section {
        text-align: center;
        padding: 5rem 2rem;
        background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
        border-radius: 12px;
        margin-top: 2rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .hero-title {
        font-size: 3rem;
        color: #102a43;
        margin-bottom: 1rem;
        font-weight: bold;
    }
    .hero-text {
        font-size: 1.2rem;
        color: #486581;
        max-width: 700px;
        margin: 0 auto 2.5rem auto;
        line-height: 1.6;
    }
    .btn-custom {
        padding: 0.8rem 2rem;
        font-size: 1.1rem;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.2s ease-in-out;
        display: inline-block;
        font-weight: 600;
    }
    .btn-login {
        background-color: #003e6b;
        color: white;
        border: 2px solid #003e6b;
    }
    .btn-login:hover {
        background-color: #00243f;
        color: white;
    }
    .btn-register {
        background-color: white;
        color: #003e6b;
        border: 2px solid #003e6b;
    }
    .btn-register:hover {
        background-color: #f0f4f8;
    }
    .features-grid {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin-top: 3rem;
        flex-wrap: wrap;
    }
    .feature-card {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        flex: 1;
        min-width: 280px;
        max-width: 350px;
        text-align: center;
        border-top: 4px solid #003e6b;
        transition: transform 0.2s ease;
    }
    .feature-card:hover {
        transform: translateY(-5px);
    }
    .feature-card h3 {
        color: #102a43;
        margin-bottom: 1rem;
        font-size: 1.3rem;
    }
    .feature-card p {
        color: #486581;
        line-height: 1.5;
    }
</style>

<div class="container">
    <div class="hero-section">
        <h1 class="hero-title">Welcome to EduPortal Academy</h1>
        <p class="hero-text">
            An interactive e-learning platform where you can explore courses, watch lectures, submit assignments, and track your progress easily and effectively.
        </p>

        <div style="display: flex; gap: 1.5rem; justify-content: center;">
            <a href="auth/login.php" class="btn-custom btn-login">Login to your account</a>
            <a href="auth/register.php" class="btn-custom btn-register">Register Now</a>
        </div>
    </div>

    <div class="features-grid">
        <div class="feature-card">
            <h3>Learn at your own pace</h3>
            <p>Access lectures, course materials, and quizzes anytime, anywhere.</p>
        </div>
        <div class="feature-card">
            <h3>Track your progress</h3>
            <p>Keep an eye on your course completion and quiz scores automatically.</p>
        </div>
        <div class="feature-card">
            <h3>Interactive Learning</h3>
            <p>Submit assignments and receive feedback directly from instructors.</p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
